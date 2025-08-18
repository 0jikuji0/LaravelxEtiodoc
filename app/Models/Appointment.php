<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'user_id',
        'appointment_date',
        'duration_minutes',
        'type',
        'status',
        'notes',
        'reminder_sent',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'duration_minutes' => 'integer',
        'reminder_sent' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope pour les rendez-vous programmés
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    // Scope pour les rendez-vous futurs
    public function scopeFuture($query)
    {
        return $query->where('appointment_date', '>', now());
    }
}