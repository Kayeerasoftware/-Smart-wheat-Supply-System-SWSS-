<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'application_data',
        'status',
        'score_financial',
        'score_reputation',
        'score_compliance',
        'total_score',
        'pdf_paths',
    ];

    protected $casts = [
        'application_data' => 'array',
        'pdf_paths' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function facilityVisits()
    {
        return $this->hasMany(FacilityVisit::class);
    }
} 