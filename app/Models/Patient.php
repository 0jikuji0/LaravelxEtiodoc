<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    // 🔥 IMPORTANT : Spécifiez explicitement la table et la clé primaire
    protected $table = 'patients';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'birthdate',
        'gender',
        'emergency_contact_name',
        'emergency_contact_phone',
        'social_security_number'
    ];

    protected $casts = [
        'birthdate' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // 🔥 IMPORTANT : Relation avec l'utilisateur (médecin)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔥 IMPORTANT : Relation avec les consultations - PLURIEL
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    // 🔥 IMPORTANT : Relation avec les antécédents médicaux - PLURIEL
    public function medicalHistories()
    {
        return $this->hasMany(MedicalHistory::class);
    }

    // 🔥 IMPORTANT : Relation avec les rapports médicaux - PLURIEL
    public function medicalReports()
    {
        return $this->hasMany(MedicalReport::class);
    }

    // Relations optionnelles (créez les modèles si nécessaire)
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function documents()
    {
        return $this->hasMany(PatientDocument::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // Accesseurs utiles
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getAgeAttribute()
    {
        return $this->birthdate ? $this->birthdate->diffInYears(now()) : null;
    }

    // 🔥 IMPORTANT : Méthode pour résoudre le route model binding
    public function getRouteKeyName()
    {
        return 'id'; // Utilise la clé primaire ID
    }
}