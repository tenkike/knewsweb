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
       Schema::create('vk_categories', function (Blueprint $table) {
            
            $table->id();
            $table->string('categoryName', 80)->index()->unique()->comment('comment:attribute');
            $table->timestamps();
        });

        $statement = "ALTER TABLE vk_categories AUTO_INCREMENT = 10000;";
            DB::unprepared($statement);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vk_categories');
    }
};
