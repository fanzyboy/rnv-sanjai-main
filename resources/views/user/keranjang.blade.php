@extends('layouts.main')

@section('title', 'Keranjang - R&V Sanjai')

@section('content')
<div class="hero">
    <div class="container text-center">
        <div class="badge"><i class="fas fa-shopping-cart me-2"></i>Daftar Belanja</div>
        <h1>Keranjang Saya</h1>
    </div>
</div>

<div class="container py-4">
    {{-- Form Checkout tetap menggunakan GET ke proses checkout --}}
    <form action="{{ route('checkout.proses') }}" method="GET" id="form-checkout">
        @if (count($keranjang) > 0)
            <div class="cart-grid">
                <div class="items">
                    <div class="header">
                        <input type="checkbox" id="check-all" class="cb">
                        <label for="check-all" class="ms-2">Pilih Semua</label>
                        <div class="col d-none d-md-block">Produk</div>
                        <div class="col d-none d-md-block">Harga</div>
                        <div class="col d-none d-md-block">Jumlah</div>
                        <div class="col d-none d-md-block">Total</div>
                        <div class="col d-none d-md-block">Aksi</div>
                    </div>

                    @foreach ($keranjang as $item)
                        {{-- Menggunakan $item['id'] yang berasal dari ID tabel keranjangs --}}
                        <div class="item {{ $item['stok'] <= 0 ? 'oos' : '' }}">
                            <input type="checkbox" name="selected_items[]" value="{{ $item['id'] }}"
                                   class="cb item-cb" {{ $item['stok'] <= 0 ? 'disabled' : '' }}
                                   data-price="{{ $item['total'] }}">

                            <a href="{{ route('produk.show', $item['produk_id']) }}" class="prod">
                                <div class="img">
                                    <img src="{{ !empty($item['foto']) ? asset('storage/' . $item['foto']) : asset('images/keripik.jpg') }}" alt="{{ $item['produk'] }}">
                                    @if($item['stok'] <= 0)<div class="overlay">Habis</div>@endif
                                </div>
                                <div class="info">
                                    <h6>{{ $item['produk'] }}</h6>
                                    <span class="var">{{ $item['gram'] }}</span>
                                    @if($item['stok'] <= 0)<div class="stock"><i class="fas fa-exclamation-circle"></i> Stok Habis</div>@endif
                                </div>
                            </a>

                            <div class="price">
                                <small class="d-md-none">Harga</small>
                                <span>Rp {{ number_format($item['harga'], 0, ',', '.') }}</span>
                            </div>

                            <div class="qty">
                                <small class="d-md-none">Jumlah</small>
                                <div class="box">{{ $item['qty'] }}</div>
                            </div>

                            <div class="total">
                                <small class="d-md-none">Total</small>
                                <strong>Rp {{ number_format($item['total'], 0, ',', '.') }}</strong>
                            </div>

                            <div class="act">
                                {{-- Link hapus langsung ke ID database --}}
                                <a href="{{ route('keranjang.remove', $item['id']) }}"
                                   class="del text-decoration-none d-inline-block"
                                   onclick="return confirm('Hapus item ini?')">
                                    <i class="fas fa-trash-alt"></i><span class="d-none d-md-inline ms-1">Hapus</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="summary">
                    <div class="card">
                        <h5><i class="fas fa-file-invoice me-2"></i>Ringkasan Belanja</h5>
                        <div class="row"><span>Total Item</span><span id="count">0 Produk</span></div>
                        <hr>
                        <div class="row big"><span>Total Pembayaran</span><strong id="total">Rp 0</strong></div>

                        {{-- Hidden input untuk membedakan checkout biasa vs beli sekarang jika diperlukan --}}
                        <input type="hidden" name="source" value="cart">

                        <button type="submit" id="btn" class="btn-co disabled" disabled>
                            <i class="fas fa-shopping-bag me-2"></i>Checkout (<span id="num">0</span>)
                        </button>
                        <a href="{{ route('produk') }}" class="btn-cont"><i class="fas fa-arrow-left me-2"></i>Lanjut Belanja</a>
                    </div>
                </div>
            </div>
        @else
            <div class="empty">
                <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                <h4>Wah, keranjang belanjamu kosong</h4>
                <p>Yuk, isi dengan produk keripik sanjai pilihan!</p>
                <a href="{{ route('produk') }}" class="btn-shop"><i class="fas fa-store me-2"></i>Mulai Belanja</a>
            </div>
        @endif
    </form>
