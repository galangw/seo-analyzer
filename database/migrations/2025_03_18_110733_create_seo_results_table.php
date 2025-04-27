<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeoResultsTable extends Migration
{
    public function up()
    {
        Schema::create('seo_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained()->onDelete('cascade');
            $table->decimal('page_title_score', 5, 2);
            $table->decimal('meta_description_score', 5, 2);
            $table->decimal('content_score', 5, 2);
            $table->decimal('overall_score', 5, 2);
            $table->json('detail_score');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('seo_results');
    }
}
