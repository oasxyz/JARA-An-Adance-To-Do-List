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
        // 1. Lists Table
        Schema::create('lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 2. ListMembers Table
        Schema::create('list_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('list_id')->constrained('lists')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            $table->unique(['list_id', 'user_id']);
        });

        // 3. Tasks Table
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('list_id')->constrained('lists')->onDelete('cascade');
            $table->string('title');
            $table->string('priority')->default('Medium'); // Low, Medium, High
            $table->dateTime('deadline')->nullable();
            $table->string('status')->default('not done'); // done, not done, canceled
            $table->foreignId('assignee_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 4. Invitations Table
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('list_id')->constrained('lists')->onDelete('cascade');
            $table->string('invited_email')->nullable();
            $table->foreignId('invited_by')->constrained('users')->onDelete('cascade');
            $table->string('token', 64)->unique();
            $table->string('status')->default('pending'); // pending, accepted, rejected
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('list_members');
        Schema::dropIfExists('lists');
    }
};
