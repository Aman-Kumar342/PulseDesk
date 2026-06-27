<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('tags', function (Blueprint $t) {
            $t->id();
            $t->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->timestamps();
        });
        Schema::create('ticket_tag', function (Blueprint $t) {
            $t->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $t->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $t->primary(['ticket_id','tag_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('ticket_tag'); Schema::dropIfExists('tags'); }
};
