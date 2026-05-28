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
        Schema::create('route_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId("route_id")->constrained()->onDelete("cascade");
            $table->integer("section_order");
            $table->string("section_origin_lat");
            $table->string("section_origin_lng");
            $table->string("section_destination_lat");
            $table->string("section_destination_lng");
            $table->string("instructions");
            $table->double("distance_km");
            $table->integer("duration_minutes");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_sections');
    }
};
