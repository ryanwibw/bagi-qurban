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
        // 1. Create pivot table
        Schema::create('organization_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['admin', 'panitia'])->default('panitia');
            $table->timestamps();
        });

        // 2. Add owner_id to organizations
        Schema::table('organizations', function (Blueprint $table) {
            $table->foreignId('owner_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
        });

        // 3. Data migration
        $users = DB::table('users')->whereNotNull('organization_id')->get();
        foreach ($users as $user) {
            DB::table('organization_user')->insert([
                'organization_id' => $user->organization_id,
                'user_id' => $user->id,
                'role' => $user->role,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Set the first admin as owner
            if ($user->role === 'admin') {
                DB::table('organizations')
                    ->where('id', $user->organization_id)
                    ->whereNull('owner_id')
                    ->update(['owner_id' => $user->id]);
            }
        }

        // 4. Cleanup users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn(['organization_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('role', ['admin', 'panitia'])->default('panitia');
        });

        $pivots = DB::table('organization_user')->get();
        foreach ($pivots as $pivot) {
            DB::table('users')->where('id', $pivot->user_id)->update([
                'organization_id' => $pivot->organization_id,
                'role' => $pivot->role,
            ]);
        }

        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropColumn('owner_id');
        });

        Schema::dropIfExists('organization_user');
    }
};
