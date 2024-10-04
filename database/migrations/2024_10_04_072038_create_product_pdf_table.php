<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPdfTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_pdf', function (Blueprint $table) {
            $table->id();
            $table->string('file_path')->nullable(); // Category name
            $table->string('product_id')->nullable(); // Category name
            $table->string('heading')->nullable(); // Category name
            
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
        Schema::dropIfExists('product_pdf');
    }
}
