<?php

namespace Database\Seeders;

use App\Models\Alphabet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // Alphabet
        Alphabet::truncate();
        $sql = file_get_contents('alphabet-insert.sql');
        DB::unprepared($sql);
    }
}
