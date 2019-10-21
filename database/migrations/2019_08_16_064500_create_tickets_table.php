<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('name');
            $table->String('email');
            $table->double('price');
            $table->integer('quantity');
            // $table->String('type');
            $table->boolean('is_paid')->default(false);
            $table->unsignedInteger('type_id');
            $table->unsignedInteger('product_id');
            $table->String('image')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
