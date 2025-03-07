<?php

use function Livewire\Volt\{state, rules, computed, uses};
use App\Models\Product;
use App\Models\Variant;
use App\Models\Cart;
use App\Models\User;
use function Laravel\Folio\name;
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

name('product-detail');

state([
    'user_id' => fn() => Auth()->user()->id ?? '',
    'product_id' => fn() => $this->product->id,
    'variant_id' => '',
    'randomProduct' => fn() => Product::inRandomOrder()->limit(6)->get(),
    'qty' => 1,
    'variant_type' => '',
    'variant_stock' => '',
    'variant' => '',
    'product',
]);

rules([
    'user_id' => 'required|exists:users,id',
    'product_id' => 'required|exists:products,id',
    'variant_id' => 'required|exists:variants,id',
    'qty' => 'required|numeric',
]);

$selectVariant = function (Variant $variant) {
    $this->variant = $variant->stock;
    $this->variant_id = $variant->id;
    $this->variant_type = $variant->type;
    $this->variant_stock = $variant->stock;
};

$addToCart = function (Product $product) {
    if (Auth::check() && auth()->user()->role == 'customer') {
        $existingCart = Cart::where('user_id', $this->user_id)->where('variant_id', $this->variant_id)->first();

        $stock = $this->variant_stock;

        // Memeriksa apakah stok mencukupi
        if ($stock < $this->qty) {
            $this->alert('error', 'Stok tidak mencukupi untuk menambahkan item ke keranjang.', [
                'position' => 'top',
                'timer' => '2000',
                'toast' => true,
                'timerProgressBar' => true,
                'text' => '',
            ]);
            return;
        }

        if ($existingCart) {
            $newQty = $existingCart->qty + $this->qty;

            // Memeriksa apakah stok mencukupi untuk jumlah baru
            if ($stock < $newQty) {
                $this->alert('error', 'Stok tidak mencukupi untuk menambahkan item ke keranjang.', [
                    'position' => 'top',
                    'timer' => '2000',
                    'toast' => true,
                    'timerProgressBar' => true,
                    'text' => '',
                ]);
                return;
            }

            $existingCart->update(['qty' => $newQty]);
        } else {
            Cart::create($this->validate());
        }

        $this->dispatch('cart-updated');

        $this->alert('success', 'Item berhasil ditambahkan ke dalam keranjang belanja.', [
            'position' => 'top',
            'timer' => '2000',
            'toast' => true,
            'timerProgressBar' => true,
            'text' => '',
        ]);
    } else {
        $this->redirect('/login');
    }
};

?>
<x-guest-layout>
    <x-slot name="title">Product {{ $product->title }}</x-slot>
    @include('layouts.fancybox')
    @volt
        <div>
            <section class="pt-5">
                <div class="container mb-5">
                    <div class="row">
                        <div class="col-lg-6">
                            <h2 id="font-custom" class="display-2 fw-bold">
                                Detail Produk
                            </h2>
                        </div>
                        <div class="col-lg-6 mt-lg-0 align-content-center">
                            <p>
                                Hadirkan gaya hidup urban dan trendi dengan <span
                                    class="fw-bold">{{ $product->title }}</span> dari lini streetwear kami.
                            </p>
                        </div>
                    </div>

                </div>
            </section>

            <section class="pb-5">
                <div class="container">
                    <div class="row gx-2">
                        <aside class="col-lg-6">
                            <div class="card rounded-4 mb-3" style="width: 100%; height: 550px">
                                <a href="{{ Storage::url($product->thumbnail) }}" data-fancybox
                                    data-src="{{ Storage::url($product->thumbnail) }}">
                                    <img class="card-img-top" src="{{ Storage::url($product->thumbnail) }}" width=100%;
                                        height=550px; style="object-fit: cover;" alt="card-img-top">
                                </a>
                            </div>

                            <div class="d-flex flex-row gap-1 overflow-auto">
                                @foreach ($product->images as $imageItem)
                                    <div class="col">
                                        <div class="card rounded-4 mb-3" style="width: 100px; height: 100px">
                                            <a href="{{ Storage::url($imageItem->image_path) }}" data-fancybox="gallery"
                                                data-caption="{{ $product->title }}">
                                                <img class="card-img-top" src="{{ Storage::url($imageItem->image_path) }}"
                                                    width=100px; height=100px; style="object-fit: cover;"
                                                    alt="other images">
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </aside>
                        <main class="col-lg-6">
                            <div class="ps-lg-3">
                                <small class="fw-bold" style="color: #f35525;">{{ $product->category->name }}</small>
                                <h2 id="font-custom" class="title text-dark fw-bold">
                                    {{ $product->title }}
                                </h2>

                                <div class="my-3">
                                    <span class="h5 fw-bold" style="color: #f35525;">
                                        {{ 'Rp. ' . Number::format($product->price, locale: 'id') }}
                                    </span>
                                </div>

                                <p class="mb-3">
                                    {{ $product->description }}
                                </p>

                                <div class="row">
                                    <dt class="col-3 mb-2">Berat:</dt>
                                    <dd class="col-9 mb-2">{{ $product->weight }} Gram</dd>

                                    <dt class="col-3 mb-2">Stok:</dt>
                                    <dd class="col-9 mb-2">
                                        {{ $variant ?? '-' }}</dd>

                                    <dt class="col-3 mb-2">Varian:</dt>
                                    <dd class="col-9 mb-2">{{ $variant_type ?? '-' }}</dd>
                                </div>

                                <div class="row mt-3">
                                    <p>Varian produk tersedia:</p>
                                    @foreach ($product->variants as $variant)
                                        <div class="col-6 mb-3">
                                            <button wire:key='{{ $variant->id }}'
                                                wire:click='selectVariant({{ $variant->id }})' type="button"
                                                class="btn btn-light w-100 tex-center border border-dark" style="color: #f35525;">
                                                {{ $variant->type }}
                                            </button>
                                        </div>
                                    @endforeach
                                </div>



                                <div class="d-grid my-4">
                                    @auth
                                        <form wire:submit='addToCart'>
                                            @if ($variant)
                                                <button wire:key="{{ $product->id }}" type="submit"
                                                    class="btn btn-dark w-100">

                                                    <span
                                                        wire:loading.remove>{{ $variant_stock == 0 ? 'Tidak Tersedia' : 'Masukkan Keranjang' }}
                                                    </span>

                                                    <div wire:loading class="spinner-border spinner-border-sm" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </button>
                                            @endif
                                        </form>
                                        @error('variant_id')
                                            <small class="my-3 text-center text-danger">
                                                Plih ukuran/variant yang diinginkan
                                            </small>
                                        @enderror
                                    @else
                                        <a class="btn btn-dark" href="{{ route('login') }}" role="button">Beli Sekarang</a>
                                    @endauth
                                </div>
                            </div>
                        </main>
                    </div>
                </div>
            </section>
            <!-- content -->

            <div class="properties section">
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <div class="section-heading mb-2">
                                <h3 id="font-custom" class="fw-bold">Rekomendasi Produk Lainnya</h3>
                                <h6 class="text-capitalize">Temukan berbagai produk menarik yang mungkin kamu suka dan dapat
                                    melengkapi pengalaman berbelanja kamu.</h6>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @foreach ($randomProduct as $product)
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
        </div>
    @endvolt
</x-guest-layout>
