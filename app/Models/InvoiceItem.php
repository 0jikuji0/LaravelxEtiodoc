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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // timestamps correspond to migration (created_at and updated_at)
    public $timestamps = true;

    // Relation avec la facture
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Mutateurs pour recalculer automatiquement le total
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

    private function calculateTotal(): void
    {
        if (isset($this->attributes['quantity']) && isset($this->attributes['unit_price'])) {
            $qty = (float) $this->attributes['quantity'];
            $unit = (float) $this->attributes['unit_price'];
            $this->attributes['total_price'] = $qty * $unit;
        }
    }
}
