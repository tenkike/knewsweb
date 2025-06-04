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
        Schema::create('vk_menus', function (Blueprint $table) {
           
            $table->id();
            $table->enum('status', [0, 1])->default(0)->comment('comment:attribute');
    	    $table->integer('id_parent')->index()->default(0)->comment('comment:attribute');
    	    $table->integer('position')->index()->default(0)->comment('comment:attribute');
            $table->string('title', 80)->index()->unique()->comment('comment:attribute');
            $table->unsignedBigInteger('id_name')->unsigned()->index()->nullable()->comment('comment:attribute');
            $table->unsignedBigInteger('id_sublink')->unsigned()->index()->nullable()->comment('comment:attribute');
            $table->text('icon')->nullable()->comment('comment:attribute');
            $table->timestamps();

            $table->foreign('id_name')->references('id')->on('vk_categories')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_sublink')->references('id')->on('vk_subcategories')->onUpdate('cascade')->onDelete('cascade');
            
	});
            $statement = "ALTER TABLE vk_menus AUTO_INCREMENT = 1000;";
            DB::unprepared($statement);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vk_menus');
    }
};
