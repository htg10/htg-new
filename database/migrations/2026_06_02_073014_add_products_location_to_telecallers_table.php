<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('telecallers', function (Blueprint $table) {
            $table->json('products')->nullable()->after('interest');
            $table->decimal('latitude', 10, 7)->nullable()->after('products');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('location_accuracy')->nullable()->after('longitude');
            $table->text('location_url')->nullable()->after('location_accuracy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('telecallers', function (Blueprint $table) {
            $table->dropColumn([
                'products',
                'latitude',
                'longitude',
                'location_accuracy',
                'location_url'
            ]);
        });
    }
};
