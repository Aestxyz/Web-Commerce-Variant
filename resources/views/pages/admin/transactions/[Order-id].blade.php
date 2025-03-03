<?php

use function Livewire\Volt\{state, rules, uses};
use Dipantry\Rajaongkir\Constants\RajaongkirCourier;
use App\Models\Order;
use App\Models\Variant;
use App\Models\Item;
use function Laravel\Folio\name;
use Jantinnerezo\LivewireAlert\LivewireAlert;

uses([LivewireAlert::class]);

name('transactions.show');

state([
    'order' => fn() => Order::find($id),
    'orderItems' => fn() => Item::where('order_id', $this->order->id)->get(),
    'tracking_number',
]);

rules([
    'tracking_number' => 'required|min:10',
]);

$confirm = function () {
    if ($this->order->courier == 'Ambil Sendiri') {
        $this->order->update(['status' => 'PICKUP']);
    } else {
        $this->order->update(['status' => 'PACKED']);
    }

    $this->dispatch('orders-alert');
};

$saveTrackingNumber = function () {
    $validate = $this->validate();
    $validate['status'] = 'SHIPPED';

    $this->order->update($validate);
    $this->alert('success', 'Pesanan telah di inputkan resi!', [
        'position' => 'top',
        'timer' => 3000,
        'toast' => true,
    ]);

    $this->dispatch('orders-alert');
};

$cancelOrder = function ($orderId) {
    $order = $this->order;

    $orderItems = Item::where('order_id', $order->id)->get();
    foreach ($orderItems as $orderItem) {
        $variant = Variant::findOrFail($orderItem->variant_id);
        $newQuantity = $variant->stock + $orderItem->qty;

        // Memperbarui quantity pada tabel produk
        $variant->update(['stock' => $newQuantity]);
    }
    $order->update(['status' => 'CANCELLED']);

    $this->dispatch('orders-alert');
    $this->alert('success', 'Pesanan telah di batalkan!', [
        'position' => 'top',
        'timer' => 3000,
        'toast' => true,
    ]);
};

$complatedOrder = fn() => $this->order->update(['status' => 'COMPLETED']);

