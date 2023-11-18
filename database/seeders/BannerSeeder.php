<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\products;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        // Giả sử bạn có model 'Product'
        $products = products::all(); // Đổi tên model đúng

        // Bạn có thể điều chỉnh số lượng banner bạn muốn tạo
        $soLuongBanner = 10;

        // Sử dụng factory để tạo banner với dữ liệu giả mạo
        for ($i = 0; $i < $soLuongBanner; $i++) {
            DB::table('banners')->insert([
                'image' => $faker->imageUrl(),
                'description' => $faker->paragraph,
                'product_id' => $products->random()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
