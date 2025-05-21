<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PdfTemplate;
use App\Models\Order;
use Illuminate\Http\Request;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class PdfTemplateController extends Controller
{
    public function index()
    {
        $templates = PdfTemplate::all();
        return view('admin.pdf_templates.index', compact('templates'));
    }
    
    public function create()
    {
        return view('admin.pdf_templates.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'html_content' => 'required|string',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean'
        ]);
        
        $validated['is_default'] = $request->has('is_default') ? 1 : 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        
        $template = PdfTemplate::create($validated);
        
        if ($request->has('is_default') && $request->is_default) {
            $template->setAsDefault();
        }
        
        return redirect()->route('admin.pdf-templates.index')
            ->with('success', 'Template berhasil dibuat');
    }
    
    public function edit(PdfTemplate $pdfTemplate)
    {
        return view('admin.pdf_templates.edit', ['template' => $pdfTemplate]);
    }
    
    public function update(Request $request, PdfTemplate $pdfTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'html_content' => 'required|string',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean'
        ]);
        
        $validated['is_default'] = $request->has('is_default') ? 1 : 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        
        $pdfTemplate->update($validated);
        
        if ($request->has('is_default') && $request->is_default) {
            $pdfTemplate->setAsDefault();
        }
        
        return redirect()->route('admin.pdf-templates.index')
            ->with('success', 'Template berhasil diperbarui');
    }
    
    public function destroy(PdfTemplate $pdfTemplate)
    {
        // Don't allow deletion of default template
        if ($pdfTemplate->is_default) {
            return redirect()->route('admin.pdf-templates.index')
                ->with('error', 'Template default tidak dapat dihapus');
        }
        
        $pdfTemplate->delete();
        return redirect()->route('admin.pdf-templates.index')
            ->with('success', 'Template berhasil dihapus');
    }
    
    public function setDefault(PdfTemplate $pdfTemplate)
    {
        $pdfTemplate->setAsDefault();
        return redirect()->route('admin.pdf-templates.index')
            ->with('success', 'Template berhasil diatur sebagai default');
    }
    
    public function preview(PdfTemplate $pdfTemplate)
    {
        // Get any order for preview (preferably a paid one)
        $order = Order::where('status', 'paid')->first() ?? Order::first();
        
        if (!$order) {
            return redirect()->route('admin.pdf-templates.index')
                ->with('error', 'Tidak dapat membuat preview karena tidak ada tiket');
        }
        
        // Generate dummy QR code if needed
        if (empty($order->qr_code)) {
            $order->qr_code = $order->id . '-preview';
        }
        
        // Generate the QR code
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($order->qr_code);
        
        // Generate PDF using the service
        $pdfService = app(\App\Services\TicketPdfService::class);
        $pdf = $pdfService->generatePdf($order, $pdfTemplate);
        
        return $pdf->stream('preview-template-' . $pdfTemplate->id . '.pdf');
    }
}
