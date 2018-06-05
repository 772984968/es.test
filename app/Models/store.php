<?php

namespace App\Models;

use App\Traits\StoreElastic;
use Illuminate\Database\Eloquent\Model;
use Elasticquent\ElasticquentTrait;

class store extends Model
{
    use ElasticquentTrait;
    use StoreElastic;
    public $timestamps = false;
    public function index(){
    }
    public $indexSetting;


}
