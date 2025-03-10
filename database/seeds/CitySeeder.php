<?php

use App\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fileKota = file_get_contents(base_path('database/kota.json'));
        $fileKabupaten = file_get_contents(base_path('database/kabupaten.json'));

        $dataKota = json_decode($fileKota, true);
        $dataKabupaten = json_decode($fileKabupaten, true);

        City::insert($dataKota);
        City::insert($dataKabupaten);
    }
}
