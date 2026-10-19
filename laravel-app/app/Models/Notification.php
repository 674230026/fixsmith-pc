<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role',
        'title',
        'message',
        'icon',
        'upcoming',
        'time_label',
        'target',
        'entity_id',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'upcoming' => 'boolean',
            'is_read' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
