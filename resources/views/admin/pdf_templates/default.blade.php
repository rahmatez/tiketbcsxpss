<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>E-Voucher | {MATCH_TEAMS}</title>
    <!-- Bootstrap 5 CSS -->    <!-- Include Bootstrap CSS inline for PDF generation -->
    <style>
      /* Bootstrap 5 core CSS (minimal subset) */
      :root {
        --bs-blue: #0d6efd;
        --bs-indigo: #6610f2;
        --bs-purple: #6f42c1;
        --bs-pink: #d63384;
        --bs-red: #dc3545;
        --bs-orange: #fd7e14;
        --bs-yellow: #ffc107;
        --bs-green: #198754;
        --bs-teal: #20c997;
        --bs-cyan: #0dcaf0;
        --bs-white: #fff;
        --bs-gray: #6c757d;
        --bs-gray-dark: #343a40;
        --bs-primary: #0d6efd;
        --bs-secondary: #6c757d;
        --bs-success: #198754;
        --bs-info: #0dcaf0;
        --bs-warning: #ffc107;
        --bs-danger: #dc3545;
        --bs-light: #f8f9fa;
        --bs-dark: #212529;
      }
        /* Container, row and column classes */
      .container { width: 100%; padding-right: 0; padding-left: 0; margin-right: auto; margin-left: auto; }
      .row { display: -webkit-box; display: -ms-flexbox; display: flex; -ms-flex-wrap: wrap; flex-wrap: wrap; margin-right: 0; margin-left: 0; }
      .col-md-4 { -webkit-box-flex: 0; -ms-flex: 0 0 auto; flex: 0 0 auto; width: 33.33333333%; padding: 0 10px; }
      .col-md-6 { -webkit-box-flex: 0; -ms-flex: 0 0 auto; flex: 0 0 auto; width: 50%; padding: 0 10px; }
      .col-md-8 { -webkit-box-flex: 0; -ms-flex: 0 0 auto; flex: 0 0 auto; width: 66.66666667%; padding: 0 10px; }
      .col-md-12 { -webkit-box-flex: 0; -ms-flex: 0 0 auto; flex: 0 0 auto; width: 100%; padding: 0 10px; }
      
      /* Text utilities */
      .text-center { text-align: center !important; }
      .text-start { text-align: left !important; }
      .text-end { text-align: right !important; }
      
      /* Spacing utilities */
      .p-2 { padding: 0.5rem !important; }
      .p-4 { padding: 1rem !important; }
      .ps-3 { padding-left: 1rem !important; }
      .py-2 { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
      .mb-0 { margin-bottom: 0 !important; }
      .mb-1 { margin-bottom: 0.25rem !important; }
      .mb-2 { margin-bottom: 0.5rem !important; }
      .mb-3 { margin-bottom: 1rem !important; }
      .me-1 { margin-right: 0.25rem !important; }
      .me-2 { margin-right: 0.5rem !important; }
      .mt-2 { margin-top: 0.5rem !important; }
      
      /* Color utilities */
      .bg-white { background-color: #fff !important; }
      .bg-danger { background-color: #dc3545 !important; }
      .text-white { color: #fff !important; }
      .text-muted { color: #6c757d !important; }
      
      /* Text decorations */
      .text-decoration-none { text-decoration: none !important; }
      
      /* Font utilities */
      .fs-4 { font-size: 1.5rem !important; }
      .fs-5 { font-size: 1.25rem !important; }
      .small { font-size: .875em !important; }
      
      /* Border utilities */
      .border { border: 1px solid #dee2e6 !important; }
      
      /* Icon placeholders */
      .bi { display: inline-block; vertical-align: -.125em; }
      .bi-calendar-event:before { content: "📅"; }
      .bi-geo-alt:before { content: "📍"; }
    </style>
    <style>
      body {
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f8f9fa;
      }
      .e-voucher-container {
        max-width: 800px;
        margin: 30px auto;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        border-radius: 15px;
        overflow: hidden;
      }
      .header {
        background-color: #1c1c1c;
        color: white;
        padding: 20px;
        text-align: center;
      }
      .event-info {
        background-color: #075028;
        color: white;
        padding: 15px 20px;
      }
      .qr-code {
        width: 100%;
        max-width: 170px;
        height: auto;
        margin: 0 auto;
      }
      .ticket-divider {
        border-top: 2px dashed #dee2e6;
        margin: 20px 0;
        position: relative;
      }
      .ticket-divider::before,
      .ticket-divider::after {
        content: "";
        position: absolute;
        width: 20px;
        height: 20px;
        background-color: #f8f9fa;
        border-radius: 50%;
        top: -10px;
      }
      .ticket-divider::before {
        left: -10px;
      }
      .ticket-divider::after {
        right: -10px;
      }
      .info-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.9rem;
      }
      .info-value {
        font-weight: 500;
        font-size: 1.1rem;
      }
      .terms-section {
        background-color: #f8f9fa;
        font-size: 0.9rem;
        padding: 15px;
        border-top: 1px solid #dee2e6;
      }
      .footer {
        background-color: #1c1c1c;
        color: white;
        text-align: center;
        padding: 15px;
        font-size: 0.8rem;
      }
      .term-item {
        margin-bottom: 8px;
      }
      .highlight {
        background-color: #fddc5c;
        padding: 2px 5px;
        border-radius: 3px;
        font-weight: 600;
      }
      .badge-custom {
        background-color: #1047b3;
        color: white;
        padding: 7px 15px;
        border-radius: 15px;
        font-weight: 600;
      }
      @media print {
        .e-voucher-container {
          box-shadow: none;
          margin: 0;
        }
        .no-print {
          display: none;
        }
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="e-voucher-container bg-white">
        <!-- Header -->
        <div class="header">
          <h1 class="mb-0">E-Voucher</h1>
        </div>

        <!-- Event Information -->
        <div class="event-info">
          <div class="row align-items-center">
            <div class="col-md-12 text-center">
              <h2 class="fs-4 mb-3">                BRI LIGA 1: {MATCH_TEAMS}
              </h2>
              <p class="mb-1">
                <i class="bi bi-calendar-event me-2"></i>
                {MATCH_DATE} {MATCH_TIME}
              </p>              <p class="mb-1">
                <i class="bi bi-geo-alt me-2"></i>
                {STADIUM}
              </p>
              <p class="mb-0">
                Jl. Gerbang Biru, Rancanumpang, Gedebage, Bandung, Jawa Barat
              </p>
            </div>
          </div>
        </div>

        <!-- Ticket Details -->
        <div class="p-4">
          <div class="row mb-3">
            <div class="col-md-6 mb-3 mb-md-0">
              <div class="info-label">Nama / Name</div>              <div class="info-value">{USER_NAME}</div>
            </div>
            <div class="col-md-6">
              <div class="info-label">Kode Tagihan / Invoice Code</div>
              <div class="info-value">#{ORDER_ID}</div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6 mb-3 mb-md-0">
              <div class="info-label">Tanggal Pembelian / Order Date</div>              <div class="info-value">{PURCHASE_DATE}</div>
            </div>
            <div class="col-md-6">
              <div class="info-label">Referensi / Reference</div>
              <div class="info-value">{TICKET_STATUS}</div>
            </div>
          </div>

          <div class="ticket-divider"></div>

          <div class="row">
            <div class="col-md-8">
              <div class="mb-3">
                <div class="info-label">Kategori Tiket / Ticket Category</div>                <div class="info-value">{SEAT_CATEGORY}</div>
              </div>
              <div class="mb-3">
                <div class="info-label">
                  Tempat Penukaran Tiket / Ticket Exchange Location
                </div>
                <div class="info-value">{STADIUM}</div>
                <a                  href="#"
                  class="text-decoration-none">
                  <small>
                    <i class="bi bi-geo-alt me-1"></i>
                    Lihat Lokasi / View Location
                  </small>
                </a>
              </div>
              <div class="mb-3">
                <div class="info-label">Quantity</div>
                <div class="info-value">{QUANTITY} Tiket</div>
              </div>
              <div>                <div class="info-label">Status Tiket / Ticket Status</div>
                <div class="info-value">{TICKET_STATUS}</div>
              </div>
            </div>            <div class="col-md-4 text-center">
              <div class="qr-code bg-white p-2">{QR_CODE}</div>
              <div class="mt-2 small text-muted">Scan untuk validasi</div>
            </div>
          </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="terms-section">
          <h4 class="fs-5 mb-3">
            Syarat & Ketentuan E-Voucher Liga 1 2022 / Terms & Conditions
          </h4>

          <ol class="ps-3">
            <li class="term-item">
              TIKET yang sah adalah yang dibeli melalui PERSIB website / apps.
            </li>
            <li class="term-item">
              Satu TIKET hanya berlaku untuk satu orang.
            </li>
            <li class="term-item">
              Semua pemegang TIKET yang telah berusia di atas 18 tahun,
              diwajibkan sudah melakukan vaksin COVID-19 Dosis 3 / Booster dan
              mempunyai aplikasi PeduliLindungi.
            </li>
            <li class="term-item">
              Semua pemegang TIKET yang telah berusia di antara 6-17 tahun,
              diwajibkan sudah melakukan vaksin COVID-19 Dosis 2 dan mempunyai
              aplikasi PeduliLindungi.
            </li>
            <li class="term-item">
              <span class="highlight">E-Voucher tidak perlu di print</span>
              , cukup menunjukkan E-Voucher dalam bentuk digital.
            </li>
            <li class="term-item">
              Semua pemegang TIKET yang telah berusia di atas 18 tahun,
              <span class="highlight">
                WAJIB membawa Kartu Identitas Penduduk (KTP) asli/fisik
              </span>
              sesuai nama pada E-Voucher pada saat penukaran TIKET.
            </li>
            <li class="term-item">
              Semua pemegang TIKET yang telah berusia di antara 6-17 tahun,
              WAJIB membawa Kartu Identitas Anak (KIA) asli/fisik, atau Kartu
              Keluarga asli/fisik, atau menunjukkan Sertifikat Vaksin Dosis 2
              pada aplikasi Peduli Lindungi yang memiliki keterangan nama sesuai
              nama pada E-Voucher pada saat penukaran TIKET.
            </li>
            <li class="term-item">
              Evoucher tidak dapat diperjualbelikan & hati-hati terhadap
              penipuan!.
            </li>
            <li class="term-item">
              Jangan mengunggah foto E-Voucher anda secara online karena pihak
              lain dapat menyalin dan mengklaim tiket anda.
            </li>
            <li class="term-item">
              E-Voucher ini
              <span class="highlight">BUKAN TIKET TANDA MASUK</span>
              dan selanjutnya E-Voucher ini akan di tukar dengan
              <span class="highlight">GELANG PENANDA</span>
              .
            </li>
            <li class="term-item">
              <span class="highlight">GELANG PENANDA</span>
              wajib dipakai sebelum memasuki area stadion.
            </li>
            <li class="term-item">
              <span class="highlight">15 menit</span>
              setelah kick off, seluruh pintu (gate) akan di tutup.
            </li>
            <li class="term-item">
              Pihak Panitia Pelaksana (Panpel) atau penyelenggara memiliki hak
              untuk:
              <ul class="mt-2">
                <li>
                  Melarang masuk pengunjung ke dalam stadion jika TIKET yang
                  digunakan tidak valid.
                </li>
                <li>
                  Memproses atau mengajukan hukum, baik perdata atau kriminal
                  kepada pengunjung yang mendapatkan TIKET dengan ilegal
                  termasuk memalsukan dan menggandakan TIKET yang sah atau
                  mendapatkan TIKET dengan cara yang tidak sesuai prosedur.
                </li>
              </ul>
            </li>
            <li class="term-item">
              Harap mematuhi protokol kesehatan yang diterapkan pihak panpel
              atau penyelenggara di area stadion, mencuci tangan, menggunakan
              masker, dan menjaga jarak (3M).
            </li>
            <li class="term-item">
              Pihak panpel atau penyelenggara menindak tegas, dan berhak
              mengeluarkan penonton apabila tidak mematuhi protokol kesehatan
              yang telah diterapkan.
            </li>
            <li class="term-item">
              TIKET yang sudah di beli tidak dapat dikembalikan.
            </li>
            <li class="term-item">
              Dilarang membawa dan menggunakan senjata obat-obatan terlarang,
              narkoba, sikotropika, atau baran-barang tajam.
            </li>
          </ol>
        </div>

        <!-- Footer -->
        <div class="footer">
          <div class="row">
            <div class="col-4 text-start">+62 218 0600 822</div>
            <div class="col-4 text-center">support@loket.com</div>
            <div class="col-4 text-end">www.loket.com</div>
          </div>
        </div>
      </div>
    </div>    <!-- No scripts needed for PDF generation -->
  </body>
</html>
