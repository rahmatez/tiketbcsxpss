<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminContactController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\AdminController;
use App\Http\Controllers\TicketController;

//Home
Route::get('/', [GameController::class, 'index'])->name('home');

//Location API
Route::get('/api/provinces', [LocationController::class, 'getProvinces'])->name('api.provinces');
Route::get('/api/provinces/{province}/cities', [LocationController::class, 'getCitiesByProvince'])->name('api.cities');

//Game Detail
Route::get('/games/{id}', [GameController::class, 'show'])->name('games.show');

//Admin
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login']);

Route::middleware(['auth:admin'])->group(function () {
    // Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Game Management
    Route::get('/admin/games', [GameController::class, 'adminIndex'])->name('admin.games.index');
    Route::get('/admin/games/create', [GameController::class, 'create'])->name('admin.games.create');
    Route::post('/admin/games', [GameController::class, 'store'])->name('admin.games.store');
    Route::get('/admin/games/{id}/edit', [GameController::class, 'edit'])->name('admin.games.edit');
    Route::put('/admin/games/{id}', [GameController::class, 'update'])->name('admin.games.update');
    Route::delete('/admin/games/{id}', [GameController::class, 'destroy'])->name('admin.games.destroy');
    Route::get('/admin/games/{id}/delete', [GameController::class, 'confirmDelete'])->name('admin.games.delete');
    
    // Ticket Management
    Route::get('/admin/tickets', [AdminController::class, 'ticketIndex'])->name('admin.tickets.index');
    Route::get('/admin/tickets/{id}/edit', [AdminController::class, 'ticketEdit'])->name('admin.tickets.edit');
    Route::put('/admin/tickets/{id}', [AdminController::class, 'ticketUpdate'])->name('admin.tickets.update');
    
    // Order Management
    Route::get('/admin/orders', [AdminController::class, 'orderIndex'])->name('admin.orders.index');
    Route::get('/admin/orders/{id}', [AdminController::class, 'orderShow'])->name('admin.orders.show');
    Route::put('/admin/orders/{id}/update-status', [AdminController::class, 'orderUpdateStatus'])->name('admin.orders.update_status');
    
    // User Management
    Route::get('/admin/users', [AdminController::class, 'userIndex'])->name('admin.users.index');
    Route::get('/admin/users/create', [AdminController::class, 'userCreate'])->name('admin.users.create');
    Route::post('/admin/users', [AdminController::class, 'userStore'])->name('admin.users.store');
    Route::get('/admin/users/{id}', [AdminController::class, 'userShow'])->name('admin.users.show');
    Route::get('/admin/users/{id}/edit', [AdminController::class, 'userEdit'])->name('admin.users.edit');
    Route::put('/admin/users/{id}', [AdminController::class, 'userUpdate'])->name('admin.users.update');
    Route::delete('/admin/users/{id}', [AdminController::class, 'userDestroy'])->name('admin.users.destroy');
    Route::put('/admin/users/{id}/status', [AdminController::class, 'userUpdateStatus'])->name('admin.users.update_status');
    
    // Scan QR Code
    Route::get('/admin/scan', [AdminController::class, 'showScanForm'])->name('admin.scan');
    Route::post('/admin/update-order-status', [AdminController::class, 'updateOrderStatus'])->name('admin.update_order_status')->middleware('web');
    Route::post('/admin/check-ticket-status', [AdminController::class, 'checkTicketStatus'])->name('admin.check_ticket_status')->middleware('web');
    Route::post('/admin/test-qr-scan', [\App\Http\Controllers\QrScanController::class, 'processQrCode'])->name('admin.test_qr_scan');
    Route::get('/admin/scan-history', [AdminController::class, 'scanHistory'])->name('admin.scan.history');
    Route::post('/admin/log-scan', [AdminController::class, 'logScan'])->name('admin.log_scan');
    
    // Reports
    Route::get('/admin/reports/sales', [AdminController::class, 'salesReport'])->name('admin.reports.sales');
    Route::get('/admin/reports/attendance', [AdminController::class, 'attendanceReport'])->name('admin.reports.attendance');
    Route::get('/admin/reports/tickets', [AdminController::class, 'ticketReport'])->name('admin.reports.tickets');
    Route::get('/admin/reports/export/{type}', [AdminController::class, 'exportReport'])->name('admin.reports.export');
    
    // Admin management
    Route::get('/admin/admins', [AdminController::class, 'adminIndex'])->name('admin.admins.index');
    Route::get('/admin/admins/create', [AdminController::class, 'adminCreate'])->name('admin.admins.create');
    Route::post('/admin/admins', [AdminController::class, 'adminStore'])->name('admin.admins.store');
    
    // Admin profile and settings
    Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::put('/admin/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::put('/admin/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
    
    // Contact Messages Management
    Route::get('/admin/contact', [AdminContactController::class, 'index'])->name('admin.contact.index');
    Route::get('/admin/contact/{id}', [AdminContactController::class, 'show'])->name('admin.contact.show');
    Route::put('/admin/contact/{id}', [AdminContactController::class, 'update'])->name('admin.contact.update');
    Route::post('/admin/contact/{id}/reply', [AdminContactController::class, 'reply'])->name('admin.contact.reply');
    Route::delete('/admin/contact/{id}', [AdminContactController::class, 'destroy'])->name('admin.contact.destroy');
    
    // PDF Templates
    Route::prefix('admin/pdf-templates')->name('admin.pdf-templates.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PdfTemplateController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\PdfTemplateController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\PdfTemplateController::class, 'store'])->name('store');
        Route::get('/{pdfTemplate}/edit', [\App\Http\Controllers\Admin\PdfTemplateController::class, 'edit'])->name('edit');
        Route::put('/{pdfTemplate}', [\App\Http\Controllers\Admin\PdfTemplateController::class, 'update'])->name('update');
        Route::delete('/{pdfTemplate}', [\App\Http\Controllers\Admin\PdfTemplateController::class, 'destroy'])->name('destroy');
        Route::post('/{pdfTemplate}/set-default', [\App\Http\Controllers\Admin\PdfTemplateController::class, 'setDefault'])->name('set-default');
        Route::get('/{pdfTemplate}/preview', [\App\Http\Controllers\Admin\PdfTemplateController::class, 'preview'])->name('preview');
    });
    
    // Logout
    Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout')->withoutMiddleware(['auth:admin']);
});

