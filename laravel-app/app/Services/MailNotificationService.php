<?php

namespace App\Services;

use App\Models\EmailLog;
use App\Models\Feedback;
use App\Models\Repair;
use App\Models\SmtpSetting;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\EsmtpTransportFactory;

class MailNotificationService
{
    /**
     * Configure mailer dynamically from SMTP settings in database
     */
    public static function configureMailer(): SmtpSetting
    {
        $setting = SmtpSetting::current();

        if ($setting->mailer === 'smtp' && !empty($setting->host)) {
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.transport', 'smtp');
            Config::set('mail.mailers.smtp.host', $setting->host);
            Config::set('mail.mailers.smtp.port', $setting->port);
            Config::set('mail.mailers.smtp.encryption', $setting->encryption ?: null);
            Config::set('mail.mailers.smtp.username', $setting->username);
            Config::set('mail.mailers.smtp.password', $setting->password);
            Config::set('mail.mailers.smtp.timeout', 15);
        } else {
            Config::set('mail.default', 'log');
        }

        Config::set('mail.from.address', $setting->from_address ?: 'support@fixsmith.com');
        Config::set('mail.from.name', $setting->from_name ?: 'Fixsmith PC Repair');

        return $setting;
    }

    /**
     * Send email helper with automatic logging
     */
    public static function send(string $toEmail, ?string $toName, string $subject, string $htmlContent, string $eventType = 'general'): array
    {
        $setting = self::configureMailer();

        $preview = strip_tags(mb_substr($htmlContent, 0, 300));
        $status = 'sent';
        $errorMessage = null;

        try {
            Mail::html($htmlContent, function ($message) use ($toEmail, $toName, $subject, $setting) {
                $message->to($toEmail, $toName)
                    ->subject($subject)
                    ->from($setting->from_address, $setting->from_name);
            });

            if ($setting->mailer === 'log') {
                $status = 'logged';
            }
        } catch (\Throwable $e) {
            $status = 'failed';
            $errorMessage = $e->getMessage();
            Log::error("SMTP Mail Send Failed to {$toEmail}: " . $e->getMessage());
        }

        $log = EmailLog::create([
            'to_email' => $toEmail,
            'to_name' => $toName,
            'subject' => $subject,
            'body_preview' => $preview,
            'status' => $status,
            'error_message' => $errorMessage,
            'event_type' => $eventType,
        ]);

        return [
            'success' => ($status === 'sent' || $status === 'logged'),
            'status' => $status,
            'error' => $errorMessage,
            'log' => $log,
        ];
    }

    /**
     * 1. Notification: New Repair Created
     */
    public static function notifyRepairCreated(Repair $repair): void
    {
        // 1A. Email to Customer
        $customerSubject = "Repair Request Received: {$repair->id} - {$repair->device}";
        $deliveryDetails = $repair->method === 'Mail-in'
            ? "<p><strong>Service Type: Mail-in Service</strong><br>Please ship your package with tracking to: <br><code>Fixsmith Repair Studio, 84 Mercer Street, Brooklyn, NY 11201</code>.</p>"
            : "<p><strong>Service Type: Drop-off Service</strong><br>Please bring your device to our workshop at 84 Mercer Street, Brooklyn, NY (Mon–Sat, 9:00 AM–6:00 PM).</p>";

        $customerHtml = self::wrapEmailTemplate(
            title: "Repair Request Confirmed",
            badge: $repair->id,
            headline: "We've received your repair request for {$repair->device}",
            body: "
                <p>Hello <strong>{$repair->customer_name}</strong>,</p>
                <p>Thank you for choosing Fixsmith! We have logged your repair ticket and our team is preparing to receive and diagnose your system.</p>
                <div style='background: #f4f6f3; border-radius: 10px; padding: 15px; margin: 15px 0;'>
                    <p style='margin:0 0 5px;'><strong>Repair Ticket:</strong> {$repair->id}</p>
                    <p style='margin:0 0 5px;'><strong>Device:</strong> {$repair->device}</p>
                    <p style='margin:0 0 5px;'><strong>Serial Number:</strong> {$repair->serial}</p>
                    <p style='margin:0 0 5px;'><strong>Reported Issue:</strong> {$repair->issue}</p>
                    <p style='margin:0;'><strong>Current Status:</strong> <span style='display:inline-block; padding: 3px 8px; background: #dceee4; color: #174c36; border-radius: 12px; font-weight: bold;'>{$repair->status}</span></p>
                </div>
                {$deliveryDetails}
                <p>You can track every diagnostic phase and message your technician directly in your Fixsmith customer portal.</p>
            "
        );

        self::send($repair->customer_email, $repair->customer_name, $customerSubject, $customerHtml, 'repair_created');

        // 1B. Alert to Workshop Admin
        $admin = User::where('role', 'admin')->first();
        $adminEmail = $admin ? $admin->email : 'emma.walsh@fixsmith.com';
        $adminHtml = self::wrapEmailTemplate(
            title: "New Repair Request in Queue",
            badge: "Dispatch Alert",
            headline: "New {$repair->method} repair submitted: {$repair->id}",
            body: "
                <p>A new repair request has arrived and is waiting in the dispatch queue:</p>
                <div style='background: #f4f6f3; border-radius: 10px; padding: 15px; margin: 15px 0;'>
                    <p style='margin:0 0 5px;'><strong>Ticket:</strong> {$repair->id}</p>
                    <p style='margin:0 0 5px;'><strong>Customer:</strong> {$repair->customer_name} ({$repair->customer_email})</p>
                    <p style='margin:0 0 5px;'><strong>Device:</strong> {$repair->device} (Serial: {$repair->serial})</p>
                    <p style='margin:0 0 5px;'><strong>Method:</strong> {$repair->method}</p>
                    <p style='margin:0;'><strong>Issue:</strong> {$repair->issue}</p>
                </div>
                <p>Please review and assign a technician from the Admin Operations dashboard.</p>
            "
        );
        self::send($adminEmail, 'Workshop Admin', "New Request {$repair->id}: {$repair->device}", $adminHtml, 'admin_alert');
    }

