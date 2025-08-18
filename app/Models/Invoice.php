<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'user_id',
        'consultation_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'total_amount',
        'status',
        'payment_method',
        'payment_date',
        'notes'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
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

    // Relation avec la consultation (optionnelle)
    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    // Relation avec les lignes de facturation
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Scope pour les factures impayées
    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['draft', 'sent', 'overdue']);
    }

    // Scope pour les factures payées
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // Scope pour les factures en retard
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereIn('status', ['sent', 'overdue']);
    }

    // Accesseur pour vérifier si la facture est en retard
    public function getIsOverdueAttribute()
    {
        return $this->due_date && 
               $this->due_date < now() && 
               in_array($this->status, ['sent', 'overdue']);
    }

    // Méthode pour générer un numéro de facture automatique
    public static function generateInvoiceNumber()
    {
        $year = date('Y');
        $lastInvoice = static::where('invoice_number', 'like', $year . '%')
                           ->orderBy('invoice_number', 'desc')
                           ->first();

        if ($lastInvoice) {
            $lastNumber = intval(substr($lastInvoice->invoice_number, 4));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $year . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}