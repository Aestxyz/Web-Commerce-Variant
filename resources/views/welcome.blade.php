<?php

use App\Models\Category;
use App\Models\Shop;
use App\Models\Product;
use function Laravel\Folio\name;
use function Livewire\Volt\{state, rules, computed};

name('welcome');

state([
    'products' => fn() => Product::inRandomOrder()->limit(6)->get(),
    'shop' => Shop::first(),
]);

?>

<x-guest-layout>
    <x-slot name="title">Selamat Datang</x-slot>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .hover {
            --c: #ff8e56;
            /* the color */

            color: #0000;
            background:
                linear-gradient(90deg, #fff 50%, var(--c) 0) calc(100% - var(--_p, 0%))/200% 100%,
                linear-gradient(var(--c) 0 0) 0% 100%/var(--_p, 0%) 100% no-repeat;
            -webkit-background-clip: text, padding-box;
            background-clip: text, padding-box;
            transition: 0.5s;
            font-weight: bolder;
        }

        .hover:hover {
            --_p: 100%
        }
    </style>

    @volt
        <div>
            <div class="container main-banner">
                <div class="owl-carousel owl-banner">
                    <div class="item item-1 rounded rounded-5"
                        style="background-image: url('https://i.pinimg.com/736x/8d/95/40/8d9540cfcf5c95f1ce84a25c166602e1.jpg'); width: 100%; height: 900px; object-fit: cover;">
                        <div class="header-text">
                            <h2 id="font-custom" class="text-white font-stroke">
                                Tren fashion terbaru, hanya di jarak selangkah.
                            </h2>
                        </div>
                    </div>
                    <div class="item item-2 rounded rounded-5"
                        style="background-image: url('https://i.pinimg.com/736x/4c/8c/58/4c8c585b071de6a0816d97bcf5f2c416.jpg'); width: 100%; height: 900px; object-fit: cover;">
                        <div class="header-text">
                            <h2 id="font-custom" class="text-white font-stroke">Percayakan gaya Anda pada kami.
                            </h2>
                        </div>
                    </div>
                    <div class="item item-3 rounded rounded-5"
                        style="background-image: url('https://i.pinimg.com/736x/df/41/04/df4104045790efb04e0b84250a7c24dd.jpg'); width: 100%; height: 900px; object-fit: cover;">
                        <div class="header-text">
                            <h2 id="font-custom" class="text-white font-stroke">Bergaya sesuai kepribadian Anda dengan
                                koleksi eksklusif kami.
                            </h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="properties section">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4 offset-lg-4">
                            <div class="section-heading text-center">
                                <h6>| Koleksi Kami</h6>
                                <h2 id="font-custom" class="fw-bold">Lihat apa yang bisa kamu temukan disini</h2>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @foreach ($products as $product)
                            <div class="col-lg-4 col-md-6">
                                <div class="item">
                                    <a href="{{ route('product-detail', ['product' => $product->id]) }}"><img
                                            src="{{ Storage::url($product->thumbnail) }}" alt="{{ $product->title }}"
                                            class="object-fit-cover" style="width: 100%; height: 300px;"></a>
                                    <span class="category">
                                        {{ Str::limit($product->category->name, 13, '...') }}
                                    </span>
                                    <h6>
                                        {{ 'Rp. ' . Number::format($product->price, locale: 'id') }}
                                    </h6>
                                    <h4>
                                        <a href="{{ route('product-detail', ['product' => $product->id]) }}">
                                            {{ Str::limit($product->title, 50, '...') }}
                                        </a>
                                    </h4>
                                    <div class="main-button">
                                        <a href="{{ route('product-detail', ['product' => $product->id]) }}">Beli
                                            Sekarang</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <section class="py-5">
                <div class="container">
                    <!--- Heading -->
                    <div class="row text-center mb-0">
                        <div class="col-12 col-lg-10 col-xl-8 mx-auto text-center section-heading">
                            <h6>| Bersama {{ $shop->name ?? '' }}</h6>
                            <h2 class="fw-bold" id="font-custom">Dapatkan produk favorit kamu
                                <span class="hover fw-bold" id="font-custom">diantar ke depan pintumu</span>
                            </h2>
                        </div>
                    </div><!--- Steps Wrap -->
                    <div class="row text-center mb-5">
                        <div class="col-lg-4">
                            <div class="btn btn-outline-light rounded-5 shadow mb-4 p-4">
                                <i class="bi bi-star text-primary" style="font-size: 2rem;"></i>
                                <p id="font-custom" class="fs-3 fw-bolder my-3">Kualitas Terbaik</p>
                                <p>Setiap produk kami dibuat dari bahan pilihan untuk memastikan
                                    kenyamanan maksimal.</p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="btn btn-outline-light rounded-5 shadow mb-4 p-4">
                                <i class="bi bi-truck text-primary" style="font-size: 2rem;"></i>
                                <p id="font-custom" class="fs-3 fw-bolder my-3">Pengiriman Cepat</p>
                                <p>Kami bekerja sama dengan jasa pengiriman terpercaya untuk memastikan
                                    produk sampai tepat waktu.</p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="btn btn-outline-light rounded-5 shadow mb-4 p-4">
                                <i class="bi bi-wallet2 text-primary" style="font-size: 2rem;"></i>
                                <p id="font-custom" class="fs-3 fw-bolder my-3">Harga Terjangkau</p>
                                <p>Dapatkan produk premium dengan harga yang bersahabat di kantong.</p>
                            </div>
                        </div>
                    </div>

                    <div class="row text-center mt-3 pb-0">
                        <div class="col-12 col-lg-10 col-xl-8 mx-auto text-center section-heading">
                            <h2 class="fw-bold" id="font-custom">Dikirim Dengan Jasa Kurir Terpecaya</h2>
                            <h4 id="font-custom" class="text-capitalize">
                                <span class="hover">Dipastikan sampai kerumahmu tanpa kendala</span>
                            </h4>
                        </div>
                    </div><!--- Steps Wrap -->

                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <div class="row my-0">
                                <!-- Step -->
                                <div class="col-lg-4">
                                    <div class="text-center position-relative">
                                        <!-- Step Icon -->
                                        <div class="step-icon mx-auto border border-2 border rounded-circle d-flex align-items-center justify-content-center"
                                            style="width:150px;height:150px">
                                            <img src="/guest/apola_image/LOGO-JNT.png" class="m-3">

                                        </div>
                                    </div>
                                </div><!-- Step -->
                                <div class="col-lg-4">
                                    <div class="text-center position-relative">
                                        <!-- Step Icon -->
                                        <div class="step-icon mx-auto border border-2 border rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 150px;height: 150px;">
                                            <img src="/guest/apola_image/LOGO-TIKI.png" class="m-3">
                                        </div>

                                    </div>
                                </div><!-- Step -->
                                <div class="col-lg-4">
                                    <div class="text-center position-relative">
                                        <!--- Step Icon -->
                                        <div class="step-icon mx-auto border border-2 border rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 150px;height: 150px;">
                                            <img src="/guest/apola_image/LOGO-POS-IND.png" class="m-3">
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="features pt-5">
                <div class="container border bg-dark rounded-4 p-3">
                    <div class="row justify-content-center text-center py-4 ">
                        <div class="col-lg-8">
                            <span class="text-white">Temukan Kemudahan</span>
                            <h2 id="font-custom" class="display-5 fw-bold my-2 text-white">Ubah Gaya Hidup Anda dengan
                                Pilihan Fashion
                                Terbaik.</h2>
                            <div class="mx-auto py-2">
                                <a class="btn btn-outline-light btn-lg" href="{{ route('catalog-products') }}">Mulai
                                    Belanja</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


        </div>
    @endvolt
</x-guest-layout>
