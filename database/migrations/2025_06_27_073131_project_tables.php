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
        // update existing user table
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique();
            $table->enum('role', ['admin', 'cashier', 'superadmin']);
            $table->boolean('active');
            $table->bigInteger('created_by_id')->default(0);
            $table->bigInteger('updated_by_id')->default(0);
            $table->bigInteger('deleted_by_id')->default(0);
            $table->dropColumn('name');
            $table->dropColumn('email');
            $table->dropColumn('email_verified_at');
            $table->softDeletes();
        });
        // tables
        Schema::create('tables', function (Blueprint $table) {
            // Set the storage engine to InnoDB
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name', 50);
            $table->tinyInteger('status')->default(2);
            $table->string('invoice_no', 20)->nullable();
            $table->tinyInteger('discount')->default(0);
            $table->decimal('total_discount')->default(0);
            $table->decimal('grand_total')->default(0);
            $table->decimal('total')->default(0);
            $table->decimal('net_amount')->default(0);
            $table->bigInteger('created_by_id')->default(0);
            $table->bigInteger('updated_by_id')->default(0);
            $table->bigInteger('deleted_by_id')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // product
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->foreignId('product_category_id');
            $table->decimal('unit_price');
            $table->string('image')->nullable();
            $table->bigInteger('created_by_id')->default(0);
            $table->bigInteger('updated_by_id')->default(0);
            $table->bigInteger('deleted_by_id')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // product category
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->integer('ordering')->default(0);
            $table->bigInteger('created_by_id')->default(0);
            $table->bigInteger('updated_by_id')->default(0);
            $table->bigInteger('deleted_by_id')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // order
        Schema::create('orders', function (Blueprint $table) {
            // Set the storage engine to InnoDB
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('table_id');
            $table->string('invoice_no', 20);
            $table->integer('discount')->default(0);
            $table->decimal('total_discount')->default(0);
            $table->decimal('grand_total')->default(0);
            $table->decimal('total')->default(0);
            $table->decimal('net_amount')->default(0);
            $table->decimal('receive_amount');
            $table->bigInteger('created_by_id')->default(0);
            $table->bigInteger('updated_by_id')->default(0);
            $table->bigInteger('deleted_by_id')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // order detail
        Schema::create('order_details', function (Blueprint $table) {
            // Set the storage engine to InnoDB
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('order_id');
            $table->foreignId('product_id');
            $table->foreignId('product_category_id');
            $table->string('description', 100);
            $table->integer('qty');
            $table->decimal('unit_price');
            $table->tinyInteger('discount')->default(0);
            $table->bigInteger('created_by_id')->default(0);
            $table->bigInteger('updated_by_id')->default(0);
            $table->bigInteger('deleted_by_id')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // order detail temp
        Schema::create('order_detail_temps', function (Blueprint $table) {
            // Set the storage engine to InnoDB
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('table_id');
            $table->foreignId('product_id');
            $table->foreignId('product_category_id');
            $table->string('description', 100);
            $table->integer('qty');
            $table->decimal('unit_price');
            $table->tinyInteger('discount')->default(0);
            $table->bigInteger('created_by_id')->default(0);
            $table->bigInteger('updated_by_id')->default(0);
            $table->timestamps();
        });

        // balance adjustment
        Schema::create('balance_adjustments', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount');
            $table->tinyInteger('type_id');
            $table->date('adjusted_date');
            $table->string('remark');
            $table->bigInteger('created_by_id')->default(0);
            $table->bigInteger('updated_by_id')->default(0);
            $table->bigInteger('deleted_by_id')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_details');
        Schema::dropIfExists('order_detail_temps');
        Schema::dropIfExists('balance_adjustments');
    }
};
