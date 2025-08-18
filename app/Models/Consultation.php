<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\ConsultationController;


class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'user_id',
        'consultation_date',
        'motif',
        'symptoms',
        'clinical_examination',
        'etiopathic_diagnosis',
        'treatment_plan',
        'recommendations',
        'next_appointment',
        'status',
        'price',
        'payment_status',
        'notes'
    ];

    protected $casts = [
        'consultation_date' => 'datetime',
        'next_appointment' => 'date',
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relation avec le patient
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Relation avec l'utilisateur (médecin)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec les rapports médicaux
    public function medicalReports()
    {
        return $this->hasMany(MedicalReport::class);
    }

    // Relation avec les traitements
    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    // Scope pour les consultations récentes
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('consultation_date', '>=', now()->subDays($days));
    }

    // Scope par statut
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}