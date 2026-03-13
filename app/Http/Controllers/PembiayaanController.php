<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PembiayaanRequest;
use App\Models\UmkmProfile;
use Illuminate\Support\Str;

class PembiayaanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'umkm_profile_id' => 'required|exists:umkm_profiles,id',
            'nominal_pengajuan' => 'required|numeric|min:1000000',
            'tenor_bulan' => 'required|integer|min:1',
            'bunga_persen' => 'required|numeric|min:0'
        ]);

        $umkm = UmkmProfile::findOrFail($request->umkm_profile_id);

      //flidsi uomset nya 3
        $maksimalPinjaman = $umkm->omzet_bulanan * 3;
        if ($request->nominal_pengajuan > $maksimalPinjaman) {
            return response()->json([
                'success' => false,
                'message' => 'Nominal pengajuan melebihi batas 3x omzet bulanan.',
                'errors' => ['nominal_pengajuan' => 'Maksimal pengajuan adalah Rp ' . number_format($maksimalPinjaman, 0, ',', '.')]
            ], 422);
        }


        $P = $request->nominal_pengajuan;
        $r = ($request->bunga_persen / 100) / 12; 
        $n = $request->tenor_bulan;
        
      
        $cicilanPerBulan = $P * ($r * pow(1 + $r, $n)) / (pow(1 + $r, $n) - 1);

        $pembiayaan = PembiayaanRequest::create([
            'id' => Str::uuid(),
            'umkm_profile_id' => $umkm->id,
            'nominal_pengajuan' => $P,
            'tenor_bulan' => $n,
            'bunga_persen' => $request->bunga_persen,
            'status_approval' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan berhasil dibuat dan sedang direview.',
            'data' => [
                'pengajuan' => $pembiayaan,
                'estimasi_cicilan_per_bulan' => round($cicilanPerBulan, 2)
            ]
        ], 201);
    }
}
