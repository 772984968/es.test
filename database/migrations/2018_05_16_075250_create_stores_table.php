<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('stores', function (Blueprint $table) {
                $table->integer('store_id')->comment('店铺id');
                $table->string('country', 30)->default('')->comment('国家');
                $table->string('prov', 30)->default('')->comment('省');
                $table->string('city', 30)->default('')->comment('市');
                $table->string('area', 30)->default('')->comment('区');
                $table->string('town', 30)->default('')->comment('镇');
                $table->string('address')->comment('地址');
                $table->double('lat', 12, 8)->default(0.00000000)->comment('经度');
                $table->double('lon', 12, 8)->default(0.00000000)->comment('纬度');
                $table->string('licence_image')->comment('营业执照电子版');
                $table->enum('business_status', ['vacation', 'rest', 'normal'])->default('normal')->comment('营业状态');
                $table->string('business_hours')->default('09:00-24:00')->comment('营业时间');
            });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
}
