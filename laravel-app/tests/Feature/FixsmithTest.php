<?php

namespace Tests\Feature;

use App\Models\Repair;
use App\Models\SmtpSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FixsmithTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        SmtpSetting::create([
            'mailer' => 'log',
            'host' => '127.0.0.1',
            'port' => 2525,
            'from_address' => 'support@fixsmith.com',
            'from_name' => 'Fixsmith Repair Studio',
        ]);
    }

    public function test_homepage_loads_successfully(): void
    {
        $user = User::create([
            'name' => 'Maya Chen',
            'email' => 'maya.chen@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Fixsmith');
    }

    public function test_customer_cannot_create_mail_in_repair_without_shipping_slip(): void
    {
        $user = User::create([
            'name' => 'Maya Chen',
            'email' => 'maya.chen@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $this->actingAs($user);

        $response = $this->postJson('/api/repairs', [
            'device' => 'Dell Alienware m16',
            'serial' => 'AW-99201-X',
            'symptom' => 'GPU artifacting under FurMark benchmark',
            'delivery' => 'mail',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['shipping_slip']);
    }

    public function test_customer_can_create_repair_and_trigger_email(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = User::create([
            'name' => 'Maya Chen',
            'email' => 'maya.chen@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $this->actingAs($user);

        $fakeSlip = \Illuminate\Http\UploadedFile::fake()->create('courier-receipt.jpg', 120);
        $fakePhoto1 = \Illuminate\Http\UploadedFile::fake()->create('symptom1.jpg', 80);
        $fakePhoto2 = \Illuminate\Http\UploadedFile::fake()->create('symptom2.jpg', 90);

        $response = $this->postJson('/api/repairs', [
            'device' => 'Dell Alienware m16',
            'serial' => 'AW-99201-X',
            'symptom' => 'GPU artifacting under FurMark benchmark',
            'delivery' => 'mail',
            'shipping_slip' => $fakeSlip,
            'device_photos' => [$fakePhoto1, $fakePhoto2],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('repairs', [
            'serial' => 'AW-99201-X',
            'customer_email' => 'maya.chen@example.com',
            'status' => 'Pending',
        ]);

        // Email log must have been created
        $this->assertDatabaseHas('email_logs', [
            'to_email' => 'maya.chen@example.com',
            'event_type' => 'repair_created',
        ]);
    }

    public function test_technician_can_update_status_and_notify_customer(): void
    {
        $customer = User::create([
            'name' => 'Jordan Blake',
            'email' => 'jordan@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $repair = Repair::create([
            'id' => 'FX-1999',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'device' => 'ThinkPad P1',
            'serial' => 'TP-99281',
            'issue' => 'Dead screen',
            'method' => 'Drop-off',
            'status' => 'Diagnosing',
            'active_step' => 1,
        ]);

        $response = $this->postJson("/api/repairs/{$repair->id}/status", [
            'status' => 'Fixing',
            'note' => 'Display panel replacement installed and tested.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('repairs', [
            'id' => 'FX-1999',
            'status' => 'Fixing',
            'active_step' => 2,
        ]);

        $this->assertDatabaseHas('email_logs', [
            'to_email' => 'jordan@example.com',
            'event_type' => 'status_updated',
        ]);
    }

    public function test_customer_can_submit_complaint_and_admin_reply(): void
    {
        $customer = User::create([
            'name' => 'Noah Wilson',
            'email' => 'noah@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $this->actingAs($customer);

        // Submit complaint
        $response = $this->postJson('/api/feedback', [
            'mode' => 'complaint',
            'repair_id' => 'FX-1046',
            'subject' => 'Delayed pickup inquiry',
            'message' => 'When can I expect the laptop to be ready?',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $feedbackId = $response->json('feedback.id');

        // Admin replies
        $replyResponse = $this->postJson("/api/feedback/{$feedbackId}/reply", [
            'reply' => 'Your unit is ready for collection today until 6 PM.',
        ]);

        $replyResponse->assertStatus(200);

        $this->assertDatabaseHas('feedback', [
            'id' => $feedbackId,
            'status' => 'Resolved',
        ]);

        $this->assertDatabaseHas('email_logs', [
            'to_email' => 'noah@example.com',
            'event_type' => 'complaint_reply',
        ]);
    }

    public function test_smtp_test_email_endpoint(): void
    {
        $response = $this->postJson('/api/smtp/test', [
            'recipient_email' => 'test-recipient@example.com',
            'custom_message' => 'Testing automated email dispatch.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('email_logs', [
            'to_email' => 'test-recipient@example.com',
            'event_type' => 'test_email',
        ]);
    }

    public function test_admin_moving_job_to_other_technician_triggers_in_web_and_email_notifications(): void
    {
        $customer = User::create([
            'name' => 'Maya Chen',
            'email' => 'maya@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $techSam = User::create([
            'name' => 'Sam Rivera',
            'email' => 'sam.rivera@fixsmith.com',
            'password' => Hash::make('password123'),
            'role' => 'technician',
        ]);

        $techAlex = User::create([
            'name' => 'Alex Kim',
            'email' => 'alex.kim@fixsmith.com',
            'password' => Hash::make('password123'),
            'role' => 'technician',
        ]);

        $repair = Repair::create([
            'id' => 'FX-2020',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'device' => 'Custom Gaming PC',
            'serial' => 'CST-2020-01',
            'issue' => 'Power supply failure',
            'method' => 'Drop-off',
            'status' => 'Pending',
            'technician_id' => $techSam->id,
            'technician_name' => $techSam->name,
            'active_step' => 0,
            'interacted' => false,
        ]);

        // Admin moves job from Sam Rivera to Alex Kim via workload board
        $response = $this->postJson('/api/repairs/workload', [
            'job_id' => $repair->id,
            'technician' => 'alex',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // 1. Database updated
        $this->assertDatabaseHas('repairs', [
            'id' => 'FX-2020',
            'technician_name' => 'Alex Kim',
        ]);

        // 2. In-web notification created for new technician (Alex Kim)
        $this->assertDatabaseHas('notifications', [
            'role' => 'technician',
            'user_id' => $techAlex->id,
            'entity_id' => 'FX-2020',
        ]);

        // 3. In-web notification created for previous technician (Sam Rivera)
        $this->assertDatabaseHas('notifications', [
            'role' => 'technician',
            'user_id' => $techSam->id,
            'entity_id' => 'FX-2020',
        ]);

        // 4. Email notification dispatched to Alex Kim
        $this->assertDatabaseHas('email_logs', [
            'to_email' => 'alex.kim@fixsmith.com',
            'event_type' => 'repair_assigned',
        ]);

        // 5. Email notification dispatched to Sam Rivera
        $this->assertDatabaseHas('email_logs', [
            'to_email' => 'sam.rivera@fixsmith.com',
            'event_type' => 'repair_transferred',
        ]);
    }

    public function test_admin_cannot_assign_technician_to_pending_mail_in_repair(): void
    {
        $customer = User::create([
            'name' => 'Sara Lee',
            'email' => 'sara@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $tech = User::create([
            'name' => 'Alex Kim',
            'email' => 'alex.kim@fixsmith.com',
            'password' => Hash::make('password123'),
            'role' => 'technician',
        ]);

        $repair = Repair::create([
            'id' => 'FX-MAIL-1',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'device' => 'Gaming Laptop',
            'serial' => 'SN-MAIL-001',
            'issue' => 'Cracked screen',
            'method' => 'Mail-in',
            'status' => 'Pending',
            'technician_name' => 'Unassigned',
            'active_step' => 0,
            'interacted' => false,
        ]);

        // Attempting to assign technician should fail because status is still Pending
        $response = $this->postJson("/api/repairs/{$repair->id}/assign", [
            'technician' => 'Alex Kim',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_admin_can_receive_mail_in_package_and_assign_technician(): void
    {
        $customer = User::create([
            'name' => 'Sara Lee',
            'email' => 'sara@example.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $tech = User::create([
            'name' => 'Alex Kim',
            'email' => 'alex.kim@fixsmith.com',
            'password' => Hash::make('password123'),
            'role' => 'technician',
        ]);

        $repair = Repair::create([
            'id' => 'FX-MAIL-2',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'device' => 'Gaming Laptop',
            'serial' => 'SN-MAIL-002',
            'issue' => 'Cracked screen',
            'method' => 'Mail-in',
            'status' => 'Pending',
            'technician_name' => 'Unassigned',
            'active_step' => 0,
            'shipping_slip' => '/storage/repairs/slips/courier-slip.svg',
            'interacted' => false,
        ]);

        // 1. Admin confirms package receipt
        $receiveResponse = $this->postJson("/api/repairs/{$repair->id}/receive");
        $receiveResponse->assertStatus(200);
        $receiveResponse->assertJson(['success' => true]);

        $this->assertDatabaseHas('repairs', [
            'id' => 'FX-MAIL-2',
            'status' => 'Package received',
            'slip_verified' => true,
        ]);

        // 2. Now technician assignment is unlocked and succeeds
        $assignResponse = $this->postJson("/api/repairs/{$repair->id}/assign", [
            'technician' => 'Alex Kim',
        ]);

        $assignResponse->assertStatus(200);
        $assignResponse->assertJson(['success' => true]);

        $this->assertDatabaseHas('repairs', [
            'id' => 'FX-MAIL-2',
            'technician_name' => 'Alex Kim',
        ]);
    }

    public function test_sequential_repair_requests_generate_unique_ids(): void
    {
        $user = User::factory()->create();

        $id1 = Repair::generateId();
        Repair::create([
            'id' => $id1,
            'customer_id' => $user->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'device' => 'PC Test 1',
            'serial' => 'SN-1111',
            'issue' => 'Issue 1',
            'method' => 'Drop-off',
            'status' => 'Pending',
            'technician_name' => 'Unassigned',
            'active_step' => 0,
            'latest_update' => 'Created',
            'slip_verified' => false,
            'interacted' => false,
        ]);

        $id2 = Repair::generateId();
        $this->assertNotEquals($id1, $id2);

        Repair::create([
            'id' => $id2,
            'customer_id' => $user->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'device' => 'PC Test 2',
            'serial' => 'SN-2222',
            'issue' => 'Issue 2',
            'method' => 'Drop-off',
            'status' => 'Pending',
            'technician_name' => 'Unassigned',
            'active_step' => 0,
            'latest_update' => 'Created',
            'slip_verified' => false,
            'interacted' => false,
        ]);

        $this->assertDatabaseHas('repairs', ['id' => $id1]);
        $this->assertDatabaseHas('repairs', ['id' => $id2]);
    }

    public function test_can_upload_gif_file_for_meme_or_symptoms(): void
    {
        Storage::fake('public');

        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        $gifFile = UploadedFile::fake()->create('funny_meme.gif', 100, 'image/gif');

        $response = $this->post('/api/repairs', [
            'device' => 'Gaming Rig',
            'serial' => 'SN-GIF-1234',
            'symptom' => 'GPU artifacting meme',
            'delivery' => 'dropoff',
            'device_photos' => [$gifFile],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $repair = Repair::where('serial', 'SN-GIF-1234')->first();
        $this->assertNotNull($repair);
        $this->assertNotEmpty($repair->device_photos);
        $this->assertStringEndsWith('.gif', $repair->device_photos[0]);
    }

    public function test_can_create_new_technician_account_and_assign_work_via_workload_and_table(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // 1. Create a new technician account
        $newTechRes = $this->postJson('/api/accounts', [
            'name' => 'Fotogus Tech',
            'email' => 'fotogus.tech@example.com',
            'password' => 'secret12345',
            'role' => 'Technician',
        ]);

        $newTechRes->assertStatus(200);
        $newTechRes->assertJson(['success' => true]);
        $newTechId = $newTechRes->json('user.id');

        $this->assertDatabaseHas('users', [
            'name' => 'Fotogus Tech',
            'role' => 'technician',
        ]);

        // 2. Create a repair ticket
        $customer = User::factory()->create(['role' => 'customer']);
        $repair = Repair::create([
            'id' => Repair::generateId(),
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'device' => 'Custom Rig',
            'serial' => 'SN-FOTOGUS-1',
            'issue' => 'Fans noisy',
            'method' => 'Drop-off',
            'status' => 'Pending',
            'technician_name' => 'Unassigned',
            'active_step' => 0,
            'latest_update' => 'Created',
            'slip_verified' => false,
            'interacted' => false,
        ]);

        // 3. Assign to newly created technician via Request table
        $assignRes = $this->postJson("/api/repairs/{$repair->id}/assign", [
            'technician' => 'Fotogus Tech',
        ]);
        $assignRes->assertStatus(200);
        $assignRes->assertJson(['success' => true]);

        $this->assertDatabaseHas('repairs', [
            'id' => $repair->id,
            'technician_name' => 'Fotogus Tech',
            'technician_id' => $newTechId,
        ]);

        // 4. Move workload via Team Manage endpoint to another newly created tech
        $workloadRes = $this->postJson('/api/repairs/workload', [
            'job_id' => $repair->id,
            'technician' => 'Fotogus Tech',
            'technician_id' => $newTechId,
        ]);
        $workloadRes->assertStatus(200);
        $workloadRes->assertJson(['success' => true]);
    }
}