?>
<x-admin-layout>
    @include('layouts.fancybox')

    <x-slot name="title">Transaksi {{ $order->invoice }}</x-slot>
    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('transactions.index') }}">Transaksi</a></li>
        <li class="breadcrumb-item"><a
                href="{{ route('transactions.show', ['order' => $order->id]) }}">{{ $order->invoice }}</a></li>
    </x-slot>
    @volt
        <div>
            <div class="row align-items-center d-print-none mb-3">
                <div class="col-md col-12 text-center text-md-start">
                    <h1 class="fw-bolder pt-3">
                        {{ $order->invoice }}
                    </h1>
                </div>
                <div class="col-md col-12">
                    <div class="d-flex justify-content-between gap-2">
                        @if ($order->status == 'PENDING')
                            <button wire:click='confirm' class="btn w-100 btn-primary" type="submit">
                                <i class="d-block d-lg-none ti ti-circle-check fs-6"></i>
                                <span class="d-none d-lg-block ">Proses Pesanan</span>
                                
                            </button>
                        @endif
                        @if (
                            $order->status === 'PENDING' ||
                                $order->status === 'PICKUP' ||
                                ($order->status === 'PACKED' && auth()->user()->role === 'superadmin'))
                            <button class="btn w-100 btn-danger" wire:click="cancelOrder('{{ $order->id }}')" role="button">
                                <i class="d-block d-lg-none ti ti-x fs-6"></i>
                                <span class="d-none d-lg-block ">Batal Pesanan</span>
                                
                            </button>
                            @endif
                            
                            <button class="btn w-100 btn-dark print-page" onclick="window.print()" type="button">
                                <i class=" d-block d-lg-none ti ti-printer fs-6"></i>
                                
                                <span class="d-none d-lg-block ">Cetak Invoice</span>
                        </button>
                    </div>
                </div>
            </div>


            <div class="alert alert-primary {{ $order->status === 'PACKED' ?: 'd-none' }}" role="alert">
                <form wire:submit="saveTrackingNumber">
                    <div class="input-group border border-dark rounded">
                        <input wire:model="tracking_number" id="tracking_number" type="text"
                            class="form-control  @error('tracking_number') is-invalid @enderror"
                            placeholder="Masukan nomor resi pesanan yang dikirim...">

                        <button class="btn btn-primary  rounded-end-1" type="submit">
                            Submit
                        </button>
                    </div>
                    @error('tracking_number')
                        <small id="tracking_numberId" class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </form>
            </div>


            @if ($order->status == 'CANCELLED')
                <div class="alert alert-primary" role="alert">
                    <h4 class="alert-heading">Pemberitahuan!</h4>
                    <p>
                        Kami mohon agar Anda menghubungi pembeli untuk mengonfirmasi pembatalan pesanan melalui nomor telepon yang tertera. 
                        @if ($order->payment_method != 'COD (Cash On Delivery)' && $order->status === 'PICKUP')
                            <span> Dan lakukan pengembalian dana kepada customer</span>
                        @endif
                    </p>

                    <p>Terima kasih atas perhatian Anda.</p>

                </div>
            @endif

            <div class="card d-print-block">
                <div class="card-body">
                    <div class="invoice-123" id="printableArea" style="display: block;">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div>
                                            <ul>
                                                <h6>Pesanan Dari</h6>
                                                <li>
                                                    {{ $order->user->name }} - {{ __('status.' . $order->status) }}
                                                </li>
                                                <li>{{ $order->user->email }}</li>
                                                <li> {{ $order->user->telp }}</li>

                                                <h6 class="mt-3">Alamat</h6>
                                                <li> {{ $order->user->address->province->name }},</li>
                                                <li> {{ $order->user->address->city->name }} </li>
                                                <li>
                                                    {{ $order->user->address->details }}
                                                </li>

                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-start text-md-end">
                                            <ul>
                                                <h6>Faktur</h6>
                                                <li>Nomor Resi Pesanan:
                                                    {{ $order->tracking_number ?? '-' }}
                                                </li>
                                                <li>Pengiriman:
                                                    {{ $order->courier }}
                                                </li>
                                                <li>Tambahan:
                                                    {{ $order->protect_cost == true ? 'Bubble Wrap' : '-' }}
                                                </li>
                                                <li>Metode Pembayaran:
                                                    {{ $order->payment_method }}
                                                </li>

                                                @if ($order->payment_method == 'Transfer Bank')
                                                    <h6 class="mt-3">
                                                        Bukti Pembayaran
                                                    </h6>
                                                    <li>
                                                        <a href="{{ Storage::url($order->proof_of_payment) }}"
                                                            data-fancybox target="_blank">
                                                            <img src="{{ Storage::url($order->proof_of_payment) }}"
                                                                class="figure-img img rounded object-fit-cover
                                                    {{ !$order->proof_of_payment ? 'placeholder' : '' }}"
                                                                width="80" height="80" alt="...">
                                                        </a>

                                                    </li>
                                                @endif
                                            </ul>
                                        </div>

                                    </div>
                                </div>

                                <hr>
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive mt-3">
                                    <table class="table table-borderless table-sm">
                                        <thead>
                                            <!-- start row -->
                                            <tr class="border">
                                                <th class="text-center">#</th>
                                                <th>Produk</th>
                                                <th class="text-center">Variant</th>
                                                <th class="text-center">Kuantitas</th>
                                                <th class="text-center">Harga Satuan</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                            <!-- center row -->
                                        </thead>
                                        <tbody>
                                            @foreach ($orderItems as $no => $item)
                                                <!-- start row -->
                                                <tr class="border">
                                                    <td class="text-center">{{ ++$no }}</td>
                                                    <td>{{ Str::limit($item->product->title, 30, '...') }}</td>
                                                    <td class="text-center">{{ $item->variant->type }}</td>
                                                    <td class="text-center">{{ $item->qty }} Item</td>
                                                    <td class="text-center">
                                                        {{ 'Rp.' . Number::format($item->product->price, locale: 'id') }}
                                                    </td>
                                                    <td class="text-end">
                                                        {{ 'Rp.' . Number::format($item->product->price * $item->qty, locale: 'id') }}
                                                    </td>
                                                </tr>
                                                <!-- end row -->
                                            @endforeach
                                        </tbody>

                                        <tfoot class="table-sm text-end">
                                            <tr>
                                                <td colspan="5"> Sub - Total:</td>
                                                <td>
                                                    {{ 'Rp.' .
                                                        Number::format(
                                                            $order->items->sum(function ($item) {
                                                                return $item->qty * $item->product->price;
                                                            }),
                                                            locale: 'id',
                                                        ) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5"> Berat Barang:</td>
                                                <td>
                                                    {{ $order->total_weight }} gram
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5"> Biaya Pengiriman:</td>
                                                <td>
                                                    {{ 'Rp.' . Number::format($order->shipping_cost, locale: 'id') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5"> Biaya Tambahan:</td>
                                                <td>
                                                    {{ $order->protect_cost == true ? 'Rp.' . Number::format(3000, locale: 'id') : 'Rp. 0' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="fw-bolder"> Total:</td>
                                                <td class="fw-bolder">
                                                    {{ 'Rp.' . Number::format($order->total_amount, locale: 'id') }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endvolt
    </x-app-layout>
