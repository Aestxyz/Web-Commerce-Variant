<?php

use Carbon\Carbon;
use App\Models\Order;
use function Laravel\Folio\name;
use function Livewire\Volt\{state, usesPagination, computed, mount};

name('transactions.index');

state(['search'])->url();
usesPagination();

state([
    'countOrders' => [],
    'ordersPerMonth ' => [],
]);

mount(function () {
    // Hitung jumlah pesanan berdasarkan status

    // Hitung jumlah pesanan berdasarkan status
    $statuses = ['PACKED', 'UNPAID', 'PROGRESS', 'COMPLETED', 'SHIPPED', 'PENDING', 'CANCELLED'];

    $this->countOrders = collect($statuses)
        ->mapWithKeys(function ($status) {
            // Menggunakan fungsi terjemahan Laravel untuk label status
            $label = __('status.' . $status);
            return [$label => Order::where('status', $status)->count()];
        })
        ->toArray();

    // Hitung jumlah pesanan per bulan dalam 1 tahun
    $this->ordersPerMonth = collect(range(1, 12))
        ->mapWithKeys(function ($month) {
            $monthName = Carbon::create()->month($month)->format('F');
            $orderCount = Order::whereMonth('created_at', $month)->whereYear('created_at', date('Y'))->count();
            return [$monthName => $orderCount];
        })
        ->toArray();
});

$orders = computed(function () {
    if ($this->search == null) {
        return Order::query()->latest()->paginate(10);
    } else {
        return Order::query()
            ->where('invoice', 'LIKE', "%{$this->search}%")
            ->orWhere('status', 'LIKE', "%{$this->search}%")
            ->orWhere('total_amount', 'LIKE', "%{$this->search}%")
            ->latest()
            ->paginate(10);
    }
});

?>


<x-admin-layout>
    <x-slot name="title">Transaksi</x-slot>
    <x-slot name="header">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('transactions.index') }}">Transaksi</a></li>
    </x-slot>

    @volt
        <div>

            <div class="card">
                <div class="card-body">
                    <div class="row gap-2 gap-md-0">
                        <!-- Line Segment Chart -->
                        <div class="col-md-8 col-12">
                            <h6 class="mb-2 text-lg font-semibold mb-5 text-center">Orders per Bulan</h6>
                            <canvas id="lineSegmentChart"></canvas>
                        </div>

                        <!-- Donut Chart -->
                        <div class="col-md-4 col-12">
                            <h6 class="mb-2 text-lg font-semibold mb-5 text-center">Status Orders</h6>
                            <canvas id="donutChart"></canvas>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Tambahkan Chart.js -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const statusData = @json($countOrders);
                    const ordersPerMonth = @json($ordersPerMonth);

                    const COLORS = ['#0088FE', '#FFBB28', '#00C49F', '#FF8042', '#A0A0A0', '#8884D8', '#FF4444'];

                    // Donut Chart
                    const donutCtx = document.getElementById('donutChart').getContext('2d');
                    new Chart(donutCtx, {
                        type: 'doughnut',
                        data: {
                            labels: Object.keys(statusData),
                            datasets: [{
                                data: Object.values(statusData),
                                backgroundColor: COLORS,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                                tooltip: {
                                    enabled: true
                                }
                            }
                        }
                    });

                    // Line Segment Chart (Orders per Month)
                    const lineCtx = document.getElementById('lineSegmentChart').getContext('2d');
                    new Chart(lineCtx, {
                        type: 'line',
                        data: {
                            labels: Object.keys(ordersPerMonth),
                            datasets: [{
                                label: 'Order Count',
                                data: Object.values(ordersPerMonth),
                                fill: false,
                                borderColor: '#00C49F',
                                borderWidth: 2,
                                tension: 0.4,
                                segment: {
                                    borderColor: function(context) {
                                        const index = context.p0DataIndex;
                                        return COLORS[index % COLORS.length];
                                    },
                                    borderWidth: 3,
                                },
                                pointBackgroundColor: COLORS,
                                pointRadius: 5,
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    enabled: true
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    stepSize: 1,
                                    title: {
                                        display: true,
                                        text: 'Jumlah Order',
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Bulan',
                                    }
                                }
                            }
                        }
                    });
                });
            </script>



            <div class="card">
                <div class="card-header">
                    <input wire:model.live="search" type="search" class="form-control" name="search" id="search"
                        aria-describedby="helpId" placeholder="Mencari transaksi..." />
                </div>


                <div class="card-body">
                    <div class="table-responsive border rounded">
                        <table class="table text-center text-nowrap">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Invoice</th>
                                    <th>Status</th>
                                    <th>Total Pesanan</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->orders as $no => $order)
                                    <tr>
                                        <th>{{ ++$no }}</th>
                                        <th>{{ $order->invoice }}</th>
                                        <th>
                                            <span class="badge bg-primary uppercase">
                                                {{ __(key: 'status.' . $order->status) }}
                                            </span>
                                        </th>
                                        <th>
                                            {{ 'Rp. ' . Number::format($order->total_amount, locale: 'id') }}
                                        </th>
                                        <th>
                                            <a href="/admin/transactions/{{ $order->id }}"
                                                class="btn btn-primary btn-sm">
                                                Detail Order
                                            </a>
                                        </th>
                                    </tr>
                                @endforeach
                            </tbody>
                            {{ $this->orders->links() }}
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endvolt
</x-admin-layout>
