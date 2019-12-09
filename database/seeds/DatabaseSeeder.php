<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        for($i = 0; $i < 50; $i++){
            if($i == 0){
                DB::table('users')->insert([
                    'nama'          => 'Pandu Dwi Putra Nugroho',
                    'no_telp'       => '087777336109',
                    'email'         => 'pandudpn@pandudpn.com',
                    'username'      => 'pandudpn',
                    'password'      => bcrypt(sha1('oranggila')),
                    'uang'          => '10000000',
                    'status'        => 2,
                    'created_at'    => now(),
                    'updated_at'    => now()
                ]);
            }else{
                DB::table('users')->insert([
                    'nama'          => $faker->name,
                    'no_telp'       => $faker->phoneNumber,
                    'email'         => $faker->unique()->freeEmail,
                    'username'      => $faker->unique()->userName,
                    'password'      => $faker->password,
                    'uang'          => 0,
                    'status'        => $faker->numberBetween($min = 1, $max = 3),
                    'created_at'    => now(),
                    'updated_at'    => now()
                ]);
            }
        } // end loop
        
    }
}
