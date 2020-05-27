<?php

namespace App\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $table      = "tbl_social_accounts";
    public $incrementing  = false;
    public $timestamps    = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider_user_id',
        'provider',
        'url',
        'expires_time',
        'token',
        'create_date',
        'alter_date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id', 'id');
    }
}
