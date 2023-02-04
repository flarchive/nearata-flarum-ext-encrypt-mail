<?php

use Flarum\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

return Migration::createTable('nearata_encrypt_mail', function (Blueprint $table) {
    $table->unsignedInteger('id');
    $table->text('public_key')->nullable()->default(null);
    $table->boolean('imported')->default(false);
    $table->timestamp('created_at')->useCurrent();
    $table->timestamp('updated_at')->useCurrent();

    $table->primary('id');

    $table->foreign('id')->references('id')->on('users');
});
