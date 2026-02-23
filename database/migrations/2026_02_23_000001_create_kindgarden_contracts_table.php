<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKindgardenContractsTable extends Migration
{
    public function up()
    {
        Schema::create('kindgarden_contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('kindgarden_id');
            $table->string('contract_number');
            $table->date('contract_date');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->foreign('kindgarden_id')->references('id')->on('kindgardens')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kindgarden_contracts');
    }
}
