<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $products = DB::table('products')
            ->where('paid_amount', '>', 0)
            ->get();

        foreach ($products as $product) {
            DB::table('payment_histories')->insert([
                'entry_id' => $product->entry_id,
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'amount' => $product->paid_amount,
                'payment_date' => $product->payment_date ?? now()->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('payment_histories')->truncate();
    }
};
