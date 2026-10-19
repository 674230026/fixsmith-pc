<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use App\Models\SmtpSetting;
use App\Services\MailNotificationService;
use Illuminate\Http\Request;

class SmtpController extends Controller
{
    public function getSettings()
    {
        $setting = SmtpSetting::current();
        $logs = EmailLog::orderBy('created_at', 'desc')->take(20)->get();

        return response()->json([
            'setting' => [
                'id' => $setting->id,
                'mailer' => $setting->mailer,
                'host' => $setting->host,
                'port' => $setting->port,
                'username' => $setting->username,
                'encryption' => $setting->encryption,
                'from_address' => $setting->from_address,
                'from_name' => $setting->from_name,
                'is_enabled' => $setting->is_enabled,
                'has_password' => !empty($setting->password),
            ],
            'logs' => $logs,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'mailer' => 'required|in:smtp,log',
            'host' => 'nullable|string',
            'port' => 'required|integer',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'encryption' => 'nullable|string',
            'from_address' => 'required|email',
            'from_name' => 'required|string',
        ]);

        $setting = SmtpSetting::current();
        $setting->mailer = $request->mailer;
        $setting->host = $request->host;
        $setting->port = (int)$request->port;
        $setting->username = $request->username;
        if ($request->has('password') && $request->password !== '') {
            $setting->password = $request->password;
        }
        $setting->encryption = $request->encryption;
        $setting->from_address = $request->from_address;
        $setting->from_name = $request->from_name;
        $setting->save();

        return response()->json([
            'success' => true,
            'message' => 'SMTP settings saved successfully.',
            'setting' => $setting,
        ]);
    }

    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'recipient_email' => 'required|email',
            'custom_message' => 'nullable|string',
        ]);

        $result = MailNotificationService::sendTestEmail(
            $request->recipient_email,
            $request->custom_message
        );

        $logs = EmailLog::orderBy('created_at', 'desc')->take(10)->get();

        return response()->json([
            'success' => $result['success'],
            'status' => $result['status'],
            'error' => $result['error'],
            'message' => $result['success']
                ? ($result['status'] === 'logged' ? 'Test email logged to storage/logs/laravel.log (Mailer set to log).' : "Test email sent successfully to {$request->recipient_email} via SMTP!")
                : "SMTP Delivery Failed: " . ($result['error'] ?? 'Unknown error'),
            'logs' => $logs,
        ]);
    }

    public function getLogs()
    {
        $logs = EmailLog::orderBy('created_at', 'desc')->take(50)->get();
        return response()->json($logs);
    }
}
