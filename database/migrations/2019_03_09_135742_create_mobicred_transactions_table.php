<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobicredTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobicred.transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('merc_id');
            $table->integer('pf_instr_id');
            $table->string('pf_ord_no', 25);
            $table->decimal('amount', 13,2);
            $table->integer('mcr_ref_no');
            $table->integer('mcr_resp_code');
            $table->string('mcr_status', 50);
            $table->string('mcr_response');
            $table->string('mcr_warning_msg', 200)->nullable();
            $table->string('mcr_error_msg', 200)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobicred.transactions');
    }
}
