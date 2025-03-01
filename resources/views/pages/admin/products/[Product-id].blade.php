<?php

use function Livewire\Volt\{state, rules, usesFileUploads, uses, computed};
use function Laravel\Folio\name;
use App\Models\Category;
use App\Models\Product;
use App\Models\Image;
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

name('products.edit');
usesFileUploads();

state([
    'categories' => fn() => Category::get(),
    'category_id' => fn() => $this->product->category_id,
    'title' => fn() => $this->product->title,
    'capital' => fn() => $this->product->capital,
    'price' => fn() => $this->product->price,
    'weight' => fn() => $this->product->weight,
    'description' => fn() => $this->product->description,
    'productId' => fn() => $this->product->id,
    'thumbnail',
    'product',

    'images' => [],
]);

$profit = computed(function () {
    $capital = is_numeric($this->capital) ? $this->capital : 0;
    $price = is_numeric($this->price) ? $this->price : 0;
    $gap = $price - $capital;
    return Number::format($gap, locale: 'id');
});

rules([
    'category_id' => 'required|exists:categories,id',
    'title' => 'required|min:5',
    'capital' => 'required|numeric|min:0',
    'price' => [
        'required',
        'numeric',
        'gte:capital', // Validasi bahwa harga jual tidak boleh kurang dari harga modal
    ],
    'thumbnail' => 'nullable',
    'weight' => 'required|numeric',
    'description' => 'required|min:10',
]);

$updatingImages = function ($value) {
    $this->previmages = $this->images;
};

$updatedImages = function ($value) {
    $this->images = array_merge($this->previmages, $value);
};

$removeItem = function ($key) {
    if (isset($this->images[$key])) {
        $file = $this->images[$key];
        $file->delete();
        unset($this->images[$key]);
    }

    $this->images = array_values($this->images);
};

$save = function () {
    $validate = $this->validate();
    if ($this->thumbnail) {
        $validate['thumbnail'] = $this->thumbnail->store('public/thumbnails');
        Storage::delete($this->product->thumbnail);
    } else {
        $validate['thumbnail'] = $this->product->thumbnail;
    }

    $product = Product::find($this->product->id);

    if ($product) {
        $product->update($validate);

        if (count($this->images) > 0) {
            $this->validate([
                'images' => 'nullable',
                'images.*' => 'image|max:2048',
            ]);

            $images = Image::where('product_id', $product->id)->get();

            if ($images->isNotEmpty()) {
                foreach ($images as $image) {
                    Storage::delete($image->image_path);
                    $image->delete();
                }
            }

            foreach ($this->images as $image) {
                $path = $image->store('fields', 'public');
                Image::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                ]);

                $image->delete();
            }
        }

        $this->alert('success', 'Penginputan produk toko telah selesai dan lengkapi dengan menambahkan varian produk!', [
            'position' => 'top',
            'width' => '500',
            'toast' => true,
            'timerProgressBar' => true,
        ]);
    } else {
        $this->alert('error', 'Produk tidak ditemukan!', [
            'position' => 'top',
            'width' => '500',
            'toast' => true,
            'timerProgressBar' => true,
        ]);
    }

    $this->redirectRoute('products.edit', ['product' => $product->id]);
};

$redirectProductsPage = function () {
    $this->redirectRoute('products.index');
};

