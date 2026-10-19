<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_id',
        'customer_name',
        'customer_email',
        'device',
        'serial',
        'issue',
        'method',
        'status',
        'technician_id',
        'technician_name',
        'active_step',
        'latest_update',
        'device_photo',
        'device_photos',
        'shipping_slip',
        'slip_verified',
        'interacted',
        'created_date_formatted',
    ];

    protected function casts(): array
    {
        return [
            'active_step' => 'integer',
            'interacted' => 'boolean',
            'slip_verified' => 'boolean',
            'device_photos' => 'array',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public static function generateId(): string
    {
        $allIds = self::pluck('id');
        $maxNum = 1052;
        foreach ($allIds as $id) {
            if (preg_match('/FX-(\d+)/', (string)$id, $matches)) {
                $n = (int)$matches[1];
                if ($n > $maxNum) {
                    $maxNum = $n;
                }
            }
        }
        $nextNum = $maxNum + 1;
        $candidate = 'FX-' . $nextNum;
        while (self::where('id', $candidate)->exists()) {
            $nextNum++;
            $candidate = 'FX-' . $nextNum;
        }
        return $candidate;
    }
}
