<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('description', 200)->nullable();
            $table->bigInteger('budget')->default(0);
            $table->bigInteger('actual')->default(0);
            $table->tinyInteger('type');
            $table->boolean('final')->default(false);
            $table->unsignedBigInteger('interval_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('payee_id')->nullable();
            $table->unsignedBigInteger('recurring_transaction_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('interval_id')->references('id')->on('intervals');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('payee_id')->references('id')->on('payees');
            $table->foreign('recurring_transaction_id')->references('id')->on('recurring_transactions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_transactions');
    }
};
