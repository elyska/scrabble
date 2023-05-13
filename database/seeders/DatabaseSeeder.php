<?php

namespace Database\Seeders;

use App\Models\Alphabet;
use App\Models\User;
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
        // User
        User::truncate();
        User::factory()->create();
        User::factory()->create([
            "name" => "Silva",
            "email" => "asd2@asd.com",
        ]);

        // Alphabet
        Alphabet::truncate();
        $sql = file_get_contents('alphabet-insert.sql');
        DB::unprepared($sql);
    }
}
