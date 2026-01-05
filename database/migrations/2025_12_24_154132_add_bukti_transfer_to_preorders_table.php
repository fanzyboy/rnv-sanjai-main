<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('preorders', function (Blueprint $table) {

        // bukti transfer dari user
        $table->string('bukti_transfer')
              ->nullable()
              ->after('qty');

        // status otomatis pending
        $table->enum('status', [
            'pending',
            'proses',
            'selesai',
            'ditolak'
        ])->default('pending')
          ->after('bukti_transfer');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('preorders', function (Blueprint $table) {
        $table->dropColumn(['bukti_transfer', 'status']);
    });
}

};
