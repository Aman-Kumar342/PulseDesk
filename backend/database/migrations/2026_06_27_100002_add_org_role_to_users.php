<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->foreignId('organization_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $t->enum('role', ['admin','agent','customer'])->default('customer')->after('email');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->dropConstrainedForeignId('organization_id');
            $t->dropColumn('role');
        });
    }
};
