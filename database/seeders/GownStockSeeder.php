<?php

namespace Database\Seeders;

use App\Models\GownStock;
use Illuminate\Database\Seeder;

class GownStockSeeder extends Seeder
{
    public function run()
    {
        $sizes = ['XS', 'S', 'M', 'L', 'XL'];
        foreach ($sizes as $size) {
            GownStock::create([
                'size' => $size,
                'total' => 50, // adjust per size
                'issued' => 0,
                'available' => 50,
            ]);
        }
    }
}
