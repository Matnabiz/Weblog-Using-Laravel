<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('likes', function (Blueprint $table) {
            //$table->dropUnique(['post_id']);
            $table->dropForeign(['post_id']); 
            $table->dropColumn('post_id');
            $table->morphs('likeable');
        });
    }
    
    public function down()
    {
        Schema::table('likes', function (Blueprint $table) {
            $table->dropMorphs('likeable');
            $table->unsignedBigInteger('post_id')->nullable();
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');

        });
    }
};