?>
<x-admin-layout>
    <x-slot name="title">Produk</x-slot>
    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk Toko</a></li>
        <li class="breadcrumb-item"><a
                href="{{ route('products.edit', ['product' => $product->id]) }}">{{ $product->title }}</a></li>
    </x-slot>

    @volt
        <div>
            <div class="card">
                <div class="card-body">
                    <form wire:submit="save" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md mb-3">
                                @if ($thumbnail)
                                    <img src="{{ $thumbnail->temporaryUrl() }}" class="img rounded object-fit-cover"
                                        alt="thumbnail" loading="lazy" height="625px" width="100%" />
                                @elseif ($product->thumbnail)
                                    <img src="{{ Storage::url($product->thumbnail) }}" class="img rounded object-fit-cover"
                                        alt="thumbnail" loading="lazy" height="625px" width="100%" />
                                @endif
                            </div>
                            <div class="col-md">

                                <div class="mb-3">
                                    <label for="title" class="form-label">Nama Produk</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        wire:model="title" id="title" aria-describedby="titleId"
                                        placeholder="Enter product title"
                                        {{ auth()->user()->role == 'superadmin' ?: 'disabled' }} />
                                    @error('title')
                                        <small id="titleId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="capital" class="form-label">Harga Modal</label>
                                    <input type="number" class="form-control @error('capital') is-invalid @enderror"
                                        wire:model.live="capital" min="0" id="capital" aria-describedby="capitalId"
                                        placeholder="Enter product capital"
                                        {{ auth()->user()->role == 'superadmin' ?: 'disabled' }} />
                                    @error('capital')
                                        <small id="capitalId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="price" class="form-label">Harga Jual</label>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror"
                                        wire:model.live="price" min="0" id="price" aria-describedby="priceId"
                                        placeholder="Enter product price"
                                        {{ auth()->user()->role == 'superadmin' ?: 'disabled' }} />
                                    @error('price')
                                        <small id="priceId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="profit" class="form-label">Keuntungan Jual /
                                        <small class="text-primary">Perproduk</small></label>
                                    <input type="text" class="form-control" value="{{ $this->profit }}" name="profit"
                                        id="profit" aria-describedby="helpId" placeholder="profit" disabled />
                                </div>


                                <div class="mb-3">
                                    <label for="thumbnail" class="form-label">Gambar Produk</label>
                                    <input type="file" class="form-control @error('thumbnail') is-invalid @enderror"
                                        wire:model="thumbnail" id="thumbnail" aria-describedby="thumbnailId"
                                        placeholder="Enter product thumbnail"
                                        {{ auth()->user()->role == 'superadmin' ?: 'disabled' }} />
                                    @error('thumbnail')
                                        <small id="thumbnailId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Kategori Produk</label>
                                    <select class="form-select" wire:model="category_id" id="category_id"
                                        {{ auth()->user()->role == 'superadmin' ?: 'disabled' }}>
                                        <option>Pilih salah satu</option>
                                        @foreach ($this->categories as $category)
                                            <option value="{{ $category->id }}">- {{ $category->name }}</option>
                                        @endforeach
                                    </select>

                                    @error('category_id')
                                        <small id="category_id" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="weight" class="form-label">Berat Produk</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('weight') is-invalid @enderror"
                                            wire:model="weight" id="weight" aria-describedby="weightId"
                                            placeholder="Enter product weight"
                                            {{ auth()->user()->role == 'superadmin' ?: 'disabled' }} />
                                        <span class="input-group-text rounded-end-1" id="basic-addon2">gram</span>
                                    </div>
                                    @error('weight')
                                        <small id="weightId" class="form-text text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                            </div>

                            <div class="col-12">
                                @if ($images)
                                    <div class="mb-3 row d-flex flex-nowrap gap-1 overflow-auto">
                                        @foreach ($images as $key => $image)
                                            <div class="col-3">
                                                <div class="card position-relative mt-6" style="width: 200px;">
                                                    <div class="card-img-top">
                                                        <img src="{{ $image->temporaryUrl() }}" class="img rounded"
                                                            style="object-fit: cover;" width="200px" height="200px"
                                                            alt="preview">
                                                        <a type="button"
                                                            class="position-absolute top-0 start-100 translate-middle p-2"
                                                            wire:click.prevent='removeItem({{ json_encode($key) }})'>
                                                            <i
                                                                class="bx bx-x p-2 rounded-circle fs-1 text-white bg-danger"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($product->images->isNotEmpty())
                                    <div class="mb-3 row d-flex flex-nowrap gap-1 overflow-auto">
                                        @foreach ($product->images as $key => $image)
                                            <div class="col-3">
                                                <div class="card position-relative" style="width: 200px;">
                                                    <div class="card-img-top">
                                                        <img src="{{ Storage::url($image->image_path) }}"
                                                            class="img rounded" style="object-fit: cover;" width="200px"
                                                            height="200px" alt="preview">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label for="images" class="form-label">
                                    Gambar Lainnya
                                    <span wire:loading.remove.class="d-none"
                                        class="d-none ms-2 spinner-border spinner-border-sm"></span>
                                </label>
                                <p>
                                    <small>Gambar tersimpan
                                        <span class="text-danger">(Jika tidak mengubah gambar, tidak perlu melakukan
                                            input gambar)</span>
                                        .
                                    </small>
                                </p>
                                <input type="file" class="form-control @error('images') is-invalid @enderror"
                                    wire:model="images" id="images" aria-describedby="imagesId" autocomplete="images"
                                    accept="image/*" multiple />
                                @error('images')
                                    <small id="imagesId" class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Penjelasan Produk</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" wire:model="description" id="description"
                                    aria-describedby="descriptionId" placeholder="Enter product description" rows="8"
                                    {{ auth()->user()->role == 'superadmin' ?: 'disabled' }}></textarea>

                                @error('description')
                                    <small id="descriptionId" class="form-text text-danger">{{ $message }}</small>
                                @enderror
                            </div>


                            <div class="text-start">
                                <button type="submit"
                                    class="btn btn-primary {{ auth()->user()->role == 'superadmin' ?: 'd-none' }}">
                                    {{ $productId == null ? 'Submit' : 'Edit' }}
                                </button>
                                <x-action-message wire:loading on="save">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </x-action-message>
                            </div>
                    </form>
                </div>

                <hr>

                @if ($productId)
                    @livewire('pages.products.createOrUpdateVariants', ['productId' => $productId, 'title' => $title])

                    <button type="button" wire:click='redirectProductsPage' class="btn btn-primary">Selesai</button>
                @endif
            </div>
        </div>
    @endvolt
</x-admin-layout>
