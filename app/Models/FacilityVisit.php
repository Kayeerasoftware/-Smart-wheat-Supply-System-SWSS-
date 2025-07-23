<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FacilityVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'scheduled_at',
        'status',
        'notes',
        'outcome'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
} 