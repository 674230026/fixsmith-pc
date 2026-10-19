<?php

namespace Database\Seeders;

use App\Models\EmailLog;
use App\Models\Feedback;
use App\Models\Notification;
use App\Models\Repair;
use App\Models\SmtpSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default SMTP Setting
        SmtpSetting::firstOrCreate(['id' => 1], [
            'mailer' => 'log',
            'host' => 'smtp.mailtrap.io',
            'port' => 587,
            'username' => '',
            'password' => '',
            'encryption' => 'tls',
            'from_address' => 'support@fixsmith.com',
            'from_name' => 'Fixsmith Repair Studio',
            'is_enabled' => true,
        ]);

        // 2. Create Users
        // Admins
        $adminEmma = User::firstOrCreate(['email' => 'emma.walsh@fixsmith.com'], [
            'name' => 'Emma Walsh',
            'phone' => '+1 (202) 555-0114',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'avatar_initials' => 'EW',
            'active' => true,
            'technician_capacity' => 0,
            'technician_title' => 'Workshop Administrator',
            'bio_meta' => 'Owner · Administrator',
        ]);

        $adminDaniel = User::firstOrCreate(['email' => 'daniel.cho@fixsmith.com'], [
            'name' => 'Daniel Cho',
            'phone' => '+1 (202) 555-0128',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'avatar_initials' => 'DC',
            'active' => true,
            'technician_capacity' => 0,
            'technician_title' => 'Workshop Manager',
            'bio_meta' => 'Manager · Administrator',
        ]);

        // Technicians
        $techAlex = User::firstOrCreate(['email' => 'alex.kim@fixsmith.com'], [
            'name' => 'Alex Kim',
            'phone' => '+1 (202) 555-0166',
            'password' => Hash::make('password123'),
            'role' => 'technician',
            'avatar_initials' => 'AK',
            'active' => true,
            'technician_capacity' => 3,
            'technician_title' => 'Senior technician',
            'bio_meta' => '7 active',
        ]);

        $techSam = User::firstOrCreate(['email' => 'sam.rivera@fixsmith.com'], [
            'name' => 'Sam Rivera',
            'phone' => '+1 (202) 555-0199',
            'password' => Hash::make('password123'),
            'role' => 'technician',
            'avatar_initials' => 'SR',
            'active' => true,
            'technician_capacity' => 3,
            'technician_title' => 'Hardware specialist',
            'bio_meta' => '5 active',
        ]);

        $techPriya = User::firstOrCreate(['email' => 'priya.shah@fixsmith.com'], [
            'name' => 'Priya Shah',
            'phone' => '+1 (202) 555-0177',
            'password' => Hash::make('password123'),
            'role' => 'technician',
            'avatar_initials' => 'PS',
            'active' => true,
            'technician_capacity' => 3,
            'technician_title' => 'Repair technician',
            'bio_meta' => '4 active',
        ]);

        $techLeo = User::firstOrCreate(['email' => 'leo.grant@fixsmith.com'], [
            'name' => 'Leo Grant',
            'phone' => '+1 (202) 555-0144',
            'password' => Hash::make('password123'),
            'role' => 'technician',
            'avatar_initials' => 'LG',
            'active' => true,
            'technician_capacity' => 3,
            'technician_title' => 'Repair technician',
            'bio_meta' => 'Available',
        ]);

        // Customers
        $customerMaya = User::firstOrCreate(['email' => 'maya.chen@example.com'], [
            'name' => 'Maya Chen',
            'phone' => '+1 (202) 555-0182',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'avatar_initials' => 'MC',
            'active' => true,
            'bio_meta' => 'Customer since Aug 2023 · 3 repairs',
        ]);

        $customerJordan = User::firstOrCreate(['email' => 'jordan.blake@example.com'], [
            'name' => 'Jordan Blake',
            'phone' => '+1 (202) 555-0133',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'avatar_initials' => 'JB',
            'active' => true,
            'bio_meta' => '2 repairs',
        ]);

        $customerNoah = User::firstOrCreate(['email' => 'noah.wilson@example.com'], [
            'name' => 'Noah Wilson',
            'phone' => '+1 (202) 555-0155',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'avatar_initials' => 'NW',
            'active' => true,
            'bio_meta' => '2 repairs',
        ]);

        $customerSofia = User::firstOrCreate(['email' => 'sofia.m@example.com'], [
            'name' => 'Sofia Martinez',
            'phone' => '+1 (202) 555-0122',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'avatar_initials' => 'SM',
            'active' => false,
            'bio_meta' => '1 repair',
        ]);

        $customerAvery = User::firstOrCreate(['email' => 'avery.brooks@example.com'], [
            'name' => 'Avery Brooks',
            'phone' => '+1 (202) 555-0191',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'avatar_initials' => 'AB',
            'active' => true,
            'bio_meta' => '1 repair',
        ]);

        $customerEthan = User::firstOrCreate(['email' => 'ethan.cole@example.com'], [
            'name' => 'Ethan Cole',
            'phone' => '+1 (202) 555-0164',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'avatar_initials' => 'EC',
            'active' => true,
            'bio_meta' => '1 repair',
        ]);

        $customerMia = User::firstOrCreate(['email' => 'mia.davis@example.com'], [
            'name' => 'Mia Davis',
            'phone' => '+1 (202) 555-0188',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'avatar_initials' => 'MD',
            'active' => true,
            'bio_meta' => '1 repair',
        ]);

        // 3. Create Repairs
        $repairs = [
            [
                'id' => 'FX-1048',
                'customer_id' => $customerMaya->id,
                'customer_name' => 'Maya Chen',
                'customer_email' => 'maya.chen@example.com',
                'device' => 'ASUS ROG Zephyrus G14',
                'serial' => 'M7NRKD029184',
                'issue' => 'Overheating and sudden shutdowns',
                'method' => 'Mail-in',
                'status' => 'Diagnosing',
                'technician_id' => $techAlex->id,
                'technician_name' => 'Alex Kim',
                'active_step' => 1,
                'latest_update' => 'Alex is running a full hardware diagnostic and thermal test under sustained load.',
                'device_photo' => '/storage/repairs/devices/screen-artifact.svg',
                'device_photos' => ['/storage/repairs/devices/screen-artifact.svg'],
                'shipping_slip' => '/storage/repairs/slips/courier-slip.svg',
                'slip_verified' => true,
                'interacted' => true,
                'created_date_formatted' => 'Jun 12, 2025',
            ],
            [
                'id' => 'FX-1047',
                'customer_id' => $customerJordan->id,
                'customer_name' => 'Jordan Blake',
                'customer_email' => 'jordan.blake@example.com',
                'device' => 'Custom Desktop PC',
                'serial' => 'CST-445-9021',
                'issue' => 'No display after BIOS update',
                'method' => 'Drop-off',
                'status' => 'Waiting for part',
                'technician_id' => $techSam->id,
                'technician_name' => 'Sam Rivera',
                'active_step' => 1,
                'latest_update' => 'Awaiting replacement CMOS chip delivery. Expected in tomorrow.',
                'device_photo' => '/storage/repairs/devices/motherboard-chip.svg',
                'device_photos' => ['/storage/repairs/devices/motherboard-chip.svg'],
                'interacted' => false,
                'created_date_formatted' => 'Jun 11, 2025',
            ],
            [
                'id' => 'FX-1046',
                'customer_id' => $customerNoah->id,
                'customer_name' => 'Noah Wilson',
                'customer_email' => 'noah.wilson@example.com',
                'device' => 'Dell XPS 15 9520',
                'serial' => 'DX9520-77182',
                'issue' => 'Battery not charging',
                'method' => 'Mail-in',
                'status' => 'Fixing',
                'technician_id' => $techAlex->id,
                'technician_name' => 'Alex Kim',
                'active_step' => 3,
                'latest_update' => 'Replaced USB-C charging daughterboard and undergoing charging cycle benchmark.',
                'device_photo' => '/storage/repairs/devices/screen-artifact.svg',
                'device_photos' => ['/storage/repairs/devices/screen-artifact.svg'],
                'shipping_slip' => '/storage/repairs/slips/courier-slip.svg',
                'slip_verified' => true,
                'interacted' => true,
                'created_date_formatted' => 'Jun 10, 2025',
            ],
            [
                'id' => 'FX-1045',
                'customer_id' => $customerSofia->id,
                'customer_name' => 'Sofia Martinez',
                'customer_email' => 'sofia.m@example.com',
                'device' => 'Lenovo ThinkPad X1',
                'serial' => 'PF4K9M21',
                'issue' => 'Keyboard keys unresponsive',
                'method' => 'Drop-off',
                'status' => 'Pending',
                'technician_id' => null,
                'technician_name' => 'Unassigned',
                'active_step' => 0,
                'latest_update' => 'Dropped off at front desk. Waiting for bench allocation.',
                'interacted' => false,
                'created_date_formatted' => 'Jun 10, 2025',
            ],
            [
                'id' => 'FX-1052',
                'customer_id' => $customerAvery->id,
                'customer_name' => 'Avery Brooks',
                'customer_email' => 'avery.brooks@example.com',
                'device' => 'HP Omen 16',
                'serial' => 'HPO-16-99201',
                'issue' => 'Display flickers under load',
                'method' => 'Mail-in',
                'status' => 'Pending',
                'technician_id' => null,
                'technician_name' => 'Unassigned',
                'active_step' => 0,
                'latest_update' => 'Package shipping slip registered. Workshop awaiting parcel arrival.',
                'device_photo' => '/storage/repairs/devices/screen-artifact.svg',
                'device_photos' => ['/storage/repairs/devices/screen-artifact.svg'],
                'shipping_slip' => '/storage/repairs/slips/courier-slip.svg',
                'slip_verified' => false,
                'interacted' => false,
                'created_date_formatted' => 'Jun 13, 2025',
            ],
            [
                'id' => 'FX-1051',
                'customer_id' => $customerEthan->id,
                'customer_name' => 'Ethan Cole',
                'customer_email' => 'ethan.cole@example.com',
                'device' => 'MSI Stealth 15',
                'serial' => 'MSI-ST-4421',
                'issue' => 'Fans run at maximum speed constantly',
                'method' => 'Mail-in',
                'status' => 'Pending',
                'technician_id' => null,
                'technician_name' => 'Unassigned',
                'active_step' => 0,
                'latest_update' => 'Received from courier.',
                'shipping_slip' => '/storage/repairs/slips/courier-slip.svg',
                'slip_verified' => false,
                'interacted' => false,
                'created_date_formatted' => 'Jun 13, 2025',
            ],
            [
                'id' => 'FX-1050',
                'customer_id' => $customerMia->id,
                'customer_name' => 'Mia Davis',
                'customer_email' => 'mia.davis@example.com',
                'device' => 'Acer Predator Helios',
                'serial' => 'APH-300-8812',
                'issue' => 'Blue screen during gaming (KERNEL_SECURITY_CHECK_FAILURE)',
                'method' => 'Drop-off',
                'status' => 'Pending',
                'technician_id' => $techAlex->id,
                'technician_name' => 'Alex Kim',
                'active_step' => 0,
                'latest_update' => 'Assigned to Alex. Diagnostics to begin shortly.',
                'interacted' => false,
                'created_date_formatted' => 'Jun 12, 2025',
            ],
            [
                'id' => 'FX-0982',
                'customer_id' => $customerMaya->id,
                'customer_name' => 'Maya Chen',
                'customer_email' => 'maya.chen@example.com',
                'device' => 'MacBook Pro 14-inch',
                'serial' => 'C02XH04AJG5H',
                'issue' => 'Battery service warning and swelling',
                'method' => 'Drop-off',
                'status' => 'Finished',
                'technician_id' => $techAlex->id,
                'technician_name' => 'Alex Kim',
                'active_step' => 3,
                'latest_update' => 'Battery replacement is complete and the device passed final quality assurance testing.',
                'interacted' => true,
                'created_date_formatted' => 'Mar 04, 2025',
            ],
            [
                'id' => 'FX-0871',
                'customer_id' => $customerMaya->id,
                'customer_name' => 'Maya Chen',
                'customer_email' => 'maya.chen@example.com',
                'device' => 'Custom Desktop PC',
                'serial' => 'CST-445-8110',
                'issue' => 'Random power cycles and artifacting in Windows',
                'method' => 'Mail-in',
                'status' => 'Finished',
                'technician_id' => $techSam->id,
                'technician_name' => 'Sam Rivera',
                'active_step' => 4,
                'latest_update' => 'Graphics card and power supply were replaced. Repair completed.',
                'interacted' => true,
                'created_date_formatted' => 'Nov 18, 2024',
            ],
        ];

        foreach ($repairs as $r) {
            Repair::updateOrCreate(['id' => $r['id']], $r);
        }

        // 4. Create Feedback (Ratings & Complaints)
        $feedbackItems = [
            [
                'id' => 'FDB-205',
                'type' => 'Rating',
                'repair_id' => 'FX-1048',
                'user_id' => $customerMaya->id,
                'customer_name' => 'Maya Chen',
                'customer_email' => 'maya.chen@example.com',
                'subject' => 'Excellent diagnostic service',
                'message' => 'Alex explained the thermal issue clearly and kept me informed at each step. Appreciate the detailed benchmark charts.',
                'rating' => 5,
                'status' => 'New',
                'age_formatted' => '8 min ago',
            ],
            [
                'id' => 'CMP-204',
                'type' => 'Complaint',
                'repair_id' => 'FX-1048',
                'user_id' => $customerMaya->id,
                'customer_name' => 'Maya Chen',
                'customer_email' => 'maya.chen@example.com',
                'subject' => 'No update after diagnostic',
                'message' => 'I was expecting an update yesterday afternoon regarding parts cost and have not heard back yet. Please confirm timeline.',
                'rating' => 0,
                'status' => 'New',
                'age_formatted' => '18 min ago',
            ],
            [
                'id' => 'FDB-203',
                'type' => 'Rating',
                'repair_id' => 'FX-1046',
                'user_id' => $customerNoah->id,
                'customer_name' => 'Noah Wilson',
                'customer_email' => 'noah.wilson@example.com',
                'subject' => 'Helpful repair experience',
                'message' => 'The repair was handled professionally and the update notes were clear and useful.',
                'rating' => 4,
                'status' => 'Reviewed',
                'age_formatted' => 'Yesterday',
            ],
            [
                'id' => 'CMP-198',
                'type' => 'Complaint',
                'repair_id' => 'FX-1046',
                'user_id' => $customerNoah->id,
                'customer_name' => 'Noah Wilson',
                'customer_email' => 'noah.wilson@example.com',
                'subject' => 'Charging issue returned',
                'message' => 'The laptop stopped charging again after yesterday\'s battery stress test. Please advise on the next diagnostic step.',
                'rating' => 0,
                'status' => 'In review',
                'age_formatted' => '2 days ago',
            ],
        ];

        foreach ($feedbackItems as $fb) {
            Feedback::updateOrCreate(['id' => $fb['id']], $fb);
        }

        // 5. Create Initial Notifications
        Notification::truncate();

        // Customer notifications
        Notification::create([
            'role' => 'customer',
            'user_id' => $customerMaya->id,
            'icon' => 'tool',
            'title' => 'Diagnostic update ready',
            'message' => 'Alex added a new note to repair FX-1048.',
            'upcoming' => false,
            'time_label' => '12 minutes ago',
            'target' => 'repairs',
            'entity_id' => 'FX-1048',
            'is_read' => false,
        ]);
        Notification::create([
            'role' => 'customer',
            'user_id' => $customerMaya->id,
            'icon' => 'calendar',
            'title' => 'Expected workshop update',
            'message' => 'Your next repair update is expected tomorrow by 5:00 PM.',
            'upcoming' => true,
            'time_label' => 'Tomorrow, 5:00 PM',
            'target' => 'repairs',
            'entity_id' => 'FX-1048',
            'is_read' => false,
        ]);
        Notification::create([
            'role' => 'customer',
            'user_id' => $customerMaya->id,
            'icon' => 'box',
            'title' => 'Package delivered',
            'message' => 'Your device was delivered safely to the Fixsmith workshop.',
            'upcoming' => false,
            'time_label' => 'Yesterday',
            'target' => 'repairs',
            'entity_id' => 'FX-1048',
            'is_read' => true,
        ]);

        // Admin notifications
        Notification::create([
            'role' => 'admin',
            'user_id' => $adminEmma->id,
            'icon' => 'inbox',
            'title' => 'Four requests need assignment',
            'message' => 'New repair requests are waiting in the dispatch queue.',
            'upcoming' => false,
            'time_label' => '8 minutes ago',
            'target' => 'team',
            'is_read' => false,
        ]);
        Notification::create([
            'role' => 'admin',
            'user_id' => $adminEmma->id,
            'icon' => 'calendar',
            'title' => 'Weekly operations summary',
            'message' => 'The workshop performance report will be ready for review.',
            'upcoming' => true,
            'time_label' => 'Friday, 4:00 PM',
            'target' => 'dashboard',
            'is_read' => false,
        ]);
        Notification::create([
            'role' => 'admin',
            'user_id' => $adminEmma->id,
            'icon' => 'star',
            'title' => 'New customer feedback',
            'message' => 'Maya Chen submitted a five-star rating for FX-1048.',
            'upcoming' => false,
            'time_label' => '32 minutes ago',
            'target' => 'complaints',
            'entity_id' => 'FDB-205',
            'is_read' => false,
        ]);

        // Technician notifications
        Notification::create([
            'role' => 'technician',
            'user_id' => $techAlex->id,
            'icon' => 'tool',
            'title' => 'New repair assigned',
            'message' => 'Repair FX-1046 was added to your active queue.',
            'upcoming' => false,
            'time_label' => '5 minutes ago',
            'target' => 'assigned',
            'entity_id' => 'FX-1046',
            'is_read' => false,
        ]);
        Notification::create([
            'role' => 'technician',
            'user_id' => $techAlex->id,
            'icon' => 'clock',
            'title' => 'Customer update due',
            'message' => 'FX-1048 needs a customer-facing progress note.',
            'upcoming' => true,
            'time_label' => 'Today, 3:30 PM',
            'target' => 'assigned',
            'entity_id' => 'FX-1048',
            'is_read' => false,
        ]);
        Notification::create([
            'role' => 'technician',
            'user_id' => $techAlex->id,
            'icon' => 'star',
            'title' => 'Customer left feedback',
            'message' => 'You received a five-star rating from Maya Chen.',
            'upcoming' => false,
            'time_label' => '1 hour ago',
            'target' => 'complaints',
            'entity_id' => 'FDB-205',
            'is_read' => false,
        ]);

        // 6. Initial Email Logs sample
        EmailLog::create([
            'to_email' => 'maya.chen@example.com',
            'to_name' => 'Maya Chen',
            'subject' => 'Repair Request Received: FX-1048 - ASUS ROG Zephyrus G14',
            'body_preview' => 'Thank you for choosing Fixsmith! We have logged your repair ticket FX-1048...',
            'status' => 'logged',
            'event_type' => 'repair_created',
        ]);
    }
}
