<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_gateway')->nullable();
            $table->string('gateway_reference')->nullable();
            $table->text('snap_token')->nullable();
            $table->integer('amount')->default(0);
            $table->string('status')->default('pending');
            $table->json('response_payload')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['transaction_id']);
            $table->index(['user_id']);
            $table->index(['gateway_reference']);
            $table->index(['status']);

            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Migrate existing payments data from transactions table into payments table, if any
        $now = DB::raw('CURRENT_TIMESTAMP');
        $transactions = DB::table('transactions')->get();
        foreach ($transactions as $t) {
            if ($t->payment_method || $t->payment_gateway_id || $t->paid_at) {
                DB::table('payments')->insert([
                    'transaction_id' => $t->id,
                    'user_id' => $t->user_id,
                    'payment_method' => $t->payment_method,
                    'payment_gateway' => $t->payment_method ?: null,
                    'gateway_reference' => $t->id,
                    'snap_token' => $t->payment_gateway_id,
                    'amount' => $t->total_amount,
                    'status' => $t->status === 'paid' ? 'success' : ($t->status ?: 'pending'),
                    'response_payload' => null,
                    'failure_reason' => null,
                    'paid_at' => $t->paid_at,
                    'created_at' => $t->created_at,
                    'updated_at' => $t->updated_at,
                ]);
            }
        }

        // Remove payment-specific columns from transactions table
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            if (Schema::hasColumn('transactions', 'payment_gateway_id')) {
                $table->dropColumn('payment_gateway_id');
            }
            if (Schema::hasColumn('transactions', 'paid_at')) {
                $table->dropColumn('paid_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate columns in transactions table (nullable) to avoid data loss on rollback.
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'payment_gateway_id')) {
                $table->string('payment_gateway_id')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'paid_at')) {
                $table->timestamp('paid_at')->nullable();
            }
        });

        Schema::dropIfExists('payments');
    }
};
