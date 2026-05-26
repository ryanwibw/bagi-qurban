<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->integer('serial_number')->nullable()->after('id');
        });

        // Initialize serial numbers for existing coupons
        $coupons = DB::table('coupons')->orderBy('id')->get();
        foreach ($coupons as $coupon) {
            $max = DB::table('coupons')
                ->where('organization_id', $coupon->organization_id)
                ->where('id', '<', $coupon->id)
                ->max('serial_number') ?? 0;
            
            DB::table('coupons')->where('id', $coupon->id)->update(['serial_number' => $max + 1]);
        }

        Schema::table('coupons', function (Blueprint $table) {
            $table->unique(['organization_id', 'serial_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropUnique(['organization_id', 'serial_number']);
            $table->dropColumn('serial_number');
        });
    }
};
