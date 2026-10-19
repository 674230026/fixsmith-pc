<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'type',
        'repair_id',
        'user_id',
        'customer_name',
        'customer_email',
        'subject',
        'message',
        'rating',
        'attachment',
        'status',
        'admin_reply',
        'age_formatted',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function generateId(string $type): string
    {
        $prefix = ($type === 'Rating') ? 'FDB-' : 'CMP-';
        $last = self::where('id', 'like', $prefix . '%')->orderBy('created_at', 'desc')->first();
        if ($last && preg_match('/(?:FDB|CMP)-(\d+)/', $last->id, $matches)) {
            $num = (int)$matches[1] + 1;
        } else {
            $num = ($type === 'Rating') ? 206 : 205;
        }
        return $prefix . $num;
    }
}
