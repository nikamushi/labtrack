<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $elektronik = \App\Models\Category::where('name', 'Elektronik')->first();
        $komputer = \App\Models\Category::where('name', 'Komputer')->first();
        $jaringan = \App\Models\Category::where('name', 'Jaringan')->first();
        $multimedia = \App\Models\Category::where('name', 'Multimedia')->first();

        // Electronics
        \App\Models\Item::create([
            'category_id' => $elektronik->id,
            'name' => 'Solder Listrik',
            'stock' => 10,
            'condition' => 'good',
            'status' => 'available'
        ]);

        // Komputer
        \App\Models\Item::create([
            'category_id' => $komputer->id,
            'name' => 'Laptop ASUS ROG',
            'stock' => 5,
            'condition' => 'good',
            'status' => 'available'
        ]);
        \App\Models\Item::create([
            'category_id' => $komputer->id,
            'name' => 'PC Monitor Dell 24"',
            'stock' => 8,
            'condition' => 'good',
            'status' => 'available'
        ]);

        // Jaringan
        \App\Models\Item::create([
            'category_id' => $jaringan->id,
            'name' => 'Router Cisco',
            'stock' => 3,
            'condition' => 'good',
            'status' => 'available'
        ]);
        \App\Models\Item::create([
            'category_id' => $jaringan->id,
            'name' => 'LAN Cable Tester',
            'stock' => 4,
            'condition' => 'maintenance',
            'status' => 'maintenance'
        ]);

        // Multimedia
        \App\Models\Item::create([
            'category_id' => $multimedia->id,
            'name' => 'Kamera Canon EOS 80D',
            'stock' => 3,
            'condition' => 'good',
            'status' => 'available'
        ]);
    }
}
