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
    Schema::create('apartments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');

        $table->string('apartment_name');
        $table->string('governorate');
        $table->string('city');
        $table->text('detailed_address');
        $table->decimal('price', 10, 2);
        $table->enum('rent_type', ['daily', 'monthly'])->default('daily');

        $table->text('description')->nullable(); // يمكن استخدامه لاحقاً
        $table->json('specifications')->nullable();
        $table->json('images')->nullable();
        $table->enum('status', ['available', 'unavailable'])->default('available');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
