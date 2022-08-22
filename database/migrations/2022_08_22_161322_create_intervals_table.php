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
        Schema::create('intervals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('opening_balance')->default(0);
            $table->bigInteger('closing_balance')->default(0);
            $table->bigInteger('income')->default(0);
            $table->bigInteger('expenditure')->default(0);
            $table->bigInteger('transactions')->default(0);
            $table->boolean('final')->default(false);
            $table->unsignedBigInteger('budget_id');
            $table->unsignedBigInteger('time_period_id');
            $table->tinyInteger('time_period_amount')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('budget_id')->references('id')->on('budgets');
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
        Schema::dropIfExists('intervals');
    }
};
