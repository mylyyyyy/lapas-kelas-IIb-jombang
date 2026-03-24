<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfilPengunjung;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

use App\Exports\VisitorExport;
use App\Exports\FollowerExport;
use App\Models\Pengikut;
use Maatwebsite\Excel\Facades\Excel;

class VisitorController extends Controller
{
    public function index(Request $request)
    {
        $query = ProfilPengunjung::query()
            ->withCount('kunjungans')
            ->with(['kunjungans' => function($q) {
                $q->with('wbp')->latest('tanggal_kunjungan')->limit(1);
            }]);

        // 1. Filter Pencarian Nama/NIK
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'LIKE', "%{$search}%")
                  ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }

        // 2. Filter Wilayah (Alamat)
        if ($request->filled('wilayah')) {
            $query->where('alamat', 'LIKE', '%' . $request->wilayah . '%');
        }

        // 3. Filter Foto KTP
        if ($request->filled('has_foto')) {
            if ($request->has_foto === 'yes') {
                $query->whereHas('kunjungans', function($q) {
                    $q->whereNotNull('foto_ktp');
                });
            } else {
                $query->whereDoesntHave('kunjungans', function($q) {
                    $q->whereNotNull('foto_ktp');
                });
            }
        }

        // 4. Pengurutan
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'latest_visit':
                $query->leftJoin('kunjungans', function($join) {
                    $join->on('profil_pengunjungs.nik', '=', 'kunjungans.nik_ktp')
                        ->whereRaw('kunjungans.id IN (select MAX(id) from kunjungans group by nik_ktp)');
                })
                ->select('profil_pengunjungs.*')
                ->orderBy('kunjungans.tanggal_kunjungan', 'desc');
                break;
            case 'most_visited':
                $query->orderBy('kunjungans_count', 'desc');
                break;
            case 'oldest':
                $query->oldest();
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $visitors = $query->paginate(10);
        $visitors->appends($request->all());

        $visitors->getCollection()->transform(function ($visitor) {
            $latestKunjungan = $visitor->kunjungans->first();
            $visitor->foto_ktp = $visitor->image ?: ($latestKunjungan ? $latestKunjungan->foto_ktp : null);
            $visitor->total_kunjungan = $visitor->kunjungans_count;
            $visitor->last_visit = $latestKunjungan ? $latestKunjungan->tanggal_kunjungan : null;
            $visitor->last_wbp = $latestKunjungan && $latestKunjungan->wbp ? $latestKunjungan->wbp->nama : '-';
            return $visitor;
        });

        return view('admin.visitors.index', compact('visitors'));
    }

    public function followers(Request $request)
    {
        $query = Pengikut::query()
            ->select('pengikuts.*')
            ->join('kunjungans', 'pengikuts.kunjungan_id', '=', 'kunjungans.id')
            ->orderBy('pengikuts.nama');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('pengikuts.nama', 'LIKE', "%{$search}%")
                  ->orWhere('pengikuts.nik', 'LIKE', "%{$search}%");
            });
        }

        $allFollowers = $query->get()->unique(function($item) {
            return ($item->nik ?: $item->nama);
        });

        $perPage = 15;
        $page = $request->get('page', 1);
        $followers = new \Illuminate\Pagination\LengthAwarePaginator(
            $allFollowers->forPage($page, $perPage),
            $allFollowers->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.visitors.followers', compact('followers'));
    }

    public function exportFollowersExcel()
    {
        return Excel::download(new FollowerExport, 'database_pengikut_' . date('Ymd_His') . '.xlsx');
    }

    public function exportFollowersPdf()
    {
        $followers = Pengikut::select('nama', 'nik', 'hubungan', 'barang_bawaan', 'created_at')
            ->orderBy('nama')
            ->get()
            ->unique(function ($item) {
                return ($item->nik ?: $item->nama);
            });

        return view('admin.visitors.followers-pdf', compact('followers'));
    }

    public function destroy(ProfilPengunjung $visitor)
    {
        DB::transaction(function() use ($visitor) {
            // Hapus kunjungan & pengikut terkait (berdasarkan NIK karena relasi string)
            $kunjunganIds = Kunjungan::where('nik_ktp', $visitor->nik)->pluck('id');
            Pengikut::whereIn('kunjungan_id', $kunjunganIds)->delete();
            Kunjungan::whereIn('id', $kunjunganIds)->delete();
            
            $visitor->delete();
        });

        return back()->with('success', 'Data pengunjung dan riwayat terkait berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Pilih data yang ingin dihapus terlebih dahulu.');
        }

        DB::transaction(function() use ($ids) {
            $niks = ProfilPengunjung::whereIn('id', $ids)->pluck('nik');
            $kunjunganIds = Kunjungan::whereIn('nik_ktp', $niks)->pluck('id');
            
            Pengikut::whereIn('kunjungan_id', $kunjunganIds)->delete();
            Kunjungan::whereIn('id', $kunjunganIds)->delete();
            ProfilPengunjung::whereIn('id', $ids)->delete();
        });

        return back()->with('success', count($ids) . ' data pengunjung dan riwayat terkait berhasil dihapus.');
    }

    public function deleteAll()
    {
        DB::transaction(function() {
            Pengikut::query()->delete();
            Kunjungan::query()->delete();
            ProfilPengunjung::query()->delete();
        });

        return back()->with('success', 'Seluruh data pengunjung dan riwayat telah berhasil dikosongkan.');
    }

    public function bulkDestroyFollowers(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Pilih data yang ingin dihapus terlebih dahulu.');
        }

        // Karena satu orang pengikut bisa punya banyak record di tabel pengikuts (tiap kunjungan),
        // kita hapus berdasarkan Nama/NIK yang dipilih.
        $followersToDelete = Pengikut::whereIn('id', $ids)->get();
        
        DB::transaction(function() use ($followersToDelete) {
            foreach ($followersToDelete as $f) {
                if ($f->nik) {
                    Pengikut::where('nik', $f->nik)->delete();
                } else {
                    Pengikut::where('nama', $f->nama)->delete();
                }
            }
        });

        return back()->with('success', 'Data pengikut terpilih berhasil dihapus dari database.');
    }

    public function deleteAllFollowers()
    {
        Pengikut::query()->delete();
        return back()->with('success', 'Seluruh data pengikut telah berhasil dikosongkan.');
    }

    public function exportPdf()
    {
        $visitors = ProfilPengunjung::with('kunjungans.wbp')->get();
        return view('admin.visitors.pdf', compact('visitors'));
    }

    public function exportExcel()
    {
        return Excel::download(new VisitorExport, 'database_pengunjung_' . date('Ymd_His') . '.xlsx');
    }

    public function deleteOldVisitors()
    {
        $count = ProfilPengunjung::where('created_at', '<', now()->subMonth())->delete();
        Log::info("Auto-cleanup: Deleted $count visitor profiles older than 1 month.");
        return $count;
    }

    public function getHistory($id)
    {
        $visitor = ProfilPengunjung::select('id', 'nik', 'nama', 'nomor_hp', 'email', 'alamat', 'rt', 'rw', 'desa', 'kecamatan', 'kabupaten', 'jenis_kelamin', 'created_at')
            ->findOrFail($id);
            
        $history = Kunjungan::where('nik_ktp', $visitor->nik)
            ->select('id', 'nik_ktp', 'wbp_id', 'tanggal_kunjungan', 'status', 'barang_bawaan', 'sesi', 'nomor_antrian_harian')
            ->with(['wbp:id,nama,no_registrasi', 'pengikuts:id,kunjungan_id,nama,nik,hubungan,barang_bawaan,foto_ktp'])
            ->latest('tanggal_kunjungan')
            ->get();

        // Map foto_ktp_url into pengikuts
        $history->each(function($kunjungan) {
            $kunjungan->pengikuts->each(function($p) {
                $p->foto_ktp_url = $p->foto_ktp_url; // Use the accessor
            });
        });

        return response()->json([
            'visitor' => $visitor,
            'history' => $history
        ]);
    }

    public function exportCsv()
    {
        $filename = "database_pengunjung_" . date('Ymd_His') . ".csv";
        
        $response = new StreamedResponse(function() {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['ID', 'NIK', 'Nama', 'Jenis Kelamin', 'Nomor HP', 'Email', 'Alamat', 'Dibuat Pada']);

            ProfilPengunjung::chunk(200, function($visitors) use ($handle) {
                foreach ($visitors as $visitor) {
                    fputcsv($handle, [
                        $visitor->id,
                        $visitor->nik,
                        $visitor->nama,
                        $visitor->jenis_kelamin,
                        $visitor->nomor_hp,
                        $visitor->email,
                        $visitor->alamat,
                        $visitor->created_at
                    ]);
                }
            });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}
