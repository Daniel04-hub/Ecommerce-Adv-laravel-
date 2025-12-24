<?php

/**
 * @property int $id
 * @property int $user_id
 * @property int $vendor_id
 * @property int $product_id
 * @property int $quantity
 * @property float $price
 * @property string $status
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'vendor_id',
        'product_id',
        'quantity',
        'price',
        'status',
        'full_name',
        'email',
        'phone',
        'address',
    ];

    // Allowed forward-only transitions
    public static function transitions(): array
    {
        return [
            'placed'   => 'accepted',
            'accepted' => 'shipped',
            'shipped'  => 'completed',
        ];
    }

    public function canTransitionTo(string $next): bool
    {
        $map = self::transitions();
        return isset($map[$this->status]) && $map[$this->status] === $next;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Alias for customer() - commonly used as 'user' in queries
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
