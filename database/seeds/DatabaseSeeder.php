<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');
        $path = 'database/data/seed.sql';
        DB::unprepared(file_get_contents($path));
        $this->command->info('All tables created and seeded');
    }
}
