<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table    = 'users';
    protected $fillable = ['id', 'nama', 'no_telp', 'email', 'username', 'password', 'uang', 'status', 'created_at', 'updated_at'];
}
