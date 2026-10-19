<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'to_email',
        'to_name',
        'subject',
        'body_preview',
        'status',
        'error_message',
        'event_type',
    ];
}
