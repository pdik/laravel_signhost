<?php

use Illuminate\Database\Seeder;

class CreatePermissions
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (class_exists('\Spatie\Permission\Models\Permission')) {
            \Spatie\Permission\Models\Permission::create(['name' => 'user.account']);
        }
    }
}