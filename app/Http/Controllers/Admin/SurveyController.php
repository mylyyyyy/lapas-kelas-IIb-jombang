<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurveyController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getSurveyData($request);
        return view('admin.surveys.index', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getSurveyData($request, false);
        return view('admin.surveys.export_pdf', $data);
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getSurveyData($request, false);
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SurveyExport($data), 
            'laporan_survei_ikm_' . date('Ymd_His') . '.xlsx'
        );
    }

    protected function getSurveyData(Request $request, $paginate = true)
    {
        $query = Survey::latest();

        if ($request->filled('search')) {
            $query->where('saran', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }
        
        $surveys = $paginate ? $query->paginate(10)->withQueryString() : $query->get();

        // Data for Charts
        $stats = DB::table('surveys')
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as sangat_baik'),
                DB::raw('SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as baik'),
                DB::raw('SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as cukup'),
                DB::raw('SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as buruk')
            )
            ->first();

        // Handle case where there are no surveys yet
        if (!$stats->total > 0) {
            $stats = (object)[
                'total' => 0,
                'sangat_baik' => 0,
                'baik' => 0,
                'cukup' => 0,
                'buruk' => 0,
            ];
        }

        $averageRating = DB::table('surveys')->avg('rating') ?: 0;

        return compact('surveys', 'stats', 'averageRating');
    }

    public function destroy(Survey $survey)
    {
        $survey->delete();
        return redirect()->route('admin.surveys.index')->with('success', 'Data survey berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }

        Survey::whereIn('id', $ids)->delete();
        return redirect()->route('admin.surveys.index')->with('success', count($ids) . ' data survey berhasil dihapus.');
    }

    public function deleteAll()
    {
        Survey::query()->delete();
        return redirect()->route('admin.surveys.index')->with('success', 'Semua data survey berhasil dikosongkan.');
    }
}
