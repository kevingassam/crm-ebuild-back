<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            EbuildData::class,
        ]);

        DB::table('users')->insert([
            'name' => 'Achref Hamouda',
            'email' => 'Achrefhamouda1997@gmail.com',
            'password' => Hash::make('123456789'), 
            'role' => 'admin',
        ]);
    }

}
