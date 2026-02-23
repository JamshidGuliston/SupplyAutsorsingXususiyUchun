<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKindgardenRequisitesTable extends Migration
{
    public function up()
    {
        Schema::create('kindgarden_requisites', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('kindgarden_id');
            $table->string('director_name')->nullable();
            $table->string('director_phone')->nullable();
            $table->string('reception_phone')->nullable();
            $table->string('address')->nullable();
            $table->string('inn')->nullable();
            $table->string('bank_account')->nullable();   // X/R
            $table->string('mfo')->nullable();
            $table->string('treasury_account')->nullable(); // Yagona g'azna
            $table->string('bank')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->foreign('kindgarden_id')->references('id')->on('kindgardens')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kindgarden_requisites');
    }
}
