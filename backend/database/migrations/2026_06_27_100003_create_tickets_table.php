<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('tickets', function (Blueprint $t) {
            $t->id();
            $t->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $t->string('subject');
            $t->text('description');
            $t->enum('status', ['open','pending','resolved','closed'])->default('open');
            $t->enum('priority', ['low','medium','high','urgent'])->default('medium');
            $t->foreignId('requester_id')->constrained('users');
            $t->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->index(['organization_id','status']);
            $t->index(['organization_id','priority']);
        });
    }
    public function down(): void { Schema::dropIfExists('tickets'); }
};
