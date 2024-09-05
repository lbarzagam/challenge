<?php

namespace Database\Seeders;

use App\Models\User;
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
        //se crearan los usuario al correr las migraciones con el comando "php artisan migrate --seed"
        //se creo el password con encriptacion "bcrypt"
        $user = new User();
        $user->name = 'Lorenzo Barzaga';
        $user->email = 'lbarzagam11@gmail.com';
        $user->password = bcrypt('123456789');
        $user->save();

        $user = new User();
        $user->name = 'Kevin Barzaga';
        $user->email = 'kbarzagam11@gmail.com';
        $user->password = bcrypt('123456789');
        $user->save();
    }
}
