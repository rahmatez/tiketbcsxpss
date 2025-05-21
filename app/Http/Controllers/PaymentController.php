<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentController extends Controller
{    
    /**
     * Membuat transaksi pembayaran baru dengan Midtrans Snap
     */    public function createTransaction(Order $order)
    {
        // Validasi bahwa order belum dibayar
        if ($order->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Order ini sudah diproses'
            ], 422);
        }
        
        try {
            // Validasi konfigurasi Midtrans
            $serverKey = config('midtrans.server_key');
            $clientKey = config('midtrans.client_key');
            
            if (empty($serverKey) || empty($clientKey)) {
                throw new \Exception('Midtrans API keys belum dikonfigurasi dengan benar. Silahkan hubungi administrator.');
            }
            
            // Pastikan Midtrans config diatur kembali untuk request ini
            \Midtrans\Config::$serverKey = $serverKey;
            \Midtrans\Config::$isProduction = !config('midtrans.sandbox');
            \Midtrans\Config::$isSanitized = config('midtrans.sanitize', true);
            \Midtrans\Config::$is3ds = config('midtrans.enable_3d_secure', true);
            
            $user = User::find($order->user_id);
              // Buat parameter untuk Midtrans Snap
            $params = [
                'transaction_details' => [
                    'order_id' => 'BCSXPSS-' . $order->id . '-' . time(),
                    'gross_amount' => $order->ticket->price * $order->quantity,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone_number ?? '',
                ],
                'item_details' => [
                    [
                        'id' => $order->ticket_id,
                        'price' => $order->ticket->price,
                        'quantity' => $order->quantity,
                        'name' => 'Tiket ' . $order->ticket->category . ' ' . $order->game->home_team . ' vs ' . $order->game->away_team,
                    ]
                ],
                'callbacks' => [
                    'finish' => route('payment.finish'),
                    'error' => route('payment.error'),
                    'unfinish' => route('payment.unfinish')
                ]
            ];
            
            Log::info('Midtrans Params: ' . json_encode($params));
            
            // Dapatkan token pembayaran dari Midtrans
            $snapToken = Snap::getSnapToken($params);
            
            // Simpan token dan order ID Midtrans ke dalam order
            $order->payment_token = $snapToken;
            $order->midtrans_order_id = $params['transaction_details']['order_id'];
            $order->save();
            
            return response()->json([
                'status' => 'success',
                'snap_token' => $snapToken,
                'redirect_url' => config('midtrans.snap_redirect') ? Snap::getSnapUrl($snapToken) : null,
            ]);
            
        } catch (Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menangani notifikasi pembayaran dari Midtrans
     */    public function handleNotification(Request $request)
    {
        try {
            // Log the raw notification data for debugging
            Log::info('Midtrans Raw Notification: ' . json_encode($request->all()));
            
            // Create Midtrans notification object
            $notificationBody = file_get_contents('php://input');
            Log::info('Midtrans Notification Raw Body: ' . $notificationBody);
            
            $notification = new Notification();
            
            // Ambil informasi pesanan dari kode transaksi Midtrans
            $orderId = $notification->order_id;
            $status = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status;
            $paymentType = $notification->payment_type;
              Log::info('Midtrans Notification Object: ' . json_encode([
                'order_id' => $orderId,
                'transaction_status' => $status,
                'fraud_status' => $fraudStatus,
                'payment_type' => $paymentType
            ]));
            
            // Format order_id dari Midtrans adalah BCSXPSS-{order_id}-{timestamp}
            // Ekstrak ID order asli
            $orderIdParts = explode('-', $orderId);
            $realOrderId = $orderIdParts[1] ?? null;
            
            if (!$realOrderId) {
                Log::error('Invalid order ID format: ' . $orderId);
                return response()->json(['status' => 'error', 'message' => 'Invalid order ID format'], 400);
            }
            
            // Ambil data pesanan
            $order = Order::find($realOrderId);
            
            if (!$order) {
                Log::error('Order not found: ' . $realOrderId);
                return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
            }
            
            // Update status order menggunakan helper method
            $this->updateOrderStatus($order, $status, $fraudStatus, $paymentType);
            
            // Log the success with detailed information
            Log::info('Midtrans Notification Processed: Order #' . $realOrderId . ' updated to ' . $order->status);
            
            return response()->json(['status' => 'success']);
            
        } catch (Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Handle pembayaran selesai - halaman sukses
     */    public function finishPayment(Request $request)
    {
        // Ambil parameter dari redirect Midtrans
        $orderId = $request->order_id;
        $status = $request->transaction_status;
        $fraudStatus = $request->fraud_status;
          // Log semua data yang diterima dari Midtrans untuk debugging
        Log::info('Midtrans Finish Payment Data', $request->all());
        
        // Format order_id dari Midtrans adalah BCSXPSS-{order_id}-{timestamp}
        // Ekstrak ID order asli
        $orderIdParts = explode('-', $orderId);
        $realOrderId = $orderIdParts[1] ?? null;
        
        if (!$realOrderId) {
            Log::error('Invalid order ID format from Midtrans: ' . $orderId);
            return redirect()->route('home')
                ->with('error', 'Terjadi kesalahan dalam memproses pembayaran. Silahkan hubungi administrator.');
        }
        
        // Coba ambil order dari database
        $order = Order::find($realOrderId);
        
        if (!$order) {
            Log::error('Order not found with ID: ' . $realOrderId);
            return redirect()->route('home')
                ->with('error', 'Order tidak ditemukan. Silahkan hubungi administrator.');
        }
        
        // Jika tidak ada status dari request, cek status langsung dari Midtrans
        if (!$status && $order->midtrans_order_id) {
            try {
                Log::info('Checking status from Midtrans API for order: ' . $order->midtrans_order_id);
                $midtransStatus = \Midtrans\Transaction::status($order->midtrans_order_id);
                
                // Update variabel status dan fraudStatus dari hasil API
                $status = $midtransStatus->transaction_status ?? null;
                $fraudStatus = $midtransStatus->fraud_status ?? null;
                
                Log::info('Retrieved status from Midtrans API: ' . $status);
            } catch (\Exception $e) {
                Log::error('Error checking Midtrans status: ' . $e->getMessage());
            }
        }
        
        // Update status order berdasarkan parameter dari Midtrans
        if ($status == 'capture' || $status == 'settlement') {
            if ($fraudStatus == 'challenge') {
                $order->status = 'challenge';
            } else {
                $order->status = 'paid';
                $this->generateQRCode($order);
            }
            
            // Jika payment_method belum diisi, isi dengan informasi dari request
            if ($order->payment_method == 'pending' && $request->payment_type) {
                $order->payment_method = $request->payment_type;
            }
            
            $order->save();
            
            // Log informasi update status
            Log::info('Status order #' . $realOrderId . ' diupdate menjadi: ' . $order->status . ' dari halaman finish payment');
        } else if ($status && $status != 'pending') {
            // Jika ada status dari Midtrans dan bukan pending, update juga
            $this->updateOrderStatus($order, $status, $fraudStatus, $request->payment_type ?? null);
        } else {
            // Jika tidak ada status yang jelas atau masih pending, coba cek satu kali lagi
            try {
                Log::info('Final check status from Midtrans API for order: ' . $order->midtrans_order_id);
                $midtransStatus = \Midtrans\Transaction::status($order->midtrans_order_id);
                $transactionStatus = $midtransStatus->transaction_status ?? null;
                
                if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                    $order->status = 'paid';
                    $this->generateQRCode($order);
                    $order->save();
                    Log::info('Status updated after final check: ' . $order->status);
                }
            } catch (\Exception $e) {
                Log::error('Error in final Midtrans status check: ' . $e->getMessage());
            }
        }
        
        return redirect()->route('orders.show', $realOrderId)
            ->with('payment_status', $status)
            ->with('message', 'Terima kasih! Status pembayaran Anda: ' . $this->getStatusMessage($status, $fraudStatus));
    }
    
    /**
     * Handle pembayaran belum selesai
     */
    public function unfinishPayment(Request $request)
    {
        $orderId = $request->order_id;
        $orderIdParts = explode('-', $orderId);
        $realOrderId = $orderIdParts[1];
        
        return redirect()->route('orders.show', $realOrderId)
            ->with('payment_status', 'unfinish')
            ->with('message', 'Pembayaran belum selesai. Silahkan cek status pembayaran Anda.');
    }
    
    /**
     * Handle pembayaran error
     */
    public function errorPayment(Request $request)
    {
        $orderId = $request->order_id;
        $orderIdParts = explode('-', $orderId);
        $realOrderId = $orderIdParts[1];
        
        return redirect()->route('orders.show', $realOrderId)
            ->with('payment_status', 'error')
            ->with('message', 'Terjadi kesalahan dalam pemrosesan pembayaran. Silahkan coba lagi.');
    }
    
    /**
     * Cek status pembayaran secara manual
     */    public function checkStatus(Order $order)
    {        try {
            // Use the midtrans_order_id if available, otherwise construct it
            $orderId = $order->midtrans_order_id ?? ('BCSXPSS-' . $order->id . '-' . strtotime($order->created_at));
            
            Log::info('Checking Midtrans status for order: ' . $orderId);
            
            // Using Midtrans library to check transaction status
            $status = \Midtrans\Transaction::status($orderId);
            
            Log::info('Midtrans Status Response: ' . json_encode($status));
            
            // Extract status information based on response format
            $transactionStatus = null;
            $fraudStatus = null;
            $paymentType = null;
            
            if (is_object($status)) {
                $transactionStatus = $status->transaction_status ?? null;
                $fraudStatus = $status->fraud_status ?? null;
                $paymentType = $status->payment_type ?? null;
            } elseif (is_array($status)) {
                $transactionStatus = $status['transaction_status'] ?? null;
                $fraudStatus = $status['fraud_status'] ?? null;
                $paymentType = $status['payment_type'] ?? null;
            }
            
            if (!$transactionStatus) {
                Log::warning('No transaction status found in Midtrans response');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak dapat membaca status transaksi dari Midtrans'
                ], 400);
            }
            
            // Update order status using helper method
            $this->updateOrderStatus($order, $transactionStatus, $fraudStatus, $paymentType);
            
            return response()->json([
                'status' => 'success', 
                'message' => $this->getStatusMessage($transactionStatus, $fraudStatus),
                'order_status' => $order->status,
                'transaction_status' => $transactionStatus
            ]);
            
        } catch (\Exception $e) {
            Log::error('Midtrans Status Check Error: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            
            return response()->json([
                'status' => 'error', 
                'message' => 'Gagal memeriksa status pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Generate pesan status berdasarkan status transaksi
     */
    private function getStatusMessage($status, $fraud = null)
    {
        $message = '';
        
        switch ($status) {
            case 'capture':
                $message = $fraud == 'challenge' ? 'Pembayaran menunggu konfirmasi' : 'Pembayaran berhasil';
                break;
            case 'settlement':
                $message = 'Pembayaran berhasil';
                break;
            case 'pending':
                $message = 'Pembayaran belum selesai';
                break;
            case 'deny':
                $message = 'Pembayaran ditolak';
                break;
            case 'expire':
                $message = 'Pembayaran kedaluwarsa';
                break;
            case 'cancel':
                $message = 'Pembayaran dibatalkan';
                break;
            default:
                $message = 'Status tidak diketahui';
                break;
        }
        
        return $message;
    }
    
    /**
     * Generate QR Code untuk tiket
     */
    private function generateQRCode(Order $order)
    {
        // Generate hash berdasarkan email pengguna dan order ID
        $hash = substr(md5($order->user->email . $order->id), 0, 8);
        $qrText = $order->id . '-' . $hash;
        
        // Simpan kode QR sebagai teks (bisa digunakan untuk generate QR di frontend)
        $order->qr_code = $qrText;
        $order->save();
        
        return true;
    }
    
    /**
     * Menampilkan detail status pembayaran
     */
    public function paymentDetail(Order $order)
    {
        // Validasi akses pengguna ke data pembayaran ini
        if (auth()->id() !== $order->user_id && !auth()->guard('admin')->check()) {
            abort(403, 'Unauthorized access');
        }
        
        // Ambil data order dengan relasi yang dibutuhkan
        $order->load(['user', 'game', 'ticket']);
        
        // Coba ambil data dari Midtrans jika order memiliki midtrans_order_id
        $midtransData = null;
        $paymentSteps = [];
        $paymentHistory = [];
        
        if ($order->midtrans_order_id) {
            try {
                $midtransData = \Midtrans\Transaction::status($order->midtrans_order_id);
                
                // Log data Midtrans untuk debug
                Log::info('Midtrans Data for Order #' . $order->id, (array) $midtransData);
                
                // Ambil riwayat status pembayaran jika ada
                if (is_object($midtransData) && isset($midtransData->status_code) && $midtransData->status_code == '200') {
                    // Olah data transaksi untuk ditampilkan
                    // Ambil langkah-langkah pembayaran berdasarkan metode
                    $paymentSteps = $this->getPaymentSteps($midtransData);
                    
                    // Riwayat status pembayaran
                    if (isset($midtransData->transaction_time)) {
                        $paymentHistory[] = [
                            'date' => $midtransData->transaction_time,
                            'status' => 'Order dibuat',
                            'description' => 'Pembayaran dibuat melalui ' . $this->formatPaymentMethod($midtransData->payment_type)
                        ];
                    }
                    
                    // Tambahkan status settlement jika sudah dibayar
                    if (isset($midtransData->transaction_status) && in_array($midtransData->transaction_status, ['capture', 'settlement'])) {
                        $paymentHistory[] = [
                            'date' => $midtransData->settlement_time ?? date('Y-m-d H:i:s'),
                            'status' => 'Pembayaran berhasil',
                            'description' => 'Dana berhasil diterima'
                        ];
                    }
                    
                    // Tambahkan status lain jika diperlukan
                    if (isset($midtransData->transaction_status)) {
                        if ($midtransData->transaction_status === 'expire') {
                            $paymentHistory[] = [
                                'date' => date('Y-m-d H:i:s', strtotime($order->updated_at)),
                                'status' => 'Pembayaran kedaluwarsa',
                                'description' => 'Batas waktu pembayaran telah habis'
                            ];
                        } elseif ($midtransData->transaction_status === 'cancel') {
                            $paymentHistory[] = [
                                'date' => date('Y-m-d H:i:s', strtotime($order->updated_at)),
                                'status' => 'Pembayaran dibatalkan',
                                'description' => 'Transaksi dibatalkan'
                            ];
                        } elseif ($midtransData->transaction_status === 'deny') {
                            $paymentHistory[] = [
                                'date' => date('Y-m-d H:i:s', strtotime($order->updated_at)),
                                'status' => 'Pembayaran ditolak',
                                'description' => 'Transaksi ditolak oleh sistem'
                            ];
                        }
                    }
                } elseif (is_array($midtransData) && isset($midtransData['status_code']) && $midtransData['status_code'] == '200') {
                    // Olah data transaksi untuk ditampilkan dalam format array
                    $paymentSteps = $this->getPaymentStepsFromArray($midtransData);
                    
                    // Riwayat status pembayaran
                    if (isset($midtransData['transaction_time'])) {
                        $paymentHistory[] = [
                            'date' => $midtransData['transaction_time'],
                            'status' => 'Order dibuat',
                            'description' => 'Pembayaran dibuat melalui ' . $this->formatPaymentMethod($midtransData['payment_type'])
                        ];
                    }
                    
                    // Tambahkan status settlement jika sudah dibayar
                    if (isset($midtransData['transaction_status']) && in_array($midtransData['transaction_status'], ['capture', 'settlement'])) {
                        $paymentHistory[] = [
                            'date' => $midtransData['settlement_time'] ?? date('Y-m-d H:i:s'),
                            'status' => 'Pembayaran berhasil',
                            'description' => 'Dana berhasil diterima'
                        ];
                    }
                    
                    // Tambahkan status lain jika diperlukan
                    if (isset($midtransData['transaction_status'])) {
                        if ($midtransData['transaction_status'] === 'expire') {
                            $paymentHistory[] = [
                                'date' => date('Y-m-d H:i:s', strtotime($order->updated_at)),
                                'status' => 'Pembayaran kedaluwarsa',
                                'description' => 'Batas waktu pembayaran telah habis'
                            ];
                        } elseif ($midtransData['transaction_status'] === 'cancel') {
                            $paymentHistory[] = [
                                'date' => date('Y-m-d H:i:s', strtotime($order->updated_at)),
                                'status' => 'Pembayaran dibatalkan',
                                'description' => 'Transaksi dibatalkan'
                            ];
                        } elseif ($midtransData['transaction_status'] === 'deny') {
                            $paymentHistory[] = [
                                'date' => date('Y-m-d H:i:s', strtotime($order->updated_at)),
                                'status' => 'Pembayaran ditolak',
                                'description' => 'Transaksi ditolak oleh sistem'
                            ];
                        }
                    }
                }
                
            } catch (\Exception $e) {
                Log::error('Error fetching Midtrans data: ' . $e->getMessage());
            }
        }
        
        // Render view
        return view('payment.detail', [
            'order' => $order,
            'midtransData' => $midtransData,
            'paymentSteps' => $paymentSteps,
            'paymentHistory' => $paymentHistory
        ]);
    }
    
    /**
     * Mendapatkan langkah-langkah pembayaran berdasarkan metode pembayaran (object)
     */
    private function getPaymentSteps($midtransData)
    {
        if (is_array($midtransData)) {
            return $this->getPaymentStepsFromArray($midtransData);
        }
        
        $steps = [];
        
        // Pembayaran melalui Virtual Account
        if (isset($midtransData->payment_type) && in_array($midtransData->payment_type, ['bank_transfer', 'echannel'])) {
            $bankCode = '';
            $vaNumber = '';
            
            if ($midtransData->payment_type === 'bank_transfer') {
                if (isset($midtransData->va_numbers) && is_array($midtransData->va_numbers) && count($midtransData->va_numbers) > 0) {
                    $bankCode = $midtransData->va_numbers[0]->bank;
                    $vaNumber = $midtransData->va_numbers[0]->va_number;
                } elseif (isset($midtransData->permata_va_number)) {
                    $bankCode = 'permata';
                    $vaNumber = $midtransData->permata_va_number;
                }
            } elseif ($midtransData->payment_type === 'echannel') {
                $bankCode = 'mandiri';
                $vaNumber = $midtransData->bill_key;
            }
            
            $steps = [
                [
                    'step' => '1',
                    'title' => 'Akses Mobile/Internet Banking',
                    'description' => 'Masuk ke aplikasi atau website ' . strtoupper($bankCode) . ' Mobile/Internet Banking Anda'
                ],
                [
                    'step' => '2',
                    'title' => 'Pilih Menu Transfer/Pembayaran',
                    'description' => 'Pilih menu Transfer atau Pembayaran Virtual Account'
                ],
                [
                    'step' => '3',
                    'title' => 'Masukkan Nomor Virtual Account',
                    'description' => 'Masukkan nomor Virtual Account: <strong>' . $vaNumber . '</strong>'
                ],
                [
                    'step' => '4',
                    'title' => 'Konfirmasi Detail',
                    'description' => 'Periksa informasi pembayaran dan jumlah yang harus dibayar'
                ],
                [
                    'step' => '5',
                    'title' => 'Selesaikan Pembayaran',
                    'description' => 'Ikuti instruksi selanjutnya untuk menyelesaikan pembayaran'
                ]
            ];
        } 
        // Pembayaran melalui QRIS
        elseif (isset($midtransData->payment_type) && $midtransData->payment_type === 'qris') {
            $steps = [
                [
                    'step' => '1',
                    'title' => 'Buka Aplikasi E-Wallet',
                    'description' => 'Buka aplikasi e-wallet Anda (GoPay, OVO, DANA, LinkAja, dll)'
                ],
                [
                    'step' => '2',
                    'title' => 'Pilih Opsi Scan',
                    'description' => 'Pilih opsi scan atau bayar menggunakan QR code'
                ],
                [
                    'step' => '3',
                    'title' => 'Scan QR Code',
                    'description' => 'Scan QR code yang ditampilkan pada halaman pembayaran'
                ],
                [
                    'step' => '4',
                    'title' => 'Konfirmasi Pembayaran',
                    'description' => 'Konfirmasi jumlah yang akan dibayarkan'
                ],
                [
                    'step' => '5',
                    'title' => 'Selesaikan Pembayaran',
                    'description' => 'Masukkan PIN atau verifikasi sidik jari untuk menyelesaikan pembayaran'
                ]
            ];
        }
        // Pembayaran melalui GoPay
        elseif (isset($midtransData->payment_type) && $midtransData->payment_type === 'gopay') {
            $steps = [
                [
                    'step' => '1',
                    'title' => 'Buka Aplikasi Gojek',
                    'description' => 'Buka aplikasi Gojek di smartphone Anda'
                ],
                [
                    'step' => '2',
                    'title' => 'Pilih Bayar',
                    'description' => 'Pilih menu Bayar di aplikasi Gojek'
                ],
                [
                    'step' => '3',
                    'title' => 'Scan QR Code',
                    'description' => 'Scan QR code yang ditampilkan pada halaman pembayaran'
                ],
                [
                    'step' => '4',
                    'title' => 'Konfirmasi Pembayaran',
                    'description' => 'Konfirmasi jumlah yang akan dibayarkan'
                ],
                [
                    'step' => '5',
                    'title' => 'Selesaikan Pembayaran',
                    'description' => 'Masukkan PIN GoPay untuk menyelesaikan pembayaran'
                ]
            ];
        }
        // Pembayaran melalui kartu kredit
        elseif (isset($midtransData->payment_type) && $midtransData->payment_type === 'credit_card') {
            $steps = [
                [
                    'step' => '1',
                    'title' => 'Isi Detail Kartu Kredit',
                    'description' => 'Masukkan nomor kartu, tanggal kadaluarsa, dan CVV'
                ],
                [
                    'step' => '2',
                    'title' => 'Verifikasi 3D Secure',
                    'description' => 'Ikuti proses verifikasi 3D Secure jika kartu Anda terdaftar'
                ],
                [
                    'step' => '3',
                    'title' => 'Konfirmasi Pembayaran',
                    'description' => 'Cek kembali detail pembayaran Anda'
                ],
                [
                    'step' => '4',
                    'title' => 'Selesaikan Pembayaran',
                    'description' => 'Klik tombol bayar untuk menyelesaikan transaksi'
                ]
            ];
        }
        // Pembayaran melalui convenience store (Indomaret/Alfamart)
        elseif (isset($midtransData->payment_type) && in_array($midtransData->payment_type, ['cstore', 'convenience_store'])) {
            $storeCode = '';
            $paymentCode = '';
            
            if (isset($midtransData->store) && $midtransData->store === 'indomaret') {
                $storeCode = 'Indomaret';
                $paymentCode = $midtransData->payment_code;
            } elseif (isset($midtransData->store) && $midtransData->store === 'alfamart') {
                $storeCode = 'Alfamart/Alfa group';
                $paymentCode = $midtransData->payment_code;
            }
            
            $steps = [
                [
                    'step' => '1',
                    'title' => 'Kunjungi ' . $storeCode,
                    'description' => 'Datang ke ' . $storeCode . ' terdekat'
                ],
                [
                    'step' => '2',
                    'title' => 'Informasi Pembayaran',
                    'description' => 'Beritahu kasir bahwa Anda ingin melakukan pembayaran transaksi online'
                ],
                [
                    'step' => '3',
                    'title' => 'Berikan Kode Pembayaran',
                    'description' => 'Berikan kode pembayaran: <strong>' . $paymentCode . '</strong> kepada kasir'
                ],
                [
                    'step' => '4',
                    'title' => 'Lakukan Pembayaran',
                    'description' => 'Bayar sesuai dengan jumlah tagihan'
                ],
                [
                    'step' => '5',
                    'title' => 'Simpan Bukti Pembayaran',
                    'description' => 'Simpan bukti pembayaran/struk sebagai bukti transaksi'
                ]
            ];
        }
        
        return $steps;
    }
    
    /**
     * Mendapatkan langkah-langkah pembayaran berdasarkan metode pembayaran (array)
     */
    private function getPaymentStepsFromArray($midtransData)
    {
        $steps = [];
        
        // Pembayaran melalui Virtual Account
        if (isset($midtransData['payment_type']) && in_array($midtransData['payment_type'], ['bank_transfer', 'echannel'])) {
            $bankCode = '';
            $vaNumber = '';
            
            if ($midtransData['payment_type'] === 'bank_transfer') {
                if (isset($midtransData['va_numbers']) && is_array($midtransData['va_numbers']) && count($midtransData['va_numbers']) > 0) {
                    $bankCode = $midtransData['va_numbers'][0]['bank'];
                    $vaNumber = $midtransData['va_numbers'][0]['va_number'];
                } elseif (isset($midtransData['permata_va_number'])) {
                    $bankCode = 'permata';
                    $vaNumber = $midtransData['permata_va_number'];
                }
            } elseif ($midtransData['payment_type'] === 'echannel') {
                $bankCode = 'mandiri';
                $vaNumber = $midtransData['bill_key'];
            }
            
            $steps = [
                [
                    'step' => '1',
                    'title' => 'Akses Mobile/Internet Banking',
                    'description' => 'Masuk ke aplikasi atau website ' . strtoupper($bankCode) . ' Mobile/Internet Banking Anda'
                ],
                [
                    'step' => '2',
                    'title' => 'Pilih Menu Transfer/Pembayaran',
                    'description' => 'Pilih menu Transfer atau Pembayaran Virtual Account'
                ],
                [
                    'step' => '3',
                    'title' => 'Masukkan Nomor Virtual Account',
                    'description' => 'Masukkan nomor Virtual Account: <strong>' . $vaNumber . '</strong>'
                ],
                [
                    'step' => '4',
                    'title' => 'Konfirmasi Detail',
                    'description' => 'Periksa informasi pembayaran dan jumlah yang harus dibayar'
                ],
                [
                    'step' => '5',
                    'title' => 'Selesaikan Pembayaran',
                    'description' => 'Ikuti instruksi selanjutnya untuk menyelesaikan pembayaran'
                ]
            ];
        } 
        // Pembayaran melalui metode lainnya - gunakan metode yang sama seperti getPaymentSteps
        // Tapi menggunakan akses array
        elseif (isset($midtransData['payment_type'])) {
            switch($midtransData['payment_type']) {
                case 'qris':
                    $steps = [
                        ['step' => '1', 'title' => 'Buka Aplikasi E-Wallet', 'description' => 'Buka aplikasi e-wallet Anda (GoPay, OVO, DANA, LinkAja, dll)'],
                        ['step' => '2', 'title' => 'Pilih Opsi Scan', 'description' => 'Pilih opsi scan atau bayar menggunakan QR code'],
                        ['step' => '3', 'title' => 'Scan QR Code', 'description' => 'Scan QR code yang ditampilkan pada halaman pembayaran'],
                        ['step' => '4', 'title' => 'Konfirmasi Pembayaran', 'description' => 'Konfirmasi jumlah yang akan dibayarkan'],
                        ['step' => '5', 'title' => 'Selesaikan Pembayaran', 'description' => 'Masukkan PIN atau verifikasi sidik jari untuk menyelesaikan pembayaran']
                    ];
                    break;
                case 'gopay':
                    $steps = [
                        ['step' => '1', 'title' => 'Buka Aplikasi Gojek', 'description' => 'Buka aplikasi Gojek di smartphone Anda'],
                        ['step' => '2', 'title' => 'Pilih Bayar', 'description' => 'Pilih menu Bayar di aplikasi Gojek'],
                        ['step' => '3', 'title' => 'Scan QR Code', 'description' => 'Scan QR code yang ditampilkan pada halaman pembayaran'],
                        ['step' => '4', 'title' => 'Konfirmasi Pembayaran', 'description' => 'Konfirmasi jumlah yang akan dibayarkan'],
                        ['step' => '5', 'title' => 'Selesaikan Pembayaran', 'description' => 'Masukkan PIN GoPay untuk menyelesaikan pembayaran']
                    ];
                    break;
                case 'credit_card':
                    $steps = [
                        ['step' => '1', 'title' => 'Isi Detail Kartu Kredit', 'description' => 'Masukkan nomor kartu, tanggal kadaluarsa, dan CVV'],
                        ['step' => '2', 'title' => 'Verifikasi 3D Secure', 'description' => 'Ikuti proses verifikasi 3D Secure jika kartu Anda terdaftar'],
                        ['step' => '3', 'title' => 'Konfirmasi Pembayaran', 'description' => 'Cek kembali detail pembayaran Anda'],
                        ['step' => '4', 'title' => 'Selesaikan Pembayaran', 'description' => 'Klik tombol bayar untuk menyelesaikan transaksi']
                    ];
                    break;
                case 'cstore':
                case 'convenience_store':
                    $storeCode = '';
                    $paymentCode = '';
                    
                    if (isset($midtransData['store']) && $midtransData['store'] === 'indomaret') {
                        $storeCode = 'Indomaret';
                        $paymentCode = $midtransData['payment_code'];
                    } elseif (isset($midtransData['store']) && $midtransData['store'] === 'alfamart') {
                        $storeCode = 'Alfamart/Alfa group';
                        $paymentCode = $midtransData['payment_code'];
                    }
                    
                    $steps = [
                        ['step' => '1', 'title' => 'Kunjungi ' . $storeCode, 'description' => 'Datang ke ' . $storeCode . ' terdekat'],
                        ['step' => '2', 'title' => 'Informasi Pembayaran', 'description' => 'Beritahu kasir bahwa Anda ingin melakukan pembayaran transaksi online'],
                        ['step' => '3', 'title' => 'Berikan Kode Pembayaran', 'description' => 'Berikan kode pembayaran: <strong>' . $paymentCode . '</strong> kepada kasir'],
                        ['step' => '4', 'title' => 'Lakukan Pembayaran', 'description' => 'Bayar sesuai dengan jumlah tagihan'],
                        ['step' => '5', 'title' => 'Simpan Bukti Pembayaran', 'description' => 'Simpan bukti pembayaran/struk sebagai bukti transaksi']
                    ];
                    break;
            }
        }
        
        return $steps;
    }
    
    /**
     * Format metode pembayaran agar lebih mudah dibaca
     */
    private function formatPaymentMethod($paymentType)
    {
        $paymentMethods = [
            'bank_transfer' => 'Transfer Bank',
            'credit_card' => 'Kartu Kredit',
            'gopay' => 'GoPay',
            'qris' => 'QRIS',
            'cstore' => 'Convenience Store',
            'echannel' => 'Mandiri Bill Payment'
        ];
        
        return $paymentMethods[$paymentType] ?? ucfirst($paymentType);
    }

    /**
     * Helper method to update order status based on Midtrans status
     */
    private function updateOrderStatus($order, $status, $fraudStatus, $paymentMethod = null)
    {
        Log::info('Updating order status for #' . $order->id . ' with status: ' . $status);
        
        // Update status berdasarkan status transaksi
        if ($status == 'capture' || $status == 'settlement') {
            if ($fraudStatus == 'challenge') {
                $order->status = 'challenge';
            } else {
                $order->status = 'paid';
                $this->generateQRCode($order);
            }
        } else if ($status == 'cancel' || $status == 'deny' || $status == 'expire') {
            $order->status = 'cancelled';
        } else if ($status == 'pending') {
            $order->status = 'pending';
        }
        
        // Update metode pembayaran jika tersedia
        if ($paymentMethod) {
            $order->payment_method = $paymentMethod;
        }
        
        $order->save();
        
        Log::info('Order status updated to: ' . $order->status);
        
        return $order;
    }
}
