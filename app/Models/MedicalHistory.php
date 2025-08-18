<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalHistory extends Model
{
    use HasFactory;

    protected $table = 'medical_history';

    protected $fillable = [
        'patient_id',
        'category',
        'title',
        'description',
        'date_occurred',
        'is_active'
    ];

    protected $casts = [
        'date_occurred' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relation avec le patient
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Scope pour les antécédents actifs
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope par catégorie
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}