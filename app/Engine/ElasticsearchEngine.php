<?php
namespace  App\Engine;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;
use Elasticsearch\Client;
use Illuminate\Database\Eloquent\Collection;

class ElasticsearchEngine extends  Engine{
    //索引
    protected $index;
    //搜索引擎
    protected $elastic;
    //初始化
    public function __construct(Client $elastic,$index)
    {
        $this->elastic=$elastic;
        $this->index=$index;

    }
    //更新
    public function update($models)
    {
        $params['body'] = [];
        $models->each(function($model) use (&$params)
        {
            $params['body'][] = [
                'update' => [
                    '_id' => $model->getKey(),
                    '_index' => $this->index,
                    '_type' => $model->searchableAs(),
                ]
            ];
            $params['body'][] = [
                'doc' => $model->toSearchableArray(),
                'doc_as_upsert' => true
            ];
        });
        $this->elastic->bulk($params);
    }
    //删除
    public function delete($models)
    {
        $params['body'] = [];
        $models->each(function($model) use (&$params)
        {
            $params['body'][] = [
                'delete' => [
                    '_id' => $model->getKey(),
                    '_index' =>$this->index,
                    '_type' => $model->searchableAs(),
                ]
            ];
        });
        $this->elastic->bulk($params);
    }
    //搜索
    public function search(Builder $builder)
    {
        return $this->performSearch($builder, array_filter([
            'numericFilters' => $this->filters($builder),
            'size' => $builder->limit,
        ]));

    }
    //搜索参数
    protected function performSearch(Builder $builder, array $options = [])
    {
        $params = [
            'index' => $this->index,
            'type' => $builder->index ?: $builder->model->searchableAs(),
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [['query_string' => [ 'query' => "*{$builder->query}*"]]]
                    ]
                ]
            ]
        ];
        if ($sort = $this->sort($builder)) {
            $params['body']['sort'] = $sort;
        }
        if (isset($options['from'])) {
            $params['body']['from'] = $options['from'];
        }
        if (isset($options['size'])) {
            $params['body']['size'] = $options['size'];
        }
        if (isset($options['numericFilters']) && count($options['numericFilters'])) {
            $params['body']['query']['bool']['must'] = array_merge($params['body']['query']['bool']['must'],
                $options['numericFilters']);
        }
        if ($builder->callback) {
            return call_user_func(
                $builder->callback,
                $this->elastic,
                $builder->query,
                $params
            );
        }
        return $this->elastic->search($params);
    }
    //获取查询的过滤器数组
    protected function filters(Builder $builder)
    {
        return collect($builder->wheres)->map(function ($value, $key) {
            if (is_array($value)) {
                return ['terms' => [$key => $value]];
            }
            return ['match_phrase' => [$key => $value]];
        })->values()->all();
    }
    //生成排序结果
    protected function sort($builder)
    {
        if (count($builder->orders) == 0) {
            return null;
        }
        return collect($builder->orders)->map(function($order) {
            return [$order['column'] => $order['direction']];
        })->toArray();
    }
    //分页
    public function paginate(Builder $builder, $perPage, $page)
    {
        $result = $this->performSearch($builder, [
            'numericFilters' => $this->filters($builder),
            'from' => (($page * $perPage) - $perPage),
            'size' => $perPage,
        ]);
        $result['nbPages'] = $result['hits']['total']/$perPage;
        return $result;
    }
    //采集并返回给定结果的主键
    public function mapIds($results)
    {
        return collect($results['hits']['hits'])->pluck('_id')->values();
    }
    //将给定结果映射到给定模式的实例
    public function map($results, $model)
    {

        if ($results['hits']['total'] === 0) {
            return Collection::make();
        }
        $keys = collect($results['hits']['hits'])
            ->pluck('_id')->values()->all();
        $models = $model->whereIn(
            $model->getKeyName(), $keys
        )->get()->keyBy($model->getKeyName());
        return collect($results['hits']['hits'])->map(function ($hit) use ($model, $models) {
            return isset($models[$hit['_id']]) ? $models[$hit['_id']] : null;
        })->filter()->values();

    }
    //获取总行数
    public function getTotalCount($results)
    {
        return $results['hits']['total'];

    }
}