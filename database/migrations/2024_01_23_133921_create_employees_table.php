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
        Schema::connection('mongodb')->create('employees', function ($collection) {
            $collection->unique('idNumber');
            $collection->unique('email');
            $collection->unique('contactNumber');
            $collection->unique('username');
            $collection->index('role');
            $collection->index('designation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
