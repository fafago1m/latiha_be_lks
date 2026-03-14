<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PembiayaanRequest extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'id',
        'umkm_profile_id',
        'nominal_pengajuan',
        'tenor_bulan',
        'bunga_persen',
        'status_approval'
    ];

    public function umkmProfile()
    {
        return $this->belongsTo(UmkmProfile::class);
    }
}
