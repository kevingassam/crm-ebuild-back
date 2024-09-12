<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EbuildData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ebuilddata')->insert(
            [
                'name' => "Ebuild",
                'mail' => "ebuild@gmail.com",
                'phone_number' => "+212 555 555 555",
                'address' => "123 Main St, City, Country",
                'matriculef' => "EF-123456789",
                'idunique' => "https://www.e-build.tn",
                'created_at' => now(),
            ]
        );
    }
}
