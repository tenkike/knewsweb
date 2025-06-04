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
        Schema::create('vk_footers', function (Blueprint $table) {
          //  $table->autoIncrement(4000);
        $table->id();
        $table->unsignedBigInteger('id_footer')->index()->unique()->comment('comment:attribute');
        $table->string('title', 80)->index()->comment('comment:attribute');
        $table->text('description')->comment('comment:attribute');
        $table->timestamps();

        $table->foreign('id_footer')->references('id')->on('vk_menus')->onUpdate('cascade')->onDelete('cascade');
        });

        $statement = "ALTER TABLE vk_footers AUTO_INCREMENT = 4000;";
            DB::unprepared($statement);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vk_footers');
    }
};
