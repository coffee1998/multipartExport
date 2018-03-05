<?php

namespace App\Services\Export;

use App\Models\RefundUserInfo;
use App\Models\ComplaintAutoVsHandle;

class DataService
{
    /**
     * 导出文件名称
     *
     * @var string
     */
    public $fileName = '';

    /**
     * 导出分页大小限制
     *
     * @var int
     */
    protected $limit = 200;

    /**
     * 缓存有效时间
     *
     * @var int
     */
    protected $timeOut = 360;

    /**
     * 总分页数
     *
     * @var int
     */
    public $totalPage = 1;

    /**
     * 当前分页数
     *
     * @var int
     */
    public $page = 1;

    /**
     * 完成进度
     *
     * @var int
     */
    public $completeSize = 0;

    /**
     * 导出用户信息
     * @author nicoke
     * @date 2018-02-01
     */
    public function exportUsers($model, $exportService)
    {
        $headData = [
            '用户ID',
            '用户名',
            'Email',
            '注册时间',
        ];
        if ($this->page == 1) {
            $exportService->addRow($headData);
        }
        foreach ($model->get() as $i => $list) {
            $row = [
                $list->id,
                $list->name,
                $list->email,
                $list->created_at,
            ];
            $exportService->addRow($row);
        }
        $this->completeProcess();
        return true;
    }
}
