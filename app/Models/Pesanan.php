<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table    = 'pesanan';
    /**
     * Fillable attribute.
     *
     * @var array
     */
    protected $fillable = [
        'pesanan_id',
        'user_id'
    ];

    protected $dates = ['created_at', 'updated_at'];

    /**
     * Set status to Pending
     *
     * @return void
     */
    // public function setPending()
    // {
    //     $this->attributes['status'] = 'pending';
    //     self::save();
    // }

    // /**
    //  * Set status to Success
    //  *
    //  * @return void
    //  */
    // public function setSuccess()
    // {
    //     $this->attributes['status'] = 'success';
    //     self::save();
    // }

    // /**
    //  * Set status to Failed
    //  *
    //  * @return void
    //  */
    // public function setFailed()
    // {
    //     $this->attributes['status'] = 'failed';
    //     self::save();
    // }

    // /**
    //  * Set status to Expired
    //  *
    //  * @return void
    //  */
    // public function setExpired()
    // {
    //     $this->attributes['status'] = 'expired';
    //     self::save();
    // }
}
