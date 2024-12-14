<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create("products", function (Blueprint $table) {
            $table->id();
            $table->string("link")->unique();
            $table->string("name");
            $table->timestamps();
        });

        Schema::create("prices", function (Blueprint $table) {
            $table->id();
            $table->string("link");
            $table->decimal("price");
            $table->timestamps();

            $table->foreign("link")->references("link")->on("products")->onDelete("cascade");
        });

        Schema::create("subscriptions", function (Blueprint $table) {
            $table->id();
            $table->string("email");
            $table->string("link");

            $table->foreign("link")->references("link")->on("products")->onDelete("cascade");
        });

        Schema::create("sessions", function (Blueprint $table) {
            $table->string("id")->primary();
            $table
                ->foreignId("user_id")
                ->nullable()
                ->index();
            $table->string("ip_address", 45)->nullable();
            $table->text("user_agent")->nullable();
            $table->longText("payload");
            $table->integer("last_activity")->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("users");
        Schema::dropIfExists("products");
        Schema::dropIfExists("subscription");
        Schema::dropIfExists("prices");
    }

};
