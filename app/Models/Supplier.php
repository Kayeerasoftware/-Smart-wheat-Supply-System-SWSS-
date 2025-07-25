<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the raw materials supplied by this supplier.
     */
    public function rawMaterials()
    {
        return $this->hasMany(RawMaterial::class);
    }

    /**
     * Get the contact email attribute.
     */
    public function getContactEmailAttribute()
    {
        return $this->email;
    }

    /**
     * Get the contact phone attribute.
     */
    public function getContactPhoneAttribute()
    {
        return $this->phone;
    }

    /**
     * Scope a query to only include active suppliers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
} 