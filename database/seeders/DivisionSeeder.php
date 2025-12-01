<?php

namespace Database\Seeders;

use App\Models\Location\Division;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            ['id' => '1', 'name' => 'Barisal', 'bn_name' => 'বরিশাল', 'lon' => '90.3471939', 'lat' => '22.4191472', 'coordinate' => '22.41914717, 90.3471939', 'code' => 'BD10'],
            ['id' => '2', 'name' => 'Chittagong', 'bn_name' => 'চট্টগ্রাম', 'lon' => '91.7317686', 'lat' => '22.7096577', 'coordinate' => '22.70965765, 91.73176862', 'code' => 'BD20'],
            ['id' => '3', 'name' => 'Dhaka', 'bn_name' => 'ঢাকা', 'lon' => '90.2416568', 'lat' => '23.839712', 'coordinate' => '23.83971202, 90.24165684', 'code' => 'BD30'],
            ['id' => '4', 'name' => 'Khulna', 'bn_name' => 'খুলনা', 'lon' => '89.29252', 'lat' => '22.9177548', 'coordinate' => '22.91775476, 89.29252', 'code' => 'BD40'],
            ['id' => '5', 'name' => 'Mymensingh', 'bn_name' => 'ময়মনসিংহ', 'lon' => '90.3804598', 'lat' => '24.8467667', 'coordinate' => '24.84676672, 90.3804598', 'code' => 'BD45'],
            ['id' => '6', 'name' => 'Rajshahi', 'bn_name' => 'রাজশাহী', 'lon' => '89.04457', 'lat' => '24.588749', 'coordinate' => '24.58874896, 89.04457004', 'code' => 'BD50'],
            ['id' => '7', 'name' => 'Rangpur', 'bn_name' => 'রংপুর', 'lon' => '89.0558362', 'lat' => '25.7794052', 'coordinate' => '25.77940519, 89.05583622', 'code' => 'BD55'],
            ['id' => '8', 'name' => 'Sylhet', 'bn_name' => 'সিলেট', 'lon' => '91.6640655', 'lat' => '24.7154555', 'coordinate' => '24.71545553, 91.66406549', 'code' => 'BD60'],
        ];

        Division::insert($divisions);
    }
}
