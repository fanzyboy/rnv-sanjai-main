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
        Schema::table('preorders', function (Blueprint $table) {
            $table->decimal('refund_amount', 15, 0)
                  ->nullable()
                  ->after('total_amount');

            $table->string('bukti_admin')
                  ->nullable()
                  ->after('refund_amount');

            $table->timestamp('refund_at')
                  ->nullable()
                  ->after('bukti_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preorders', function (Blueprint $table) {
            $table->dropColumn([
                'refund_amount',
                'bukti_admin',
                'refund_at'
            ]);
        });
    }
};
