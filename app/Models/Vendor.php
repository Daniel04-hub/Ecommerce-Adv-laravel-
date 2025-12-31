<?php

/**
 * @property int $id
 * @property int $user_id
 * @property string $company_name
 * @property string|null $gst_number
 * @property string|null $address
 * @property string $status
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'gst_number',
        'address',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function profile()
    {
        return $this->hasOne(VendorProfile::class);
    }
}
