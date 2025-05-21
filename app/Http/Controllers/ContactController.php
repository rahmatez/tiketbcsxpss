<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormSubmission;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact.index');
    }
    
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string',
            'message' => 'required|string',
            'order_id' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }        // Jika order_id disediakan (tidak kosong), validasi keberadaannya
        if ($request->filled('order_id') && !empty(trim($request->order_id))) {
            $order = Order::find($request->order_id);
            
            // Jika order tidak ditemukan, tambahkan error khusus
            if (!$order) {
                return redirect()->back()
                    ->withErrors(['order_id' => 'ID Pesanan tidak ditemukan dalam sistem'])
                    ->withInput();
            }
            
            // Jika user terautentikasi, pastikan order milik mereka
            if (Auth::check() && $order->user_id !== Auth::id()) {
                return redirect()->back()
                    ->withErrors(['order_id' => 'ID Pesanan bukan milik akun Anda'])
                    ->withInput();
            }
        }// Kirim email jika diinginkan (opsional)
        // Mail::to('support@punditfc.com')->send(new ContactFormSubmission($request->all()));
          // Simpan pesan kontak ke database
        $contactMessage = new ContactMessage();
        $contactMessage->name = $request->name;
        $contactMessage->email = $request->email;
        $contactMessage->subject = $request->subject;
        $contactMessage->order_id = $request->filled('order_id') && !empty(trim($request->order_id)) ? $request->order_id : null;
        $contactMessage->message = $request->message;
        $contactMessage->status = 'pending';
        $contactMessage->save();

        return redirect()->back()->with('success', 'Pesan Anda telah terkirim. Tim kami akan segera menghubungi Anda.');
    }
}
