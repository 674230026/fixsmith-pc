<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'avatar_initials',
        'active',
        'technician_capacity',
        'technician_title',
        'bio_meta',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
            'technician_capacity' => 'integer',
        ];
    }

    public function repairs()
    {
        return $this->hasMany(Repair::class, 'customer_id');
    }

    public function assignedRepairs()
    {
        return $this->hasMany(Repair::class, 'technician_id');
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public static function getInitials(string $name): string
    {
        $words = preg_split('/\s+/', trim($name));
        $initials = '';
        foreach ($words as $w) {
            if (!empty($w)) {
                $initials .= mb_substr($w, 0, 1);
            }
        }
        return strtoupper(mb_substr($initials, 0, 2));
    }
}
