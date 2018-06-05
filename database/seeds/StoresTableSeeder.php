<?php

use Illuminate\Database\Seeder;

class StoresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        for ($i=0;$i<10;$i++){
            \App\Models\Store::create([
           'store_id'=>random_int(1,10),
            'country'=>'中国',
            'prov'=>'广东省',
            'city'=>'深圳市',
            'area'=>array_random(['南山','龙华','宝安']),
            'town'=>'麻垌镇',
            'address'=>'这是地址哈哈',
            'lat'=>'22.5422907',
            'lon'=>'114.04834288',
            'licence_image'=>'营业执照电子版',
            'business_status'=>array_random(['vacation', 'rest', 'normal']),
            'business_hours'=>'营业时间',
            ]);
        }

    }
}
