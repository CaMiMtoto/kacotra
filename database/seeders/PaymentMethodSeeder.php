<?php

namespace Database\Seeders;

use App\Models\Method;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            ['id' => 1, 'name' => 'Cash In Hand', 'code' => 'HandCash'],
            ['id' => 2, 'name' => 'Mobile Money', 'code' => 'MoMo'],
            ['id' => 3, 'name' => 'Cheque', 'code' => 'Cheque'],
            ['id' => 4, 'name' => 'Due', 'code' => 'Due'],
        ];

        if (Method::query()->exists())
            return;

        foreach ($methods as $method) {
            $slug = Str::slug($method['name']);
            $method['slug'] = $slug;
            Method::create($method);
        }
    }
}
