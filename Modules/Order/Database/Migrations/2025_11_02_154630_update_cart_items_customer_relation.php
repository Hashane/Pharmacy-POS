<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'customer_user_id')) {
                $table->dropForeign(['customer_user_id']);
                $table->dropColumn('customer_user_id');
            }

            $table->foreignId('customer_id')
                ->after('id')
                ->constrained('customers')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');

            $table->foreignId('customer_user_id')
                ->nullable()
                ->constrained('customer_users')
                ->cascadeOnDelete();
        });
    }
};
