<?php

namespace App\Http\Controllers;

use App\Services\ReportDataService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function __construct(private ReportDataService $service) {}

    public function download(Request $request, string $report): Response
    {
        $reports = ReportDataService::reports();

        abort_unless(array_key_exists($report, $reports), 404);

        $meta = $reports[$report];

        // Authorization — the same permission that gates the dashboard
        abort_unless($request->user()?->can($meta['permission']), 403);

        $sections = $this->service->build($report);

        $pdf = Pdf::loadView('reports.pdf', [
            'title' => $meta['title'],
            'sections' => $sections,
            'generatedAt' => now(),
            'generatedBy' => $request->user()->name ?? 'System',
        ])->setPaper('a4', 'portrait');

        // Stamp "Page X of Y" centered at the bottom of every page
        $pdf->render();
        $canvas = $pdf->getCanvas();
        $font = $pdf->getDomPDF()->getFontMetrics()->getFont('DejaVu Sans', 'normal');
        $canvas->page_text(
            $canvas->get_width() / 2 - 28,
            $canvas->get_height() - 28,
            'Page {PAGE_NUM} of {PAGE_COUNT}',
            $font,
            8,
            [0.42, 0.45, 0.50]
        );

        $filename = str_replace('-', '_', $report).'_'.now()->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }
}
