<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('mobile', 15)->nullable();
            $table->string('unit')->nullable();
            $table->decimal('rent_amount', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('buildings', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->nullable()->after('id');
            $table->unsignedBigInteger('tenant_id')->nullable()->after('property_id');

            $table->foreign('property_id')->references('id')->on('properties')->nullOnDelete();
            $table->foreign('tenant_id')->references('id')->on('tenants')->nullOnDelete();
        });

        // Migrate existing data
        $rows = DB::table('buildings')->select('building', 'name', 'mobile', 'amount')->distinct()->get();

        $propertyMap = [];
        $tenantMap = [];

        foreach ($rows as $row) {
            $bName = trim($row->building ?? '');
            if ($bName === '') continue;

            if (!isset($propertyMap[$bName])) {
                $pid = DB::table('properties')->insertGetId([
                    'name' => $bName,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $propertyMap[$bName] = $pid;
            }

            $tName = trim($row->name ?? '');
            $tKey = $bName . '||' . $tName;
            if ($tName !== '' && !isset($tenantMap[$tKey])) {
                $tid = DB::table('tenants')->insertGetId([
                    'property_id' => $propertyMap[$bName],
                    'name' => $tName,
                    'mobile' => $row->mobile,
                    'rent_amount' => is_numeric($row->amount) ? $row->amount : 0,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $tenantMap[$tKey] = $tid;
            }
        }

        // Link existing building records
        foreach ($propertyMap as $bName => $pid) {
            DB::table('buildings')
                ->where('building', $bName)
                ->update(['property_id' => $pid]);
        }

        foreach ($tenantMap as $key => $tid) {
            [$bName, $tName] = explode('||', $key, 2);
            DB::table('buildings')
                ->where('building', $bName)
                ->where('name', $tName)
                ->update(['tenant_id' => $tid]);
        }
    }

    public function down(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['property_id', 'tenant_id']);
        });

        Schema::dropIfExists('tenants');
        Schema::dropIfExists('properties');
    }
};
