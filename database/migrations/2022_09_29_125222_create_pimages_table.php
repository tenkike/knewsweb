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
        Schema::create('vk_images', function (Blueprint $table) {
          //  $table->autoIncrement(5000);
            $table->id();
            $table->unsignedBigInteger('id_body')->index()->unsigned()->nullable()->comment('comment:attribute');
            $table->unsignedBigInteger('id_portafolio')->index()->unsigned()->nullable()->comment('comment:attribute');
            $table->string('alt', 80)->index()->unique()->comment('comment:attribute');
            $table->text('description')->comment('comment:attribute');
            $table->text('src')->nullable()->comment('comment:attribute');
            $table->timestamps();

            $table->foreign('id_body')->references('id')->on('vk_bodys')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_portafolio')->references('id')->on('vk_portafolios')->onUpdate('cascade')->onDelete('cascade');

        });

        $statement = "ALTER TABLE vk_images AUTO_INCREMENT = 5000;";
            DB::unprepared($statement);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vk_images');
    }
};
