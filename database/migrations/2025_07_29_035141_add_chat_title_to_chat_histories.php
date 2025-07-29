<?php
// database/migrations/2025_07_29_000001_add_chat_title_to_chat_histories.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('chat_histories', function (Blueprint $table) {
            $table->string('chat_title')->nullable()->after('bot_response');
            $table->index(['user_id', 'session_id']);
        });
    }

    public function down()
    {
        Schema::table('chat_histories', function (Blueprint $table) {
            $table->dropColumn('chat_title');
        });
    }
};