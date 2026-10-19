<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmtpSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'mailer',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_address',
        'from_name',
        'is_enabled',
    ];

    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'is_enabled' => 'boolean',
        ];
    }

    public static function current(): self
    {
        $setting = self::first();
        if (!$setting) {
            $setting = self::create([
                'mailer' => env('MAIL_MAILER', 'log'),
                'host' => env('MAIL_HOST', 'smtp.mailtrap.io'),
                'port' => (int)env('MAIL_PORT', 587),
                'username' => env('MAIL_USERNAME', ''),
                'password' => env('MAIL_PASSWORD', ''),
                'encryption' => env('MAIL_ENCRYPTION', 'tls'),
                'from_address' => env('MAIL_FROM_ADDRESS', 'support@fixsmith.com'),
                'from_name' => env('MAIL_FROM_NAME', 'Fixsmith Repair Studio'),
                'is_enabled' => true,
            ]);
        }
        return $setting;
    }
}
