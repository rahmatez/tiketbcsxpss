<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\TicketScan;
use Illuminate\Support\Facades\Auth;

class QrScanController extends Controller
{
    public function processQrCode(Request $request)
    {
        try {
            // Log request untuk debugging
            Log::info('QR scan test called', ['data' => $request->all()]);
            
            // Validasi input
            $request->validate([
                'qr_code' => 'required|string'
            ]);
            
            $qrCode = $request->input('qr_code');
            
            // Hanya kembalikan response sukses untuk testing
            return response()->json([
                'success' => true,
                'message' => 'Test sukses: QR code diterima',
                'qr_code' => $qrCode
            ]);
            
        } catch (\Exception $e) {
            Log::error('QR scan test error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error pada test scan: ' . $e->getMessage()
            ], 500);
        }
    }
}
