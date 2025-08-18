<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientDocument extends Model
{
    use HasFactory;

    protected $table = 'patient_documents';

    protected $fillable = [
        'patient_id',
        'consultation_id',
        'document_type',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'description',
        'uploaded_by'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relation avec le patient
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Relation avec la consultation (optionnelle)
    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    // Relation avec l'utilisateur qui a uploadé
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Scope par type de document
    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    // Accesseur pour la taille du fichier formatée
    public function getFormattedFileSizeAttribute()
    {
        if ($this->file_size < 1024) {
            return $this->file_size . ' B';
        } elseif ($this->file_size < 1048576) {
            return round($this->file_size / 1024, 2) . ' KB';
        } else {
            return round($this->file_size / 1048576, 2) . ' MB';
        }
    }

    // Méthode pour obtenir l'URL complète du fichier
    public function getFileUrl()
    {
        return asset('storage/' . $this->file_path);
    }
}