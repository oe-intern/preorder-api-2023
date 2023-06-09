<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Variant extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'product_id',
        'price',
        'option1',
        'option2',
        'title_var',
        'sku',
        'stock',
        'sold',
        'preorder'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function preorders()
    {
        return $this->belongsTo(Preorder::class, 'variant_id', 'id');
    }

    public static function saveVariantInfo($variant)
    {
        $data = [
            'id' => $variant['id'],
            'product_id' => $variant['product_id'],
            'title_var' => $variant['title'],
            'price' => $variant['price'],
            'option1' => $variant['option1'],
            'option2' => $variant['option2'],
            'sku' => $variant['sku']
        ];

        self::updateOrCreate(['id' => $variant['id']], $data);
    }

    public static function getVariants($user_id)
    {
        return Variant::with('product:id,user_id')->whereHas('product', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })->get();
    }

    public static function setStock($variants_stock, $reset = 0)
    {
        if ($reset == 1) {
            Variant::whereIn('product_id', $variants_stock)->update(['stock' => 0]);
        } else {
            foreach ($variants_stock as $variant) {
                Variant::where('id', $variant['id'])->update(['stock' => $variant['stock']]);
            }
        }
    }

    public static function getUserIdByVariant($variant_id)
    {
        $variant = Variant::where('id', $variant_id)->first();
        return $variant->product->user_id;
    }

    public static function deductStock($variant_id, $quantity)
    {
        Variant::where('id', $variant_id)->decrement('stock', $quantity);
    }

    public static function addPreorder($variant_id, $quantity)
    {
        Variant::where('id', $variant_id)->increment('preorder', $quantity);
    }

    public static function extractId($variants)
    {
        return collect($variants)->pluck('id')->all();
    }

    public static function fulfillVar($variants_id)
{
    Variant::whereIn('id', $variants_id)
        ->update([
            'sold' => DB::raw('sold + preorder'),
            'preorder' => 0
        ]);
}
}
