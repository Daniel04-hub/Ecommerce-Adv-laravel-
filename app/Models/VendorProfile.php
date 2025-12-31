<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorProfile extends Model
{
    protected $fillable = [
        'vendor_id',
        'company_email',
        'company_phone',
        'company_address',
        'company_description',
        'company_logo_path',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
