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
        Schema::create('recurring_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('description', 200)->nullable();
            $table->bigInteger('amount');
            $table->tinyInteger('recurring_transaction_type');
            $table->tinyInteger('transaction_type');
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('budget_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('payee_id')->nullable();
            $table->unsignedBigInteger('time_period_id');
            $table->tinyInteger('time_period_amount')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('budget_id')->references('id')->on('budgets');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('payee_id')->references('id')->on('payees');
            $table->foreign('time_period_id')->references('id')->on('time_periods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recurring_transactions');
    }
};
