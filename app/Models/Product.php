<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = [
        'product_category_id',
        'supplier_id',
        'name',
        'description',
        'dosage',
        'strength',
        'requires_prescription',
        'price',
        'stock',
        'reorder_threshold',
        'expiration_date',
    ];

    protected $casts = [
        'requires_prescription' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function wishlistedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
