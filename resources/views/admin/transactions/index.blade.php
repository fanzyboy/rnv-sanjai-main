@extends('layouts.template')

@section('title', 'Kelola Orders & Preorders')

@section('content')
<style>
    /* Global & Card Styling */
    :root {
        --primary-color: #ff6b35; /* Menyesuaikan tema R&V Orange */
        --secondary-color: #858796;
        --success-color: #1cc88a;
        --warning-color: #f6c23e;
        --danger-color: #e74a3b;
    }

    .card {
        border: none;
        border-radius: 0 0 15px 15px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    }

    /* Tab Styling Custom */
    .nav-tabs-custom {
        border-bottom: none;
        gap: 10px;
    }

    .nav-tabs-custom .nav-link {
        border: none;
        color: var(--secondary-color);
        font-weight: 700;
        padding: 12px 25px;
        border-radius: 12px 12px 0 0;
        background: #f1f1f1;
        transition: 0.3s;
    }

    .nav-tabs-custom .nav-link i {
        margin-right: 8px;
    }

    .nav-tabs-custom .nav-link.active {
        background: white;
        color: var(--primary-color);
        box-shadow: 0 -4px 10px rgba(0,0,0,0.05);
        position: relative;
    }

    .nav-tabs-custom .nav-link.active::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--primary-color);
    }

    /* Table Styling */
    .table thead th {
        background-color: #f8f9fc;
        border-bottom: 2px solid #e3e6f0;
        color: #4e73df;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 15px;
    }

    .table tbody td {
        padding: 15px;
        vertical-align: middle;
    }

    /* Badge Styling */
    .badge-status {
        padding: 0.5em 1em;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    /* Pagination Fix */
    .pagination { margin-top: 20px; justify-content: center; }
    .page-item .page-link { border: none; border-radius: 8px !important; margin: 0 3px; }
    .page-item.active .page-link { background-color: var(--primary-color); border-color: var(--primary-color); }

    .btn-view-bukti {
        color: #4e73df;
        font-weight: 600;
        cursor: pointer;
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-0 fw-bold text-dark">Manajemen Transaksi</h3>
            <p class="text-muted mb-0">Kelola pesanan masuk dan preorder dalam satu panel.</p>
        </div>
        <div class="bg-white p-2 rounded-3 shadow-sm border">
            <span class="badge bg-primary rounded-pill">{{ $orders->total() }} Orders</span>
            <span class="badge bg-warning text-dark rounded-pill">{{ $preorders->total() }} Preorders</span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
            <i class="fas fa-check-circle me-3 fa-lg"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <ul class="nav nav-tabs nav-tabs-custom" id="transactionTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="order-tab" data-bs-toggle="tab" data-bs-target="#order-pane" type="button" role="tab">
                <i class="fas fa-shopping-bag"></i> Pesanan Reguler
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="preorder-tab" data-bs-toggle="tab" data-bs-target="#preorder-pane" type="button" role="tab">
                <i class="fas fa-clock"></i> Daftar Preorder
            </button>
        </li>
    </ul>

    <div class="tab-content mt-0" id="transactionTabsContent">
        {{-- TAB ORDER --}}
        <div class="tab-pane fade show active" id="order-pane" role="tabpanel">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Pelanggan</th>
                                    <th>Detail Produk</th>
                                    <th>Total Bayar</th>
                                    <th>Metode</th>
                                    <th>Bukti</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td class="text-center text-muted">{{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $order->user->name ?? 'User' }}</div>
                                        <div class="small text-muted">{{ $order->created_at->format('d M Y, H:i') }}</div>
                                    </td>
                                    <td>
                                        @foreach($order->items as $item)
                                            <div class="mb-1 small">
                                                <span class="fw-semibold text-dark">{{ $item->product->nama_produk }}</span>
                                                <span class="badge bg-light text-dark border ms-1">{{ $item->quantity }}x</span>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="fw-bold text-dark">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    <td><span class="text-uppercase small fw-bold text-secondary">{{ $order->payments->first()->metode ?? 'N/A' }}</span></td>
                                    <td>
                                        @if($order->payments->first() && $order->payments->first()->bukti)
                                            <div class="btn-view-bukti small" onclick="previewImage('{{ asset('storage/' . $order->payments->first()->bukti) }}', 'Bukti - {{ $order->user->name }}')">
                                                <i class="fas fa-camera me-1"></i> Periksa
                                            </div>
                                        @else
                                            <span class="text-muted small">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge-status
                                            @if($order->status == 'pending') bg-warning text-dark
                                            @elseif($order->status == 'proses') bg-primary text-white
                                            @elseif($order->status == 'selesai') bg-success text-white
                                            @else bg-danger text-white @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td style="width: 180px;">
                                        {{-- LOCK DROPDOWN JIKA SELESAI / DITOLAK --}}
                                        <select class="form-select form-select-sm"
                                            data-bank="{{ $order->nama_bank ?? '-' }}"
                                            data-rekening="{{ $order->nomor_rekening ?? '-' }}"
                                            onchange="handleOrderStatusChange(this, {{ $order->id }}, {{ $order->total_amount }}, '{{ strtolower($order->payments->first()->metode ?? 'cash') }}')"
                                            {{ in_array($order->status, ['selesai', 'ditolak']) ? 'disabled' : '' }}>
                                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="proses" {{ $order->status == 'proses' ? 'selected' : '' }}>Proses</option>
                                            <option value="selesai" {{ $order->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                            <option value="ditolak" {{ $order->status == 'ditolak' ? 'selected' : '' }}>Tolak / Refund</option>
                                        </select>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="8" class="text-center py-5 text-muted">Tidak ada data pesanan reguler.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center py-4">
                        {{ $orders->appends(['tab' => 'order'])->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB PREORDER --}}
        <div class="tab-pane fade" id="preorder-pane" role="tabpanel">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Pelanggan</th>
                                    <th>Produk Varian</th>
                                    <th>Qty</th>
                                    <th>Bukti</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($preorders as $item)
                                <tr>
                                    <td class="text-center text-muted">{{ ($preorders->currentPage() - 1) * $preorders->perPage() + $loop->iteration }}</td>
                                    <td><div class="fw-bold text-dark">{{ $item->user->name ?? '-' }}</div></td>
                                    <td>
                                        <div class="fw-bold">{{ $item->price->product->nama_produk ?? '-' }}</div>
                                        <div class="small text-muted">{{ $item->price->berat ?? 0 }}g</div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $item->qty }}</span></td>
                                    <td>
                                        @if($item->bukti_transfer)
                                            <div class="btn-view-bukti small text-warning" onclick="previewImage('{{ asset('storage/' . $item->bukti_transfer) }}', 'Bukti PO - {{ $item->user->name }}')">
                                                <i class="fas fa-file-invoice-dollar me-1"></i> Lihat
                                            </div>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge-status
                                            @if($item->status == 'pending') bg-warning text-dark
                                            @elseif($item->status == 'proses') bg-primary text-white
                                            @elseif($item->status == 'selesai') bg-success text-white
                                            @else bg-danger text-white @endif">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                    <td style="width: 180px;">
                                        {{-- LOCK DROPDOWN JIKA SELESAI / DITOLAK --}}
                                        <select class="form-select form-select-sm"
                                            data-bank="{{ $item->user->nama_bank ?? '-' }}"
                                            data-rekening="{{ $item->user->nomor_rekening ?? '-' }}"
                                            onchange="handlePreorderStatusChange(this, {{ $item->id }}, {{ $item->total_amount }}, '{{ strtolower($item->metode_pembayaran ?? 'cod') }}')"
                                            {{ in_array($item->status, ['selesai', 'ditolak']) ? 'disabled' : '' }}>
                                            <option value="pending" {{ $item->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="proses" {{ $item->status == 'proses' ? 'selected' : '' }}>Proses</option>
                                            <option value="selesai" {{ $item->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                            <option value="ditolak" {{ $item->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                        </select>
                                    </td>
                                    <td class="fw-bold text-dark">Rp{{ number_format($item->total_amount, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="8" class="text-center py-5 text-muted">Belum ada data preorder.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center py-4">
                        {{ $preorders->appends(['tab' => 'preorder'])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PREVIEW GAMBAR --}}
<div class="modal fade" id="imagePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h6 class="modal-title fw-bold" id="imagePreviewTitle">Bukti Transfer</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-light text-center">
                <img src="" id="previewImg" class="img-fluid" alt="Bukti">
            </div>
        </div>
    </div>
</div>

{{-- MODAL REFUND --}}
<div class="modal fade" id="refundModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="refundForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="type" value="">
            <input type="hidden" name="status" value="ditolak">
            <input type="hidden" name="refund_amount" id="refundAmount">

            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold">Konfirmasi Pengembalian Dana</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">Pesanan ini menggunakan <strong>Transfer</strong>. Harap lampirkan bukti transfer balik ke pelanggan.</p>

                    <div class="alert alert-secondary border-0 mb-3 shadow-sm">
                        <label class="small fw-bold text-dark d-block mb-1"><i class="fas fa-university me-1"></i> Rekening Tujuan Pelanggan:</label>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-bold text-primary" id="refundBankName">-</h6>
                                <p class="mb-0 fw-bold fs-5" id="refundAccountNumber">-</p>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary border-0" onclick="copyAccountNumber()">
                                <i class="far fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold">Total Refund</label>
                        <input type="text" id="refundAmountDisplay" class="form-control form-control-lg fw-bold text-danger bg-light" readonly>
                    </div>

                    <div class="mb-0">
                        <label class="small fw-bold">Upload Bukti Refund <span class="text-danger">*</span></label>
                        <input type="file" name="bukti_admin" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Kirim Bukti & Tolak</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');
    if (activeTab === 'preorder') {
        const preorderTab = new bootstrap.Tab(document.getElementById('preorder-tab'));
        preorderTab.show();
    }
});

function previewImage(url, title) {
    document.getElementById('previewImg').src = url;
    document.getElementById('imagePreviewTitle').innerText = title;
    new bootstrap.Modal(document.getElementById('imagePreviewModal')).show();
}

function handleOrderStatusChange(select, id, amount, paymentMethod) {
    processRefundFlow(select, id, amount, paymentMethod, 'order');
}

function handlePreorderStatusChange(select, id, amount, paymentMethod) {
    processRefundFlow(select, id, amount, paymentMethod, 'preorder');
}

function processRefundFlow(select, id, amount, paymentMethod, type) {
    const selectedStatus = select.value;
    const baseUrl = type === 'order' ? '{{ url("admin/orders/updateStatus") }}' : '{{ url("admin/preorders/updateStatus") }}';

    if (selectedStatus === 'ditolak' && (paymentMethod.includes('transfer') || paymentMethod === 'bca' || paymentMethod === 'mandiri')) {
        const bankName = select.getAttribute('data-bank');
        const accountNumber = select.getAttribute('data-rekening');

        const form = document.getElementById('refundForm');
        form.action = `${baseUrl}/${id}`;
        form.querySelector('input[name="type"]').value = type;

        document.getElementById('refundBankName').innerText = bankName || 'Tidak Ada Data';
        document.getElementById('refundAccountNumber').innerText = accountNumber || 'Tidak Ada Data';
        document.getElementById('refundAmount').value = amount;
        document.getElementById('refundAmountDisplay').value = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);

        new bootstrap.Modal(document.getElementById('refundModal')).show();
        return;
    }

    // Jika user memilih selesai atau status lainnya
    const confirmMsg = selectedStatus === 'selesai' ? "Tandai pesanan sebagai SELESAI? Status tidak bisa diubah lagi setelah ini." : "Ubah status pesanan?";

    if(confirm(confirmMsg)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `${baseUrl}/${id}`;
        form.innerHTML = `
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="${selectedStatus}">
            <input type="hidden" name="type" value="${type}">
        `;
        document.body.appendChild(form);
        form.submit();
    } else {
        location.reload(); // Reset select jika batal
    }
}

function copyAccountNumber() {
    const accNo = document.getElementById('refundAccountNumber').innerText;
    if(accNo !== '-' && accNo !== 'Tidak Ada Data') {
        navigator.clipboard.writeText(accNo);
        alert('Nomor rekening berhasil disalin!');
    }
}
</script>
@endsection
