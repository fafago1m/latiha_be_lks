<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PembiayaanRequest;
use App\Models\UmkmProfile;
use Illuminate\Support\Str;

class PembiayaanController extends Controller
{
    public function index(Request $request)
    {
        //  hanya role approver/admin yang bisa akse
        if ($request->user()->role !== 'approver') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        // Ambil data besserta relasi profil UMKM
        $pengajuan = PembiayaanRequest::with(['umkmProfile.user'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Data pengajuan berhasil diambil',
            'data' => $pengajuan
        ]);
    }

    //Mengubah status approval (setujui /Tolak)
    public function updateStatus(Request $request, $id)
    {
        if ($request->user()->role !== 'approver') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'status_approval' => 'required|in:approved_tier_1,approved_tier_2,rejected'
        ]);

        $pembiayaan = PembiayaanRequest::findOrFail($id);
        $pembiayaan->status_approval = $request->status_approval;
        $pembiayaan->save();

        return response()->json([
            'success' => true,
            'message' => 'Status pengajuan berhasil diperbarui menjadi: ' . $request->status_approval,
            'data' => $pembiayaan
        ]);
    }

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
