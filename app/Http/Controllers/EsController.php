<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Elasticsearch\ClientBuilder;

class EsController extends Controller
{
    protected  $elastic;
    protected  $host=['http://elasticsearch:9200'];
    public function __construct()
    {
        $this->elastic=ClientBuilder::create()->setHosts($this->host)->build();

    }

    public function createIndex(){
        //普通索引

//        $params=[
//            'index'=>'my_index',
//            'type'=>'my_type',
//            'id'=>'my_id',
//            'body'=>[
//                'textfield'=>'abc'
//            ],
//        ];
//        $response=$this->elastic->index($params);
//        dd($response);

        //
//        $params2=[
//            'index'=>'my_index',
//            'body'=>[
//                'settings'=> [
//                    'number_of_shards'=> 5,
//                    'number_of_replicas'=> 1,
//                ],
//              'mappings'=>$this->mappings(),
//
//            ],
//        ];
//
//        $rs=$this->elastic->indices()->create($params2);
//        dd($rs);

}
    //映射
    public function mappings(){
        return[
            'my_type' => [
                '_source' => [
                    'enabled' => true
                ],
                'properties' => [
                    'first_name' => [
                        'type' => 'keyword',
                    ],
                    'age' => [
                        'type' => 'integer'
                    ]
                ]
            ]
            ];


    }
    public function getIndex(){
        $params=[
            'index'=>'my_index',
            'type'=>'my_type',
            'id'=>'my_id',
            //忽略错误请求
            'client' => [ 'ignore' => [400, 404] ]
        ];
        $rs=$this->elastic->get($params);
        dd($rs);
    }
    public function search(){
        //查询
        $params=[
            'index'=>'my_index',
            'type'=>'my_type',
            'body'=>[
                'query'=>[
                    'match'=>[
                        'textfield'=>'abc'
                    ]
                ]
            ],
        ];
        //自定义查询
        $params2=[

        ];
        $rs=$this->elastic->search($params);
        dd($rs);
    }
    public function delete(){
        $params=[
            'index'=>'my_index',
//            'type'=>'my_type',
//             'id'=>'my_id',
        ];
        $rs=$this->elastic->indices()->delete($params);
        dd($rs);
    }
    public function setting(){
        $params=[
            'index'=>'my_index',
           'body'=>[
               'settings'=> [
                   'number_of_shards'=> 2,
                   'number_of_replicas'=> 0,
        ]
            ],
        ];
        $response=$this->elastic->indices()->create($params);
        dd($response);
    }
    //设置分片数
    public function setseting(){
        return [
                'number_of_shards'=> 4,
                'number_of_replicas'=> 2,
        ];
    }
    //设置分析

}
