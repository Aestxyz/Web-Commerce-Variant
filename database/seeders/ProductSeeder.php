<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Category;
use App\Models\Variant;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                "image" => "https://down-id.img.susercontent.com/file/sg-11134201-7qvdy-lj2pp8i3uo0d26_tn.webp",
                "title" => "Epidemic T-Shirt American Cotton 20s Armless Black",
                "price" => "102000",
                "capital" => "85000",
                "quantity" => "50",
                "weight" => "200",
                "description" => "Kaos premium berbahan cotton 20s yang lembut dan nyaman. Warna hitam dengan desain minimalis tanpa lengan. Ideal untuk tampilan kasual.",
                "category_id" => 1 // T-Shirt Casual
            ],
            [
                "image" => "https://down-id.img.susercontent.com/file/id-11134207-7r98v-ltdgf5vpmz4954_tn.webp",
                "title" => "SUNDAYROSE T-Shirt Oversize Kaos Drink Steel Blue",
                "price" => "89000",
                "capital" => "70000",
                "quantity" => "30",
                "weight" => "250",
                "description" => "Kaos oversize berwarna steel blue dengan desain unik bertema minuman. Bahan nyaman dan cocok untuk gaya santai.",
                "category_id" => 1 // T-Shirt Casual
            ],
            [
                "image" => "https://down-id.img.susercontent.com/file/id-11134207-7rasl-m2d9dpfmoxc3ed_tn.webp",
                "title" => "Skaters DS Baseball Jersey XK007 Black",
                "price" => "199000",
                "capital" => "150000",
                "quantity" => "20",
                "weight" => "350",
                "description" => "Jersey baseball dengan desain klasik warna hitam. Cocok untuk penggemar gaya sporty.",
                "category_id" => 3 // Jersey Baseball
            ],
            [
                "image" => "https://down-id.img.susercontent.com/file/id-11134207-7rasj-m13rppmz5qo171_tn.webp",
                "title" => "Skaters Jersey Mets 80 XI004 White",
                "price" => "140000",
                "capital" => "110000",
                "quantity" => "25",
                "weight" => "300",
                "description" => "Jersey putih dengan logo Mets, dirancang untuk memberikan kenyamanan maksimal saat dikenakan.",
                "category_id" => 3 // Jersey Baseball
            ],
            [
                "image" => "https://down-id.img.susercontent.com/file/id-11134207-7rasg-m1do1jtjjq9120_tn.webp",
                "title" => "Skaters Baseball Jersey Flat XI033 Green",
                "price" => "155000",
                "capital" => "120000",
                "quantity" => "18",
                "weight" => "320",
                "description" => "Jersey baseball warna hijau dengan desain modern. Tersedia dalam berbagai ukuran.",
                "category_id" => 3 // Jersey Baseball
            ],
            [
                "image" => "https://down-id.img.susercontent.com/file/id-11134201-7r992-lr3ytgeeb7dta0_tn.webp",
                "title" => "VOLCOM - MSN ROCKER 2 STY",
                "price" => "250000",
                "capital" => "200000",
                "quantity" => "30",
                "weight" => "500",
                "description" => "Kaos Volcom MSN Rocker 2 dengan bahan berkualitas tinggi, ideal untuk gaya kasual.",
                "category_id" => 1 // T-Shirt Casual
            ],
            [
                "image" => "https://down-id.img.susercontent.com/file/id-11134207-7r992-lqwgotpu1ufac5_tn.webp",
                "title" => "Carhartt WIP x New Balance MADE in USA 990v6 Original",
                "price" => "3500000",
                "capital" => "3200000",
                "quantity" => "10",
                "weight" => "1200",
                "description" => "Sepatu kolaborasi Carhartt dan New Balance dengan desain eksklusif.",
                "category_id" => 5 // Sneakers
            ],
            [
                "image" => "https://down-id.img.susercontent.com/file/id-11134207-7r98u-lp7tsstvcw3y46_tn.webp",
                "title" => "KAOS KAKI SKATERS WG033 (PUTIH), WG034 (HITAM), WG035 (ABU)",
                "price" => "45000",
                "capital" => "30000",
                "quantity" => "50",
                "weight" => "100",
                "description" => "Kaos kaki premium untuk skater dengan tiga pilihan warna.",
                "category_id" => 6 // Socks
            ],
            [
                "image" => "https://down-id.img.susercontent.com/file/id-11134201-23020-ynpj6hb43lnve8.webp",
                "title" => "Sepatu Sneakers Tricks Pria Dewasa - Putih/Hitam",
                "price" => "202000",
                "capital" => "147000",
                "quantity" => "8000",
                "weight" => "800",
                "description" => "Sepatu Sneakers Tricks Ini Didesain Untuk Menunjang Berbagai Aktifitas Anda Sehari-Hari. Bagian Atas Menggunakan Kulit Sintetis Yang Berkualitas Dan Tahan Lama. Sepatu Ini Menggunakan Sol Sintetis Direct Injection Sehingga Daya Rekat Antara Bagian Atas Dengan Sol Sangat Kuat.",
                "category_id" => 5 // Sandals
            ],
            [
                "image" => "https://down-id.img.susercontent.com/file/id-11134207-7r98x-lt0fxktavsjt5b.webp",
                "title" => "Skaters Sandal Slide - Sol super lembut",
                "price" => "27900",
                "capital" => "19000",
                "quantity" => "35",
                "weight" => "700",
                "description" => "Koleksi Sandal dari ada lagi nih guys, sandal yang ringan dan nyaman untuk di pakai kemana saja. Dengan model yang simple, serta warna hitam pekat yang sangat cocok untuk Propeople pakai.",
                "category_id" => 7 // Sandals
            ],
            [
                "image" => "https://down-id.img.susercontent.com/file/id-11134207-7r98w-lxxs43zl4hhmfb_tn.webp",
                "title" => "(LW) LifeWork Sweatshirt Life Work Big Radog T-shirt Kaos Oversize Basic Tee Black Big Logo",
                "price" => "940499",
                "capital" => "800000",
                "quantity" => "20",
                "weight" => "500",
                "description" => "Sweatshirt Life Work Big Radog T-shirt dengan desain logo besar, cocok untuk gaya kasual.",
                "category_id" => 1 // T-Shirt Casual
            ],
            [
                "image" => "https://down-id.img.susercontent.com/file/c1e6f8d5688785aff2a964016bf42b7a_tn.webp",
                "title" => "Skaters Crewneck Title VF037 Black",
                "price" => "150000",
                "capital" => "120000",
                "quantity" => "30",
                "weight" => "700",
                "description" => "Crewneck Skaters dengan bahan berkualitas tinggi dan desain simpel.",
                "category_id" => 4 // Casual Wear
            ],
            [
                "image" => "https://down-id.img.susercontent.com/file/id-11134207-7qul1-lk7la9y2gq3faa_tn.webp",
                "title" => "Skaters Crewneck Respect WB093 Black",
                "price" => "145000",
                "capital" => "115000",
                "quantity" => "25",
                "weight" => "700",
                "description" => "Crewneck Skaters dengan desain modern dan bahan premium.",
                "category_id" => 4 // Casual Wear
            ],
            [
                "image" => "https://down-id.img.susercontent.com/file/b184e551ed6fb2b7f5834bfa957a5d38_tn.webp",
                "title" => "Switer Crewneck Terbaru 2024, Switer crewneck SKATERS distro pria-wanita, kualitas premium bahan tebal.",
                "price" => "78850",
                "capital" => "60000",
                "quantity" => "50",
                "weight" => "800",
                "description" => "Crewneck terbaru dari Skaters, cocok untuk pria dan wanita, dengan bahan premium yang tebal.",
                "category_id" => 4 // Casual Wear
            ],
            [
                "image" => "https://down-id.img.susercontent.com/file/id-11134207-7r98v-lof7bcfrxls0c3_tn.webp",
                "title" => "Skaters Oversized Sweater Hoodie Athletic WK034 Navy",
                "price" => "295000",
                "capital" => "250000",
                "quantity" => "15",
                "weight" => "900",
                "description" => "Sweater oversized hoodie dengan warna navy, desain sporty, cocok untuk aktivitas santai.",
                "category_id" => 4 // Casual Wear
            ]
        ];

        foreach ($data as $item) {
            // Upload gambar dari URL ke folder storage
            $imageContents = file_get_contents($item['image']);
            $imageName = basename($item['image']);
            $storagePath = 'images/' . $imageName;
            Storage::disk('public')->put($storagePath, $imageContents);

            // Buat record produk di database
            $product = Product::create([
                'category_id' => $item['category_id'],
                'title' => $item['title'],
                'capital' => $item['capital'],
                'price' => $item['price'],
                'quantity' => 0,
                'image' => $storagePath,
                'weight' => $item['weight'],
                'description' => $item['description'],
            ]);

            $type = ['XL', 'L', 'M', 'S'];
            foreach ($type as $variant) {
                Variant::create([
                    'product_id' => $product->id,
                    'type' => $variant,
                    'stock' => rand(10, 100),
                ]);
            }

            $this->command->info('Tambah Produk ' . $product->title);
        }
    }
}
