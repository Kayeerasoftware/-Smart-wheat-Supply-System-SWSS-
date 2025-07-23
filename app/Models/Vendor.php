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
        'processing_status',
        'score_financial',
        'score_reputation',
        'score_compliance',
        'total_score',
        'pdf_paths',
        'image_paths',
        'pdf_validation_result',
        'facility_visit_scheduled',
        'facility_visit_date',
        'facility_visit_notes',
    ];

    protected $casts = [
        'application_data' => 'array',
        'pdf_paths' => 'array',
        'image_paths' => 'array',
        'pdf_validation_result' => 'array',
        'facility_visit_scheduled' => 'boolean',
        'facility_visit_date' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PDF_VALIDATED = 'pdf_validated';
    const STATUS_PDF_REJECTED = 'pdf_rejected';
    const STATUS_PENDING_VISIT = 'pending_visit';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Processing status constants
    const PROCESSING_PENDING_REVIEW = 'pending_review';
    const PROCESSING_PDF_VALIDATION_FAILED = 'pdf_validation_failed';
    const PROCESSING_PENDING_VISIT = 'pending_visit';
    const PROCESSING_VISIT_COMPLETED = 'visit_completed';
    const PROCESSING_APPROVED = 'approved';
    const PROCESSING_REJECTED = 'rejected';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function facilityVisits()
    {
        return $this->hasMany(FacilityVisit::class);
    }

    /**
     * Check if supplier has full access to system features
     */
    public function hasFullAccess()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if supplier has basic access (PDF validated but not approved)
     */
    public function hasBasicAccess()
    {
        return in_array($this->status, [
            self::STATUS_PDF_VALIDATED,
            self::STATUS_PENDING_VISIT
        ]);
    }

    /**
     * Check if supplier can access restricted features
     */
    public function canAccessRestrictedFeatures()
    {
        return $this->hasFullAccess();
    }

    /**
     * Get status message for display
     */
    public function getStatusMessage()
    {
        $messages = [
            self::STATUS_PENDING => 'Application submitted and pending review.',
            self::STATUS_PDF_VALIDATED => 'PDF validated successfully. Awaiting facility visit.',
            self::STATUS_PDF_REJECTED => 'PDF validation failed. Please upload a valid document.',
            self::STATUS_PENDING_VISIT => 'PDF validated. Facility visit scheduled.',
            self::STATUS_APPROVED => 'Application approved. Full access granted.',
            self::STATUS_REJECTED => 'Application rejected.',
        ];

        return $messages[$this->status] ?? 'Status unknown.';
    }
} 