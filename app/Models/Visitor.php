<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $table        = 'visitor';
    protected $fillable     = ['produk_id', 'ip', 'tgl'];
    protected $primaryKey   = 'produk_id';

    public $timestamps      = false;
    public $autoincrements  = false;
}
