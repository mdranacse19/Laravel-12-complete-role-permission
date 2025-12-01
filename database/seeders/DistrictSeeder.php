<?php

namespace Database\Seeders;

use App\Models\Location\District;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districts = [
            ['id' => '1', 'division_id' => '4', 'name' => 'Bagerhat', 'bn_name' => 'বাগেরহাট', 'lon' => '89.7395643', 'lat' => '22.36978', 'coordinate' => '22.3697799679524, 89.7395643166382', 'code' => 'BD4001'],
            ['id' => '2', 'division_id' => '2', 'name' => 'Bandarban', 'bn_name' => 'বান্দরবান', 'lon' => '92.3638254', 'lat' => '21.807117', 'coordinate' => '21.8071170166179, 92.3638254311408', 'code' => 'BD2003'],
            ['id' => '3', 'division_id' => '1', 'name' => 'Barguna', 'bn_name' => 'বরগুনা', 'lon' => '90.1204804', 'lat' => '22.1411266', 'coordinate' => '22.1411265824604, 90.1204803599127', 'code' => 'BD1004'],
            ['id' => '4', 'division_id' => '1', 'name' => 'Barisal', 'bn_name' => 'বরিশাল', 'lon' => '90.3416723', 'lat' => '22.8114414', 'coordinate' => '22.8114414379803, 90.341672338065', 'code' => 'BD1006'],
            ['id' => '5', 'division_id' => '1', 'name' => 'Bhola', 'bn_name' => 'ভোলা', 'lon' => '90.7333408', 'lat' => '22.3356973', 'coordinate' => '22.3356973214482, 90.7333408288266', 'code' => 'BD1009'],
            ['id' => '6', 'division_id' => '6', 'name' => 'Bogra', 'bn_name' => 'বগুড়া', 'lon' => '89.3787918', 'lat' => '24.8244581', 'coordinate' => '24.824458085036, 89.3787917814528', 'code' => 'BD5010'],
            ['id' => '7', 'division_id' => '2', 'name' => 'Brahamanbaria', 'bn_name' => 'ব্রাহ্মণবাড়িয়া', 'lon' => '91.0834843', 'lat' => '23.9529957', 'coordinate' => '23.9529956682736, 91.08348432889', 'code' => 'BD2012'],
            ['id' => '8', 'division_id' => '2', 'name' => 'Chandpur', 'bn_name' => 'চাঁদপুর', 'lon' => '90.7710138', 'lat' => '23.2735644', 'coordinate' => '23.2735644416365, 90.7710138363366', 'code' => 'BD2013'],
            ['id' => '9', 'division_id' => '2', 'name' => 'Chittagong', 'bn_name' => 'চট্টগ্রাম', 'lon' => '91.8343689', 'lat' => '22.4432645', 'coordinate' => '22.4432644673034, 91.8343689213404', 'code' => 'BD2015'],
            ['id' => '10', 'division_id' => '4', 'name' => 'Chuadanga', 'bn_name' => 'চুয়াডাঙ্গা', 'lon' => '88.847975', 'lat' => '23.6083123', 'coordinate' => '23.6083122800858, 88.8479749522613', 'code' => 'BD4018'],
            ['id' => '11', 'division_id' => '2', 'name' => 'Comilla', 'bn_name' => 'কুমিল্লা', 'lon' => '91.0339551', 'lat' => '23.4368731', 'coordinate' => '23.4368731498961, 91.0339550874305', 'code' => 'BD2019'],
            ['id' => '12', 'division_id' => '2', 'name' => 'Coxs Bazar', 'bn_name' => 'কক্সবাজার', 'lon' => '92.0639254', 'lat' => '21.4837979', 'coordinate' => '21.4837979288818, 92.063925400496', 'code' => 'BD2022'],
            ['id' => '13', 'division_id' => '3', 'name' => 'Dhaka', 'bn_name' => 'ঢাকা', 'lon' => '90.2507698', 'lat' => '23.7869676', 'coordinate' => '23.7869676288546, 90.2507698426167', 'code' => 'BD3026'],
            ['id' => '14', 'division_id' => '7', 'name' => 'Dinajpur', 'bn_name' => 'দিনাজপুর', 'lon' => '88.7868874', 'lat' => '25.629879', 'coordinate' => '25.6298790351655, 88.7868874043043', 'code' => 'BD5527'],
            ['id' => '15', 'division_id' => '3', 'name' => 'Faridpur', 'bn_name' => 'ফরিদপুর', 'lon' => '89.8388794', 'lat' => '23.4789419', 'coordinate' => '23.4789418648107, 89.8388793808416', 'code' => 'BD3029'],
            ['id' => '16', 'division_id' => '2', 'name' => 'Feni', 'bn_name' => 'ফেনী', 'lon' => '91.4124305', 'lat' => '22.9994316', 'coordinate' => '22.9994316236504, 91.4124305373666', 'code' => 'BD2030'],
            ['id' => '17', 'division_id' => '7', 'name' => 'Gaibandha', 'bn_name' => 'গাইবান্ধা', 'lon' => '89.505845', 'lat' => '25.2987222', 'coordinate' => '25.2987221731065, 89.5058450243876', 'code' => 'BD5532'],
            ['id' => '18', 'division_id' => '3', 'name' => 'Gazipur', 'bn_name' => 'গাজীপুর', 'lon' => '90.4449979', 'lat' => '24.0984243', 'coordinate' => '24.0984242858717, 90.4449979279923', 'code' => 'BD3033'],
            ['id' => '19', 'division_id' => '3', 'name' => 'Gopalganj', 'bn_name' => 'গোপালগঞ্জ', 'lon' => '89.8995116', 'lat' => '23.1055328', 'coordinate' => '23.1055327967815, 89.8995116232292', 'code' => 'BD3035'],
            ['id' => '20', 'division_id' => '8', 'name' => 'Habiganj', 'bn_name' => 'হবিগঞ্জ', 'lon' => '91.4303365', 'lat' => '24.3680822', 'coordinate' => '24.368082214365, 91.4303364977791', 'code' => 'BD6036'],
            ['id' => '21', 'division_id' => '5', 'name' => 'Jamalpur', 'bn_name' => 'জামালপুর', 'lon' => '89.8462657', 'lat' => '24.9731078', 'coordinate' => '24.9731077519347, 89.846265658857', 'code' => 'BD4539'],
            ['id' => '22', 'division_id' => '4', 'name' => 'Jessore', 'bn_name' => 'যশোর', 'lon' => '89.1746757', 'lat' => '23.0897761', 'coordinate' => '23.0897761328977, 89.1746756672581', 'code' => 'BD4041'],
            ['id' => '23', 'division_id' => '1', 'name' => 'Jhalokati', 'bn_name' => 'ঝালকাঠি', 'lon' => '90.1840126', 'lat' => '22.5727866', 'coordinate' => '22.5727865856114, 90.1840125886511', 'code' => 'BD1042'],
            ['id' => '24', 'division_id' => '4', 'name' => 'Jhenaidah', 'bn_name' => 'ঝিনাইদহ', 'lon' => '89.0874884', 'lat' => '23.4892163', 'coordinate' => '23.4892163352194, 89.0874883931931', 'code' => 'BD4044'],
            ['id' => '25', 'division_id' => '6', 'name' => 'Joypurhat', 'bn_name' => 'জয়পুরহাট', 'lon' => '89.0829155', 'lat' => '25.0927687', 'coordinate' => '25.0927687106127, 89.0829155125828', 'code' => 'BD5038'],
            ['id' => '26', 'division_id' => '2', 'name' => 'Khagrachhari', 'bn_name' => 'খাগড়াছড়ি', 'lon' => '91.9521438', 'lat' => '23.1643341', 'coordinate' => '23.1643341110586, 91.9521437706983', 'code' => 'BD2046'],
            ['id' => '27', 'division_id' => '4', 'name' => 'Khulna', 'bn_name' => 'খুলনা', 'lon' => '89.4478478', 'lat' => '22.4708782', 'coordinate' => '22.470878221683, 89.4478477572375', 'code' => 'BD4047'],
            ['id' => '28', 'division_id' => '3', 'name' => 'Kishoreganj', 'bn_name' => 'কিশোরগঞ্জ', 'lon' => '90.9424051', 'lat' => '24.3761861', 'coordinate' => '24.376186087127, 90.9424051199103', 'code' => 'BD3048'],
            ['id' => '29', 'division_id' => '7', 'name' => 'Kurigram', 'bn_name' => 'কুড়িগ্রাম', 'lon' => '89.6947033', 'lat' => '25.7920927', 'coordinate' => '25.7920926796336, 89.6947033065137', 'code' => 'BD5549'],
            ['id' => '30', 'division_id' => '4', 'name' => 'Kushtia', 'bn_name' => 'কুষ্টিয়া', 'lon' => '89.0219315', 'lat' => '23.9229668', 'coordinate' => '23.9229668489309, 89.0219315432875', 'code' => 'BD4050'],
            ['id' => '31', 'division_id' => '2', 'name' => 'Lakshmipur', 'bn_name' => 'লক্ষ্মীপুর', 'lon' => '90.8558288', 'lat' => '22.8917688', 'coordinate' => '22.8917688385899, 90.8558288310122', 'code' => 'BD2051'],
            ['id' => '32', 'division_id' => '7', 'name' => 'Lalmonirhat', 'bn_name' => 'লালমনিরহাট', 'lon' => '89.2357304', 'lat' => '26.0631123', 'coordinate' => '26.0631123278329, 89.2357303948538', 'code' => 'BD5552'],
            ['id' => '33', 'division_id' => '3', 'name' => 'Madaripur', 'bn_name' => 'মাদারীপুর', 'lon' => '90.165097', 'lat' => '23.2180422', 'coordinate' => '23.2180421594776, 90.165097002001', 'code' => 'BD3054'],
            ['id' => '34', 'division_id' => '4', 'name' => 'Magura', 'bn_name' => 'মাগুরা', 'lon' => '89.4337124', 'lat' => '23.4431795', 'coordinate' => '23.4431795334816, 89.4337123916009', 'code' => 'BD4055'],
            ['id' => '35', 'division_id' => '3', 'name' => 'Manikganj', 'bn_name' => 'মানিকগঞ্জ', 'lon' => '89.9495957', 'lat' => '23.8439257', 'coordinate' => '23.8439257334515, 89.9495957481121', 'code' => 'BD3056'],
            ['id' => '36', 'division_id' => '8', 'name' => 'Maulvibazar', 'bn_name' => 'মৌলভীবাজার', 'lon' => '91.9153756', 'lat' => '24.4804211', 'coordinate' => '24.480421110322, 91.9153755681843', 'code' => 'BD6058'],
            ['id' => '37', 'division_id' => '4', 'name' => 'Meherpur', 'bn_name' => 'মেহেরপুর', 'lon' => '88.7071425', 'lat' => '23.7941016', 'coordinate' => '23.7941015572166, 88.7071424934929', 'code' => 'BD4057'],
            ['id' => '38', 'division_id' => '3', 'name' => 'Munshiganj', 'bn_name' => 'মুন্সীগঞ্জ', 'lon' => '90.4172852', 'lat' => '23.5257482', 'coordinate' => '23.525748249241, 90.4172851942029', 'code' => 'BD3059'],
            ['id' => '39', 'division_id' => '5', 'name' => 'Mymensingh', 'bn_name' => 'ময়মনসিংহ', 'lon' => '90.4286088', 'lat' => '24.6990942', 'coordinate' => '24.6990942142479, 90.4286088471072', 'code' => 'BD4561'],
            ['id' => '40', 'division_id' => '6', 'name' => 'Naogaon', 'bn_name' => 'নওগাঁ', 'lon' => '88.7515717', 'lat' => '24.8996423', 'coordinate' => '24.8996423095563, 88.7515716848143', 'code' => 'BD5064'],
            ['id' => '41', 'division_id' => '4', 'name' => 'Narail', 'bn_name' => 'নড়াইল', 'lon' => '89.5801759', 'lat' => '23.1300995', 'coordinate' => '23.1300995240261, 89.5801758800111', 'code' => 'BD4065'],
            ['id' => '42', 'division_id' => '3', 'name' => 'Narayanganj', 'bn_name' => 'নারায়ণগঞ্জ', 'lon' => '90.5780073', 'lat' => '23.7232176', 'coordinate' => '23.7232176456378, 90.5780072888578', 'code' => 'BD3067'],
            ['id' => '43', 'division_id' => '3', 'name' => 'Narsingdi', 'bn_name' => 'নরসিংদী', 'lon' => '90.7732316', 'lat' => '24.0022908', 'coordinate' => '24.0022907705157, 90.773231617816', 'code' => 'BD3068'],
            ['id' => '44', 'division_id' => '6', 'name' => 'Natore', 'bn_name' => 'নাটোর', 'lon' => '89.0868586', 'lat' => '24.3803673', 'coordinate' => '24.3803672875284, 89.0868585953886', 'code' => 'BD5069'],
            ['id' => '45', 'division_id' => '6', 'name' => 'Chapainawabganj', 'bn_name' => 'চাঁপাইনবাবগঞ্জ', 'lon' => '88.2641126', 'lat' => '24.7156988', 'coordinate' => '24.7156987855576, 88.2641126162697', 'code' => 'BD5070'],
            ['id' => '46', 'division_id' => '5', 'name' => 'Netrakona', 'bn_name' => 'নেত্রাকোনা', 'lon' => '90.8446298', 'lat' => '24.8707605', 'coordinate' => '24.8707604564733, 90.8446297920717', 'code' => 'BD4572'],
            ['id' => '47', 'division_id' => '7', 'name' => 'Nilphamari', 'bn_name' => 'নীলফামারী', 'lon' => '88.9295231', 'lat' => '26.0246551', 'coordinate' => '26.0246551122935, 88.9295231003787', 'code' => 'BD5573'],
            ['id' => '48', 'division_id' => '2', 'name' => 'Noakhali', 'bn_name' => 'নোয়াখালী', 'lon' => '91.1246575', 'lat' => '22.6936969', 'coordinate' => '22.6936968732011, 91.1246574598643', 'code' => 'BD2075'],
            ['id' => '49', 'division_id' => '6', 'name' => 'Pabna', 'bn_name' => 'পাবনা', 'lon' => '89.3853323', 'lat' => '24.0531716', 'coordinate' => '24.0531715515006, 89.3853322640532', 'code' => 'BD5076'],
            ['id' => '51', 'division_id' => '1', 'name' => 'Patuakhali', 'bn_name' => 'পটুয়াখালী', 'lon' => '90.3989979', 'lat' => '22.1908556', 'coordinate' => '22.1908556164555, 90.3989978942543', 'code' => 'BD1078'],
            ['id' => '50', 'division_id' => '7', 'name' => 'Panchagarh', 'bn_name' => 'পঞ্চগড়', 'lon' => '88.575459', 'lat' => '26.2851291', 'coordinate' => '26.285129105221, 88.5754590338474', 'code' => 'BD5577'],
            ['id' => '52', 'division_id' => '1', 'name' => 'Pirojpur', 'bn_name' => 'পিরোজপুর', 'lon' => '89.99318', 'lat' => '22.5337928', 'coordinate' => '22.5337928039079, 89.9931800472942', 'code' => 'BD1079'],
            ['id' => '53', 'division_id' => '3', 'name' => 'Rajbari', 'bn_name' => 'রাজবাড়ী', 'lon' => '89.5615267', 'lat' => '23.7276551', 'coordinate' => '23.7276551254323, 89.5615266675926', 'code' => 'BD3082'],
            ['id' => '54', 'division_id' => '6', 'name' => 'Rajshahi', 'bn_name' => 'রাজশাহী', 'lon' => '88.6514399', 'lat' => '24.4657403', 'coordinate' => '24.4657402559634, 88.651439884661', 'code' => 'BD5081'],
            ['id' => '55', 'division_id' => '2', 'name' => 'Rangamati', 'bn_name' => 'রাঙ্গামাটি', 'lon' => '92.2795572', 'lat' => '22.8299787', 'coordinate' => '22.8299786557806, 92.2795571783611', 'code' => 'BD2084'],
            ['id' => '56', 'division_id' => '7', 'name' => 'Rangpur', 'bn_name' => 'রংপুর', 'lon' => '89.2368135', 'lat' => '25.6497584', 'coordinate' => '25.6497583755847, 89.2368134529719', 'code' => 'BD5585'],
            ['id' => '57', 'division_id' => '4', 'name' => 'Satkhira', 'bn_name' => 'সাতক্ষীরা', 'lon' => '89.1366913', 'lat' => '22.3822414', 'coordinate' => '22.3822413763056, 89.1366912928941', 'code' => 'BD4087'],
            ['id' => '58', 'division_id' => '3', 'name' => 'Shariatpur', 'bn_name' => 'শরীয়তপুর', 'lon' => '90.4083773', 'lat' => '23.2473651', 'coordinate' => '23.2473650884159, 90.4083773428958', 'code' => 'BD3086'],
            ['id' => '59', 'division_id' => '5', 'name' => 'Sherpur', 'bn_name' => 'শেরপুর', 'lon' => '90.0744779', 'lat' => '25.0822667', 'coordinate' => '25.0822666856324, 90.0744778760509', 'code' => 'BD4589'],
            ['id' => '60', 'division_id' => '6', 'name' => 'Sirajganj', 'bn_name' => 'সিরাজগঞ্জ', 'lon' => '89.5996804', 'lat' => '24.3916297', 'coordinate' => '24.3916297032871, 89.599680442215', 'code' => 'BD5088'],
            ['id' => '61', 'division_id' => '8', 'name' => 'Sunamganj', 'bn_name' => 'সুনামগঞ্জ', 'lon' => '91.3460454', 'lat' => '24.9385674', 'coordinate' => '24.9385673984308, 91.3460454320714', 'code' => 'BD6090'],
            ['id' => '62', 'division_id' => '8', 'name' => 'Sylhet', 'bn_name' => 'সিলেট', 'lon' => '91.9873344', 'lat' => '24.9192183', 'coordinate' => '24.919218341539, 91.9873343730909', 'code' => 'BD6091'],
            ['id' => '63', 'division_id' => '3', 'name' => 'Tangail', 'bn_name' => 'টাঙ্গাইল', 'lon' => '89.9986055', 'lat' => '24.3563036', 'coordinate' => '24.3563035524522, 89.9986055018869', 'code' => 'BD3093'],
            ['id' => '64', 'division_id' => '7', 'name' => 'Thakurgaon', 'bn_name' => 'ঠাকুরগাঁও', 'lon' => '88.3455689', 'lat' => '25.9905601', 'coordinate' => '25.9905601177224, 88.345568901469', 'code' => 'BD5594'],
        ];

        District::insert($districts);
    }
}
