<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\products;
use App\Models\ProductImage;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 20; $i++) {
            $productData = [
                'name' => $faker->word,
                'description' => $faker->sentence,
                'price' => $faker->randomFloat(2, 10, 1000),
                'qr_code' => $faker->uuid,
                'image' => $faker->imageUrl(),
                'category_id' => $faker->numberBetween(1, 5),
            ];

            $product = products::create($productData);

            // Tạo và liên kết 2 hình ảnh với sản phẩm
            for ($j = 0; $j < 2; $j++) {
                $imageData = [
                    'image_path' => $faker->imageUrl(),
                ];

                $product->images()->create($imageData);
            }
        }
    }
}


