<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BrokerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // \DB::table('brokers')->truncate();
        $brokers = [
            ['broker_no'=>'3','name'=>'Arun Securities Pvt. Limited','address'=>'Dillibazar, Kathmandu','phone'=>'01-4239567',],
            ['broker_no'=>'4','name'=>'Stock Broker Opal Securities Investment Pvt. Limited','address'=>'Putalisadak, Kathmandu','phone'=>'01-4420313',],
            ['broker_no'=>'5','name'=>'Market Securities Exchange Company Pvt. Limited','address'=>'Uttardhoka, Kathmandu','phone'=>'01-4248973',],
            ['broker_no'=>'6','name'=>'Agrawal Securities Pvt. Limited','address'=>'Kichha Pokhari, Kathmandu','phone'=>'01-4424406',],
            ['broker_no'=>'7','name'=>'J.F. Securities Company Pvt. Limited','address'=>'Dillibazar, Kathmandu','phone'=>'01-4256099',],
            ['broker_no'=>'8','name'=>'Ashutosh Brokerage & Securities Pvt. Limited','address'=>'Dharma path, Kathmandu','phone'=>'01-4490232',],
            ['broker_no'=>'10','name'=>'Pragyan Securities Pvt. Limited','address'=>'Behind Nepal SBI Bank, Battisputali, Kathmandu','phone'=>'01-4413392',],
            ['broker_no'=>'11','name'=>'Malla & Malla Stock Broking Company Pvt. Limited','address'=>'Kamaladi, Kathmandu','phone'=>'01-4432008',],
            ['broker_no'=>'13','name'=>'Thrive Brokerage House Pvt. Limited','address'=>'Lalupate Marg, Hattisar, Kathmandu, Nepal','phone'=>'01-4419051',],
            ['broker_no'=>'14','name'=>'Nepal Stock House Pvt. Limited','address'=>'Naxal, Kathmandu','phone'=>'01-4429621',],
            ['broker_no'=>'16','name'=>'Primo Securities Pvt. Limited','address'=>'Kalikasthan, Kathmandu','phone'=>'01-4168175',],
            ['broker_no'=>'17','name'=>'ABC Securities Pvt. Limited','address'=>'49/8, Shanker Dev Marg, Putalisadak, Kathmandu.','phone'=>'01-4230787',],
            ['broker_no'=>'18','name'=>'Sagarmatha Securities Pvt. Limited','address'=>'Indrachowk, Kathmandu','phone'=>'01-4439315',],
            ['broker_no'=>'19','name'=>'Nepal Investment & Securities Trading Pvt. Limited','address'=>'Dillibazar, Kathmandu','phone'=>'01- 4495450',],
            ['broker_no'=>'20','name'=>'Sipla Securities Pvt. Limited','address'=>'Purano Baneshwor Kathmandu, Nepa','phone'=>'01-4255782',],
            ['broker_no'=>'21','name'=>'Midas Stock Broking Company Pvt. Limited','address'=>'NewRoad, Kathmandu','phone'=>'01-4240089',],
            ['broker_no'=>'22','name'=>'Siprabi Securities Pvt. Limited','address'=>'Putalisadak, Kathmandu','phone'=>'01-5530701',],
            ['broker_no'=>'25','name'=>'Sweta Securities Pvt. Limited','address'=>'Pulchowk, Lalitpur','phone'=>'01-4223914',],
            ['broker_no'=>'26','name'=>'Asian Securities Pvt. Limited','address'=>'Putalisadak, Kathmandu','phone'=>'01-4424351',],
            ['broker_no'=>'28','name'=>'Shree Krishna Securities Limited','address'=>'Putalisadak-32, Kathmandu, Near NMB Bank Ltd','phone'=>'01-4441226',],
            ['broker_no'=>'29','name'=>'Trishul Securities And Investment Limited','address'=>'Dillibazar, Kathmandu','phone'=>'01-4440709',],
            ['broker_no'=>'32','name'=>'Premier Securites Company Limited','address'=>'Putalisadak, Kathmandu','phone'=>'01-4432705',],
            ['broker_no'=>'33','name'=>'Dakshinkali Investment Securities Pvt.Limited','address'=>'Putalisadak,Kathmandu','phone'=>'01-4168640',],
            ['broker_no'=>'34','name'=>'Vision Securities Pvt.Limited','address'=>'Apex Building, Kamaladi, Kathmandu','phone'=>'01-4770425/452',],
            ['broker_no'=>'35','name'=>'Kohinoor Investment and Securities Pvt.Ltd','address'=>'Marpha House, Anamnagar, Kathmandu','phone'=>'01-4442857',],
            ['broker_no'=>'36','name'=>'Secured Securities Limited','address'=>'Hattisar Sadak, Kathmandu','phone'=>'01-4262861',],
            ['broker_no'=>'37','name'=>'Swarnalaxmi Securities Pvt.Limited','address'=>'Opp to Padmadaya School Pradarshani Marga, Kathmandu-28','phone'=>'01-4168219',],
            ['broker_no'=>'38','name'=>'Dipshika Dhitopatra Karobar Co. Pvt.Limited','address'=>'Putalisadak, Kathmandu','phone'=>'01-4102532',],
            ['broker_no'=>'39','name'=>'Sumeru Securities Pvt.Limited','address'=>'Anamnagar, Kathmandu','phone'=>'01-4444740',],
            ['broker_no'=>'40','name'=>'Creative Securities Pvt.Limited','address'=>'Hattisar, Kathmandu','phone'=>'01-4419572',],
            ['broker_no'=>'41','name'=>'Linch Stock Market Limited','address'=>'Kamalpokhari-28, Kathmandu','phone'=>'01-4469367',],
            ['broker_no'=>'42','name'=>'Sani Securities Company Limited','address'=>'New Baneshwor, Kathmandu','phone'=>'01-4166005',],
            ['broker_no'=>'43','name'=>'South Asian Bulls Pvt.Limited','address'=>'Jamal, Kathmandu','phone'=>'01-4284785',],
            ['broker_no'=>'44','name'=>'Dynamic Money Managers Securities Pvt.Ltd','address'=>'Tulsi Krishna Plaza 2nd Floor, Kuleshwor-14, Kathmandu','phone'=>'01-4414522',],
            ['broker_no'=>'45','name'=>'Imperial Securities Co .Pvt.Limited','address'=>'Kamalpokhari, Kathmandu','phone'=>'01-5706004',],
            ['broker_no'=>'46','name'=>'Kalika Securities Pvt.Limited','address'=>'Anamnagar, Kathmandu','phone'=>'977-01-5705563',],
            ['broker_no'=>'47','name'=>'Neev Securities Pvt.Ltd','address'=>'Hunamanthan, Anamnagar, Kathmandu, Nepal','phone'=>'01-4168601',],
            ['broker_no'=>'48','name'=>'Trishakti Securities Public Limited','address'=>'Putalisadak, Kathmandu','phone'=>'01-4232132',],
            ['broker_no'=>'49','name'=>'Online Securities Pvt.Ltd','address'=>'Putalisadak, Kathmandu','phone'=>'01-4168298',],
            ['broker_no'=>'50','name'=>'Crystal Kanchenjunga Securities Pvt.Ltd','address'=>'Putalisadak, Kathmandu','phone'=>'01-4011176',],
            ['broker_no'=>'51','name'=>'Oxford Securities Pvt.Ltd','address'=>'New Plaza, Kathmandu','phone'=>'01-4278113',],
            ['broker_no'=>'52','name'=>'Sundhara Securities Limited','address'=>'Kalimati, Kathmandu','phone'=>'01-4212215',],
            ['broker_no'=>'53','name'=>'Investment Management Nepal Pvt. Ltd.','address'=>'Sundhara, Kathmandu','phone'=>'01-4256589',],
            ['broker_no'=>'54','name'=>'Sewa Securities Pvt. Ltd.','address'=>'Tripureshwor, Kathmandu','phone'=>'01-4256642',],
            ['broker_no'=>'55','name'=>'Bhrikuti Stock Broking Co. Pvt. Ltd.','address'=>'Tripureshwor, Kathmandu','phone'=>'01-4233213',],
            ['broker_no'=>'56','name'=>'Shree Hari Securities Pvt.Ltd','address'=>'New Road, Opposite to Bhugol Park, Kathmandu, Nepal','phone'=>'01-4437562',],
            ['broker_no'=>'57','name'=>'Araya Tara Investment And Securities Pvt. Ltd.','address'=>'Kamaladi, Kathmandu','phone'=>'01-5706297',],
            ['broker_no'=>'58','name'=>'Naasa Securities Co. Ltd.','address'=>'Anamnagar, Kathmandu','phone'=>'01-4440384',],
            ['broker_no'=>'59','name'=>'Deevyaa Securities & Stock House Pvt. Ltd','address'=>'Naxal, Kathmandu','phone'=>'01-4421488',],
            
        ];

        foreach ($brokers as $value) {
            \DB::table('brokers')->insert(
                [
                    'broker_no' => $value['broker_no'],
                    'broker_name' => $value['name'],
                    'office_address' => $value['address'],
                    'phone' => $value['phone'],
                    'created_at' => Carbon::now()->toDateTimeString()
                ]);
        } 

        
    }
}
