<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'scheduled_at',
        'status',
        'notes',
        'outcome',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
} 