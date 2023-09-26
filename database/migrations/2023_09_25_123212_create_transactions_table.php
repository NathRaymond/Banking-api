<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("user_id");
            $table->string("transaction_id");
            $table->string("currency");
            $table->string("amount");
            $table->string("charges");
            $table->string("reason");
            $table->string("reference_id");
            $table->text("details");
            $table->string("type");
            $table->enum("status", ["pending", "success", "failed", "reversed"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
