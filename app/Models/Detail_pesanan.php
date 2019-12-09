<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detail_pesanan extends Model
{
    protected $table    = 'detail_pesanan';
    public $timestamps  = false;

    protected $fillable = [
        'status',
        'no_resi'
    ];

    protected $primaryKey = 'pesanan_id';

    public function setPending()
    {
        $this->attributes['status'] = 1;
        self::save();
    }

    /**
     * Set status to Success
     *
     * @return void
     */
    public function setSuccess()
    {
        $this->attributes['status'] = 2;
        self::save();
    }

    /**
     * Set status to Failed
     *
     * @return void
     */
    public function setFailed()
    {
        $this->attributes['status'] = 4;
        self::save();
    }
}
