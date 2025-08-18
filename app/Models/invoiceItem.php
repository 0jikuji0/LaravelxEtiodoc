<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_price',
        'total_price'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime'
    ];

    public $timestamps = false; // Seul created_at dans le schéma

    // Relation avec la facture
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Mutateur pour calculer automatiquement le prix total
    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = $value;
        $this->calculateTotal();
    }

    public function setUnitPriceAttribute($value)
    {
        $this->attributes['unit_price'] = $value;
        $this->calculateTotal();
    }

    private function calculateTotal()
    {
        if (isset($this->attributes['quantity']) && isset($this->attributes['unit_price'])) {
            $this->attributes['total_price'] = $this->attributes['quantity'] * $this->attributes['unit_price'];
        }
    }
}