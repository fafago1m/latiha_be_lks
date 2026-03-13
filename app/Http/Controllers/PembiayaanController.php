<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePembiayaanRequest;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use App\Models\PembiayaanRequest;
use App\Models\UmkmProfile;
use Illuminate\Support\Facades\Gate;
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
       
        if (!Gate::allows('manage-approval')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Anda bukan approver.'], 403);
        }

        $request->validate([
            'status_approval' => 'required|in:approved_tier_1,approved_tier_2,rejected'
        ]);

        $pembiayaan = PembiayaanRequest::findOrFail($id);
        
      
        $oldStatus = $pembiayaan->status_approval;
        $newStatus = $request->status_approval;

      
        $pembiayaan->status_approval = $newStatus;
        $pembiayaan->save();

        
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'Change Approval Status',
            'model_type' => 'PembiayaanRequest',
            'model_id' => $pembiayaan->id,
            'old_values' => ['status_approval' => $oldStatus],
            'new_values' => ['status_approval' => $newStatus],
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status pengajuan diperbarui menjadi: ' . $newStatus,
            'data' => $pembiayaan
        ]);
    }

    public function store(StorePembiayaanRequest $request) 
    {
        $umkm = UmkmProfile::findOrFail($request->umkm_profile_id);
        $maksimalPinjaman = $umkm->omzet_bulanan * 3;

        if ($request->nominal_pengajuan > $maksimalPinjaman) {
            return response()->json([
                'success' => false,
                'message' => 'Nominal pengajuan melebihi batas 3x omzet.',
                'errors' => ['nominal_pengajuan' => 'Maksimal: Rp ' . number_format($maksimalPinjaman, 0, ',', '.')]
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
            'message' => 'Pengajuan berhasil dibuat.',
            'data' => [
                'pengajuan' => $pembiayaan,
                'estimasi_cicilan' => round($cicilanPerBulan, 2)
            ]
        ], 201);
    }
}