//Users
// Route untuk menampilkan form register
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');

// Route untuk proses register
Route::post('/register', [RegisterController::class, 'register']);

// Route untuk menampilkan form login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// Route untuk proses login
Route::post('/login', [LoginController::class, 'login']);

// Route untuk melihat profil, terbuka untuk semua
Route::get('/profile/{id}', [ProfileController::class, 'show'])->name('profile.show');

// Route untuk mengedit dan mengupdate profil, hanya untuk pengguna yang terautentikasi
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/{id}/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/{id}', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/my-tickets', [OrderController::class, 'myTickets'])->name('my.tickets');
    Route::get('/purchase-history', [OrderController::class, 'purchaseHistory'])->name('purchase.history');
    
    // Route untuk detail tiket (payment check dihandle di controller)
    Route::get('/ticket/{id}', [OrderController::class, 'ticketDetail'])
         ->name('ticket.detail');
         
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    
    // Route untuk mengunduh tiket PDF
    Route::get('/ticket/{id}/pdf', [OrderController::class, 'downloadTicketPdf'])->name('orders.ticket.pdf');
    
    // Routes untuk notifikasi
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
});

Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::get('/not_logged_in', function () {
    return view('not_logged_in');
})->name('not_logged_in');

Route::post('/finalize_checkout', [OrderController::class, 'finalizeCheckout'])->name('finalize_checkout');

// Midtrans Payment Routes
Route::prefix('payment')->group(function () {
    // Route untuk membuat transaksi baru
    Route::post('/create/{order}', [PaymentController::class, 'createTransaction'])->name('payment.create');
    
    // Route untuk menangani notifikasi dari Midtrans - No CSRF untuk webhook
    Route::post('/notification', [PaymentController::class, 'handleNotification'])->withoutMiddleware(['web', 'csrf'])->name('payment.notification');
    
    // Route untuk callback setelah pembayaran
    Route::get('/finish', [PaymentController::class, 'finishPayment'])->name('payment.finish');
    Route::get('/unfinish', [PaymentController::class, 'unfinishPayment'])->name('payment.unfinish');
    Route::get('/error', [PaymentController::class, 'errorPayment'])->name('payment.error');
    
    // Route untuk cek status pembayaran
    Route::get('/status/{order}', [PaymentController::class, 'checkStatus'])->name('payment.status');
    
    // Route untuk halaman detail status pembayaran
    Route::get('/detail/{order}', [PaymentController::class, 'paymentDetail'])->name('payment.detail')->middleware('auth');
});

// Pencarian Tiket
Route::get('/search', [TicketController::class, 'search'])->name('tickets.search');

// FAQ & Bantuan
Route::get('/faq', function () {
    return view('help.faq');
})->name('faq');

// Kontak
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact/send', [ContactController::class, 'send'])->name('contact.send');