    /**
     * 2. Notification: Repair Status & Workshop Note Updated
     */
    public static function notifyStatusUpdated(Repair $repair, string $oldStatus, string $newStatus, ?string $note): void
    {
        $subject = "Update on your repair {$repair->id}: {$newStatus}";
        $noteBlock = $note ? "<div style='background: #f3f8f5; border-left: 4px solid #174c36; padding: 12px 15px; margin: 15px 0; border-radius: 4px;'><strong style='color:#174c36;'>Technician Note:</strong><p style='margin: 5px 0 0; color: #444;'>{$note}</p></div>" : "";

        $html = self::wrapEmailTemplate(
            title: "Repair Status Progress",
            badge: $newStatus,
            headline: "Your repair status is now: {$newStatus}",
            body: "
                <p>Hello <strong>{$repair->customer_name}</strong>,</p>
                <p>Here is the latest progress update for your <strong>{$repair->device}</strong> (Ticket #<strong>{$repair->id}</strong>).</p>
                <div style='background: #f4f6f3; border-radius: 10px; padding: 15px; margin: 15px 0;'>
                    <p style='margin:0 0 5px;'><strong>Previous Status:</strong> {$oldStatus}</p>
                    <p style='margin:0 0 5px;'><strong>New Status:</strong> <span style='display:inline-block; padding: 3px 8px; background: #dceee4; color: #174c36; border-radius: 12px; font-weight: bold;'>{$newStatus}</span></p>
                    <p style='margin:0;'><strong>Technician:</strong> " . ($repair->technician_name ?: 'Workshop Team') . "</p>
                </div>
                {$noteBlock}
                <p>Log in to your account anytime to view detailed benchmarks, photos, or timeline milestones.</p>
            "
        );

        self::send($repair->customer_email, $repair->customer_name, $subject, $html, 'status_updated');
    }

    /**
     * 3. Notification: Repair Assigned to Technician
     */
    public static function notifyRepairAssigned(Repair $repair, User $technician, ?string $previousTechName = null): void
    {
        $isTransfer = !empty($previousTechName) && $previousTechName !== 'Unassigned' && $previousTechName !== $technician->name;
        $subject = $isTransfer
            ? "Workload Transfer: {$repair->id} ({$repair->device}) assigned to your bench"
            : "New Repair Assigned: {$repair->id} - {$repair->device}";
        $headline = $isTransfer
            ? "Repair {$repair->id} has been transferred to your queue"
            : "Repair {$repair->id} has been assigned to you";
        $transferNotice = $isTransfer
            ? "<p style='color: #d77232; font-weight: bold;'>Transferred from: {$previousTechName}</p>"
            : "";

        $html = self::wrapEmailTemplate(
            title: "Technician Assignment",
            badge: $isTransfer ? "Transferred Ticket" : $repair->id,
            headline: $headline,
            body: "
                <p>Hello <strong>{$technician->name}</strong>,</p>
                <p>A repair job has been moved to your active bench queue by the workshop administrator.</p>
                {$transferNotice}
                <div style='background: #f4f6f3; border-radius: 10px; padding: 15px; margin: 15px 0;'>
                    <p style='margin:0 0 5px;'><strong>Repair ID:</strong> {$repair->id}</p>
                    <p style='margin:0 0 5px;'><strong>Customer:</strong> {$repair->customer_name}</p>
                    <p style='margin:0 0 5px;'><strong>Device:</strong> {$repair->device} (Serial: {$repair->serial})</p>
                    <p style='margin:0 0 5px;'><strong>Delivery Method:</strong> {$repair->method}</p>
                    <p style='margin:0;'><strong>Reported Issue:</strong> {$repair->issue}</p>
                </div>
                <p>Please inspect the system in your technician workspace when the unit is available.</p>
            "
        );

        self::send($technician->email, $technician->name, $subject, $html, 'repair_assigned');
    }

    /**
     * Notification to previous technician when a job is moved away
     */
    public static function notifyRepairTransferredAway(Repair $repair, User $previousTechnician, string $newTechName): void
    {
        $subject = "Queue Update: Repair {$repair->id} transferred to {$newTechName}";
        $html = self::wrapEmailTemplate(
            title: "Queue Rebalancing",
            badge: "Reassigned",
            headline: "Repair {$repair->id} was moved to {$newTechName}",
            body: "
                <p>Hello <strong>{$previousTechnician->name}</strong>,</p>
                <p>Please be advised that workshop administration has transferred repair ticket <strong>{$repair->id}</strong> ({$repair->device}) from your bench to <strong>{$newTechName}</strong>.</p>
                <div style='background: #f4f6f3; border-radius: 10px; padding: 15px; margin: 15px 0;'>
                    <p style='margin:0 0 5px;'><strong>Repair Ticket:</strong> {$repair->id}</p>
                    <p style='margin:0 0 5px;'><strong>Device:</strong> {$repair->device}</p>
                    <p style='margin:0;'><strong>Transferred To:</strong> {$newTechName}</p>
                </div>
                <p>This job has been automatically cleared from your active queue.</p>
            "
        );

        self::send($previousTechnician->email, $previousTechnician->name, $subject, $html, 'repair_transferred');
    }

    /**
     * 4. Notification: Feedback or Complaint Submitted
     */
    public static function notifyFeedbackSubmitted(Feedback $feedback): void
    {
        $admin = User::where('role', 'admin')->first();
        $adminEmail = $admin ? $admin->email : 'emma.walsh@fixsmith.com';

        $isComplaint = $feedback->type === 'Complaint';
        $badge = $isComplaint ? 'Customer Complaint' : "{$feedback->rating}-Star Rating";
        $subject = "New {$feedback->type}: {$feedback->id} from {$feedback->customer_name}";

        $html = self::wrapEmailTemplate(
            title: "Customer Care Alert",
            badge: $badge,
            headline: "{$feedback->type} submission #{$feedback->id}",
            body: "
                <p>A customer has submitted feedback regarding repair case <strong>" . ($feedback->repair_id ?: 'General') . "</strong>.</p>
                <div style='background: #f4f6f3; border-radius: 10px; padding: 15px; margin: 15px 0;'>
                    <p style='margin:0 0 5px;'><strong>Customer:</strong> {$feedback->customer_name} ({$feedback->customer_email})</p>
                    <p style='margin:0 0 5px;'><strong>Type:</strong> {$feedback->type}</p>
                    <p style='margin:0 0 5px;'><strong>Subject:</strong> {$feedback->subject}</p>
                    " . ($feedback->rating > 0 ? "<p style='margin:0 0 5px;'><strong>Rating:</strong> {$feedback->rating} / 5 Stars</p>" : "") . "
                    <p style='margin:0;'><strong>Message:</strong><br>{$feedback->message}</p>
                </div>
                " . ($isComplaint ? "<p style='color: #ad4f4a;'><strong>Action Required:</strong> Please review and send a reply within 1 business day.</p>" : "<p>Feedback has been saved to the workshop records.</p>") . "
            "
        );

        self::send($adminEmail, 'Workshop Admin', $subject, $html, 'feedback_received');
    }

    /**
     * 5. Notification: Complaint Reply to Customer
     */
    public static function notifyComplaintReply(Feedback $feedback, string $reply): void
    {
        $subject = "Response to your inquiry [{$feedback->id}]: {$feedback->subject}";
        $html = self::wrapEmailTemplate(
            title: "Fixsmith Customer Care",
            badge: $feedback->id,
            headline: "Update regarding reference {$feedback->id}",
            body: "
                <p>Hello <strong>{$feedback->customer_name}</strong>,</p>
                <p>Thank you for your patience. A workshop manager has reviewed your inquiry regarding <strong>{$feedback->subject}</strong>.</p>
                <div style='background: #f3f8f5; border-left: 4px solid #174c36; padding: 15px; margin: 15px 0; border-radius: 4px;'>
                    <strong style='color:#174c36;'>Fixsmith Support Response:</strong>
                    <p style='margin: 8px 0 0; color: #222; font-size: 14px; line-height: 1.6;'>{$reply}</p>
                </div>
                <p>If you have any further questions or require immediate assistance, please reply to this email or call our workshop directly at <strong>+1 (202) 555-0148</strong>.</p>
            "
        );

        self::send($feedback->customer_email, $feedback->customer_name, $subject, $html, 'complaint_reply');
    }

    /**
     * 6. Test SMTP Mail
     */
    public static function sendTestEmail(string $recipientEmail, ?string $customMessage = null): array
    {
        $setting = SmtpSetting::current();
        $messageBody = $customMessage ?: "This is a live test email sent from your Fixsmith PC Repair Studio Laravel application via SMTP ({$setting->host}:{$setting->port}).";

        $html = self::wrapEmailTemplate(
            title: "SMTP Connection Test",
            badge: "SMTP Verified",
            headline: "Your SMTP Configuration is Working!",
            body: "
                <p>Congratulations! Your Laravel application successfully connected to the SMTP server and dispatched this notification.</p>
                <div style='background: #f4f6f3; border-radius: 10px; padding: 15px; margin: 15px 0;'>
                    <p style='margin:0 0 5px;'><strong>SMTP Host:</strong> {$setting->host}</p>
                    <p style='margin:0 0 5px;'><strong>SMTP Port:</strong> {$setting->port}</p>
                    <p style='margin:0 0 5px;'><strong>Encryption:</strong> " . ($setting->encryption ?: 'None') . "</p>
                    <p style='margin:0 0 5px;'><strong>From Address:</strong> {$setting->from_address}</p>
                    <p style='margin:0;'><strong>Timestamp:</strong> " . date('Y-m-d H:i:s T') . "</p>
                </div>
                <p>{$messageBody}</p>
            "
        );

        return self::send($recipientEmail, 'Test Recipient', "Fixsmith SMTP Test - " . date('H:i:s'), $html, 'test_email');
    }

    /**
     * Reusable clean branded HTML email wrapper
     */
    private static function wrapEmailTemplate(string $title, string $badge, string $headline, string $body): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$title}</title>
            <style>
                body { margin: 0; padding: 0; background: #f4f6f3; font-family: 'Segoe UI', Helvetica, Arial, sans-serif; color: #17211b; }
                .email-container { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e3e8e4; box-shadow: 0 10px 30px rgba(24,52,38,0.06); }
                .email-header { background: #103b2a; color: #ffffff; padding: 28px 32px; }
                .email-brand { font-size: 20px; font-weight: 800; letter-spacing: -0.5px; }
                .email-badge { display: inline-block; font-size: 11px; text-transform: uppercase; font-weight: 800; letter-spacing: 0.1em; background: rgba(255,255,255,0.12); color: #b8ddc8; padding: 4px 10px; border-radius: 20px; margin-top: 10px; }
                .email-content { padding: 32px; font-size: 14px; line-height: 1.65; color: #333333; }
                .email-content h1 { font-size: 22px; font-weight: 800; color: #17211b; margin: 0 0 16px; line-height: 1.3; }
                .email-footer { background: #f8faf8; padding: 20px 32px; border-top: 1px solid #e3e8e4; font-size: 12px; color: #68736c; text-align: center; }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='email-header'>
                    <div class='email-brand'>Fixsmith &middot; PC Repair Studio</div>
                    <div class='email-badge'>{$badge}</div>
                </div>
                <div class='email-content'>
                    <h1>{$headline}</h1>
                    {$body}
                </div>
                <div class='email-footer'>
                    &copy; " . date('Y') . " Fixsmith Repair Studio &middot; 84 Mercer Street, Brooklyn, NY &middot; +1 (202) 555-0148<br>
                    Live status, diagnostics, and notifications enabled.
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
