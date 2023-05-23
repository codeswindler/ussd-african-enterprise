<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->integer('zoneId');
            $table->string('zoneName');
            $table->timestamps();
       
        });

        $zonesData = [
            'Embakasi East',
            'Embakasi West',
            'Embakasi Central',
            'Makadara',
            'Kamukunji',
            'Dagoretti South',
            'Langata',
            'Youth Alliance',
            'Ruaraka',
            'Roysambu',
            'Embakasi North',
            'Embakasi South',
            'Northern Gate',
            'Women Alliance',
            'Dagoretti North',
            'Starehe',
            'Kahawa West',
            'Eastern Gate',
            'Kasarani',
            'Mathare',
            'Kibra',
            'Westlands',
            'Western Gate'
        ];

        $zoneId =[1,2,3,4,5,6,7,8,10,11,12,13,14,15,16,17,19,20,21,22,23,24,25

        ];

        foreach ($zonesData as $index => $zoneName) {
            DB::table('zones')->insert([
                'zoneId' => $zoneId[$index],
                'zoneName' => $zoneName
            ]);
    }

}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
