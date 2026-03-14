<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UmkmProfile extends Model
{
    use SoftDeletes;
    protected $keyType = 'string';
    public $incrementing = false;

     protected $fillable = [
        'id',
        'user_id',
        'nama_usaha',
        'alamat',
        'omzet_bulanan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    //
}
