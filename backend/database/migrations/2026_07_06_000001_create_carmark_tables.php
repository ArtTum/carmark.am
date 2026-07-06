<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->json('title');
            $table->string('lot')->nullable();
            $table->string('vin')->nullable();
            $table->unsignedSmallInteger('year');
            $table->string('make');
            $table->string('model');
            $table->string('body')->nullable();
            $table->string('engine')->nullable();
            $table->string('transmission')->nullable();
            $table->string('fuel')->nullable();
            $table->string('color')->nullable();
            $table->string('condition')->nullable();
            $table->string('location')->nullable();
            $table->string('auction')->nullable();
            $table->dateTime('sale_date')->nullable();
            $table->string('odometer')->nullable();
            $table->unsignedInteger('current_bid')->default(0);
            $table->unsignedInteger('buy_now')->nullable();
            $table->unsignedInteger('shipping_fee')->default(0);
            $table->boolean('featured')->default(false);
            $table->boolean('private_sale')->default(false);
            $table->string('status')->default('published');
            $table->timestamps();
        });

        Schema::create('vehicle_images', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('auctions', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('country')->default('USA');
            $table->dateTime('starts_at')->nullable();
            $table->unsignedInteger('lots_count')->default(0);
            $table->unsignedInteger('buyer_fee')->default(0);
            $table->string('image')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('leads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('contact');
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('new');
            $table->timestamps();
        });

        Schema::create('bids', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('amount');
            $table->string('name')->nullable();
            $table->string('email');
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->json('title');
            $table->json('body');
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->json('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('bids');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('auctions');
        Schema::dropIfExists('vehicle_images');
        Schema::dropIfExists('vehicles');
    }
};
