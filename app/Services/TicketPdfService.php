<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PdfTemplate;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Carbon\Carbon;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;

class TicketPdfService
{
    public function generatePdf(Order $order, ?PdfTemplate $template = null)
    {
        // If no template provided, get default template
        $template = $template ?? PdfTemplate::getDefault();
          // Generate QR code
        $renderer = new ImageRenderer(
            new RendererStyle(500),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($order->qr_code);        // Convert SVG QR code to an HTML img tag
        $svgQrCode = 'data:image/svg+xml;base64,' . base64_encode($qrCode);
        $imgTag = '<img src="' . $svgQrCode . '" style="width: 200px; height: 200px; border: none;">';
        
        // Prepare data for PDF
        $data = [
            'order' => $order,
            'qr_code' => $imgTag,
            'baseUrl' => url('/')
        ];
        
        // Replace placeholders in template
        $html = $this->parsePlaceholders($template->html_content, $data);
        
        // Generate PDF with Snappy
        $pdf = PDF::loadHTML($html);
          // Set PDF options
        $pdf->setOption('encoding', 'UTF-8');
        $pdf->setOption('page-size', 'A4');
        $pdf->setOption('orientation', 'Portrait');
        $pdf->setOption('margin-top', '10mm');
        $pdf->setOption('margin-right', '10mm');
        $pdf->setOption('margin-bottom', '10mm');
        $pdf->setOption('margin-left', '10mm');
          // Enable image loading and disable network requests
        $pdf->setOption('enable-local-file-access', true);
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('javascript-delay', 1000);
        $pdf->setOption('no-stop-slow-scripts', true);
        $pdf->setOption('enable-smart-shrinking', true);
        
        // Disable external resources to prevent network errors
        $pdf->setOption('disable-external-links', true);
        $pdf->setOption('no-outline', true);
        $pdf->setOption('load-error-handling', 'ignore');
        $pdf->setOption('load-media-error-handling', 'ignore');
        
        return $pdf;
    }
      private function parsePlaceholders($html, $data)
    {
        // Replace placeholders with actual data
        $order = $data['order'];
        $placeholders = [
            '{ORDER_ID}' => $order->id,
            '{USER_NAME}' => $order->user->name,
            '{MATCH_TEAMS}' => $order->game->home_team . ' vs ' . $order->game->away_team,
            '{MATCH_DATE}' => Carbon::parse($order->game->match_time)->format('d M Y'),
            '{MATCH_TIME}' => Carbon::parse($order->game->match_time)->format('H:i'),
            '{STADIUM}' => $order->game->stadium_name,
            '{SEAT_CATEGORY}' => $order->ticket->category,
            '{QUANTITY}' => $order->quantity,
            '{PURCHASE_DATE}' => Carbon::parse($order->created_at)->format('d M Y H:i'),
            '{QR_CODE}' => $data['qr_code'],
            '{TICKET_STATUS}' => ucfirst($order->status),
            '{BASE_URL}' => $data['baseUrl'],
            '{TICKET_PRICE}' => 'Rp ' . number_format($order->ticket->price, 0, ',', '.')
        ];
        
        return str_replace(array_keys($placeholders), array_values($placeholders), $html);
    }
}