</div>

<style>
/* CSS Anda tetap sama, tidak ada perubahan pada style */
.hero{background:linear-gradient(135deg,#ee4d2d,#ff6b45);padding:40px 0;margin-bottom:30px;color:#fff}
.hero .badge{display:inline-block;background:rgba(255,255,255,.2);padding:8px 20px;border-radius:50px;font-size:14px;margin-bottom:10px;backdrop-filter:blur(10px)}
.hero h1{font-size:32px;font-weight:700;margin:0}
.cart-grid{display:grid;grid-template-columns:1fr 380px;gap:24px;align-items:start}
.header{background:#fff;padding:18px 24px;border-radius:8px;margin-bottom:16px;display:flex;align-items:center;box-shadow:0 1px 2px rgba(0,0,0,.05);font-weight:500;color:#555}
.header label{cursor:pointer;margin:0}
.header .col{flex:1;text-align:center}
.cb{width:20px;height:20px;cursor:pointer;border:2px solid #ddd;border-radius:3px;margin-right:8px}
.cb:hover{border-color:#ee4d2d}
.cb:checked{background:#ee4d2d;border-color:#ee4d2d}
.cb:disabled{cursor:not-allowed;opacity:.5}
.item{background:#fff;border-radius:8px;margin-bottom:16px;padding:20px 24px;display:flex;align-items:center;box-shadow:0 1px 2px rgba(0,0,0,.05);transition:all .2s}
.item:hover{box-shadow:0 2px 8px rgba(0,0,0,.1)}
.item.oos{background:#fafafa;opacity:.7}
.prod{text-decoration:none;color:inherit;display:flex;align-items:center;gap:16px;flex:4}
.img{width:90px;height:90px;position:relative;border-radius:8px;overflow:hidden;border:1px solid #f0f0f0;flex-shrink:0}
.img img{width:100%;height:100%;object-fit:cover;transition:transform .3s}
.prod:hover .img img{transform:scale(1.05)}
.overlay{position:absolute;inset:0;background:rgba(0,0,0,.6);color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600}
.info{flex:1;min-width:0}
.info h6{font-size:15px;font-weight:500;color:#222;margin:0 0 8px;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.prod:hover .info h6{color:#ee4d2d}
.var{display:inline-block;background:#f5f5f5;padding:4px 10px;border-radius:4px;font-size:13px;color:#555}
.stock{color:#ee4d2d;font-size:12px;font-weight:500;margin-top:6px}
.price,.qty,.total,.act{flex:1;text-align:center}
.price small,.qty small,.total small{display:none;font-size:11px;color:#888;text-transform:uppercase;margin-bottom:4px}
.qty .box{display:inline-block;padding:6px 16px;background:#f5f5f5;border-radius:6px;font-weight:500}
.total strong{color:#ee4d2d;font-size:16px;font-weight:600}
.del{background:#fff;border:1px solid #ddd;padding:8px 16px;border-radius:6px;color:#555;cursor:pointer;transition:all .2s; font-size: 14px;}
.del:hover{color:#ee4d2d;border-color:#ee4d2d;background:#fff5f5}
.summary .card{background:#fff;padding:24px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.08);position:sticky;top:100px}
.summary h5{font-size:18px;font-weight:600;margin:0 0 20px;display:flex;align-items:center}
.summary h5 i{color:#ee4d2d}
.summary .row{display:flex;justify-content:space-between;margin-bottom:12px;font-size:14px}
.summary .row.big{font-size:15px;margin-top:16px}
.summary .row.big strong{color:#ee4d2d;font-size:24px;font-weight:700}
.summary hr{border:0;height:1px;background:linear-gradient(to right,transparent,#e5e5e5,transparent);margin:16px 0}
.btn-co{background:linear-gradient(135deg,#ee4d2d,#ff6b45);color:#fff;width:100%;border:0;padding:14px;font-weight:600;font-size:15px;margin-top:20px;border-radius:8px;text-transform:uppercase;letter-spacing:.5px;box-shadow:0 4px 12px rgba(238,77,45,.3);transition:all .3s}
.btn-co:hover{transform:translateY(-2px);box-shadow:0 6px 16px rgba(238,77,45,.4)}
.btn-co.disabled{background:#d5d5d5;cursor:not-allowed;opacity:.6;transform:none;box-shadow:none}
.btn-cont{display:block;text-align:center;color:#ee4d2d;text-decoration:none;font-size:14px;font-weight:500;margin-top:12px;padding:10px;border-radius:6px;transition:all .2s}
.btn-cont:hover{background:#fff5f5}
.empty{background:#fff;padding:80px 40px;text-align:center;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.05)}
.empty .icon{width:120px;height:120px;margin:0 auto 24px;background:linear-gradient(135deg,#fff5f5,#ffe8e5);border-radius:50%;display:flex;align-items:center;justify-content:center}
.empty .icon i{font-size:60px;color:#ee4d2d}
.empty h4{font-size:22px;font-weight:600;color:#222;margin-bottom:8px}
.empty p{color:#666;font-size:15px;margin-bottom:24px}
.btn-shop{display:inline-block;background:linear-gradient(135deg,#ee4d2d,#ff6b45);color:#fff;padding:12px 32px;border-radius:8px;text-decoration:none;font-weight:600;font-size:15px;box-shadow:0 4px 12px rgba(238,77,45,.3);transition:all .3s}
.btn-shop:hover{transform:translateY(-2px);box-shadow:0 6px 16px rgba(238,77,45,.4);color:#fff}
@media(max-width:768px){
    .hero{padding:30px 0}.hero h1{font-size:24px}
    .cart-grid{grid-template-columns:1fr;gap:20px}
    .header{padding:14px 16px}
    .item{flex-wrap:wrap;padding:16px;gap:12px}
    .cb{width:30px;margin:0}
    .prod{width:calc(100% - 40px);gap:12px}
    .img{width:70px;height:70px}
    .info h6{font-size:14px}
    .price,.qty,.total{width:calc(33.33% - 8px);margin-top:12px}
    .price small,.qty small,.total small{display:block}
    .act{width:100%;margin-top:12px;padding-top:12px;border-top:1px solid #f0f0f0}
    .del{width:100%;justify-content:center}
    .summary .card{position:static}
    .empty{padding:60px 20px}
    .empty .icon{width:100px;height:100px}
    .empty .icon i{font-size:50px}
}
</style>

<script>
document.addEventListener('DOMContentLoaded',function(){
    const all=document.getElementById('check-all'),
          cbs=document.querySelectorAll('.item-cb:not(:disabled)'),
          total=document.getElementById('total'),
          count=document.getElementById('count'),
          num=document.getElementById('num'),
          btn=document.getElementById('btn');

    function calc(){
        let t=0,c=0;
        cbs.forEach(cb=>{
            if(cb.checked){
                t+=parseFloat(cb.dataset.price);
                c++;
            }
        });
        total.innerText='Rp '+t.toLocaleString('id-ID');
        count.innerText=c+' Produk';
        num.innerText=c;
        btn.classList.toggle('disabled',c===0);
        btn.disabled=c===0;
    }

    all.addEventListener('change',()=>{
        cbs.forEach(cb=>cb.checked=all.checked);
        calc();
    });

    cbs.forEach(cb=>cb.addEventListener('change',function(){
        if(!this.checked)all.checked=false;
        if(Array.from(cbs).every(c=>c.checked))all.checked=true;
        calc();
    }));
});
</script>
@endsection
