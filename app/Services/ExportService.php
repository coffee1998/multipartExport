<?php

/*
|--------------------------------------------------------------------------
| 数据导出统一 Service TODO:使用队列
|--------------------------------------------------------------------------
 */

namespace App\Services;

use App\Services\Export\DataService as Builder;

class ExportService extends Builder
{
    /**
     * 执行导出作业
     * @param \Illuminate\Eloquent\Model $model
     * @param string $method
     * @param boolean $hasGroupBy
     * @author nicoke
     * @date 2018-02-01
     * @return boolean
     */
    public function handle($model, $method = '', $hasGroupBy = false)
    {
        $this->fileName = request('export_name');
        $requestData = request()->all();
        $url = request()->url();

        if (!empty($this->fileName) && in_array($this->fileName, $this->cacheQueueUrl())) {
            $this->page = (int) request('page', 1);

            if ($this->md5Sign($this->page) != request('sign')) {
                $this->completeSize = 100;
                $this->clearCacheProcess();
                return $this->finishJob('', 500);
            }

            $this->totalPage = $this->calCountPage($model, $hasGroupBy);
            $model = $this->initLimitModel($model);
            $initData = $this->initializeData($this->fileName);
            $result = $this->{$method}($model, $initData['exportService']);

            $requestData['page'] = $requestData['page'] + 1;
            $requestData['sign'] = $this->md5Sign($requestData['page']);
            $url = $url . '?' . http_build_query($requestData);

            return $this->finishJob($url);
        }

        $initData = $this->initializeData();

        $requestData['export_name'] = $initData['fileName'];
        $requestData['page'] = 1;
        $requestData['sign'] = $this->md5Sign($requestData['page']);
        $url = $url . '?' . http_build_query($requestData);

        return $this->finishJob($url);
    }

    /**
     * initialize data
     * @param $fileName string 文件名
     * @author nicoke
     * @date 2018-02-01
     * @return   [type] [description]
     */
    public function initializeData($fileName = '')
    {
        $exportService = app('export.csv');

        $exportService->setFileName($fileName);

        $this->fileName = $exportService->getFileName();

        if (empty($fileName)) {
            $this->setCacheQueueUrl();
        }

        $fileName = $exportService->getFileName();

        return compact('exportService', 'fileName');
    }

    /**
     * 执行导出任务 TODO:使用队列，现在的方式不安全
     * @param \Illuminate\Eloquent\Model $model
     * @param array $initData
     * @param string $method
     * @author nicoke
     * @date 2018-02-01
     */
    public function startJob($model, $initData, $method)
    {
        // $job = (new \App\Jobs\ExportDataJob($model, $initData['fileName'], $method))->onQueue('twoline_crm');
        //
        // dispatch($job);
    }

    /**
     * 执行导出完成任务
     * @param string $url
     * @param int $code
     * @author nicoke
     * @date 2018-02-01
     * @return   return
     */
    public function finishJob($url, $code = 200)
    {
        return [
            'url' => $url,
            'fileName' => $this->fileName,
            'fileType' => 'csv',
            'fileCharset' => 'gbk',
            'completeSize' => $this->completeSize,
            'code' => $code
        ];
    }

    public function cacheQueueUrl()
    {
        return cache(\App\CacheKey::EXPORTSERVICEQUEUE, []);
    }

    public function setCacheQueueUrl()
    {
        $data = $this->cacheQueueUrl();

        $data = array_merge($data, [$this->fileName]);

        cache([\App\CacheKey::EXPORTSERVICEQUEUE => $data], $this->cacheTimeOut());
    }

    /**
     * 更新完成进度
     * @param    string $fileName
     * @author nicoke
     * @date 2018-02-01
     */
    public function completeProcess()
    {
        if ($this->totalPage == 0) {
            $this->completeSize = 100;
        } else {
            $this->completeSize = ceil(($this->page / $this->totalPage) * 100);
        }

        logger()->info('export-process-' . $this->page . '-' . $this->totalPage . '-' . $this->completeSize);

        if ($this->completeSize >= 100) {
            $this->clearCacheProcess();
        } else {
            cache([
                \App\CacheKey::EXPORTCOMPELTEPROCESS . $this->fileName => $this->completeSize
            ], $this->cacheTimeOut());
        }

        ob_end_clean();
    }

    /**
     * 获取当前缓存进度
     * @author nicoke
     * @date 2018-02-01
     * @return   int
     */
    public function getCompleteProcess()
    {
        return cache(\App\CacheKey::EXPORTCOMPELTEPROCESS . $this->fileName, 0);
    }

    /**
     * 清除已完成任务
     * @author nicoke
     * @date 2018-02-01
     * @return   [type] [description]
     */
    public function clearCacheProcess()
    {
        $data = $this->cacheQueueUrl();

        $data = array_diff($data, [$this->fileName]);

        cache([\App\CacheKey::EXPORTSERVICEQUEUE => $data], $this->cacheTimeOut());

        \Cache::forget(\App\CacheKey::EXPORTCOMPELTEPROCESS . $this->fileName);
    }

    /**
     * 缓存有效时间
     * @author nicoke
     * @date 2018-02-01
     * @return   [type] [description]
     */
    public function cacheTimeOut()
    {
        return $this->timeOut;
    }

    /**
     * init limit & skip the model
     * @param \Illuminate\Eloquent\Model $model
     * @author nicoke
     * @date 2018-02-01
     * @return   \Illuminate\Eloquent\Model
     */
    public function initLimitModel($model)
    {
        if (is_array($model)) {
            return $model;
        }

        $page = $this->page - 1;
        $page = $page <= 0 ? 0 : $page;
        $offset = $this->limit * $page;

        $model = $model->limit($this->limit)->skip($offset);

        return $model;
    }

    /**
     * 计算总分页数
     * @param \Illuminate\Eloquent\Model|array $model
     * @author nicoke
     * @date 2018-02-01
     * @return int
     */
    public function calCountPage($model, $hasGroupBy = false)
    {
        if (is_array($model)) {
             return 1;
        }

        $count = clone($model);

        if ($hasGroupBy) {
            $count = $count->select(\DB::raw('count(*) as aggregate'))->get()->count();
        } else {
            $count = $count->count();
        }

        $totalPage =ceil($count / $this->limit);

        return $totalPage;
    }

    /**
     * md5Sign
     * @param    int $page
     * @author nicoke
     * @date 2018-02-01
     * @return   string
     */
    public function md5Sign($page)
    {
        $param = [
            'key' => env('APP_KEY', 'NWQTOwxUCUA1CbOS2od4575am0GbuvoP'),
            'fileName' => $this->fileName,
            'page' => $page
        ];

        $str = http_build_query($param);

        return md5($str);
    }

}
