<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vk_subcategories', function (Blueprint $table) {
            
            $table->id();
            $table->unsignedBigInteger('id_category')->index()->comment('comment:attribute');
            $table->string('subcatName', 80)->index()->unique()->comment('comment:attribute');
            
            $table->timestamps();

            
            $table->foreign('id_category')->references('id')->on('vk_categories')->onUpdate('cascade')->onDelete('cascade');
            
        });
            $statement = "ALTER TABLE vk_subcategories AUTO_INCREMENT = 20000;";
            DB::unprepared($statement);
    }
    //
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vk_subcategories');
    }
};
