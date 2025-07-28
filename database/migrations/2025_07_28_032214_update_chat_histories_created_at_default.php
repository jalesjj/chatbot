<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('chat_histories', function (Blueprint $table) {
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->change();
        });
    }

    public function down()
    {
        Schema::table('chat_histories', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable(false)->change();
        });
    }
};