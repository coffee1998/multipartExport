<?php

namespace App;

abstract class CacheKey
{
	/**
	 * 当天二线放弃资源领取总数
	 */
	const TWOLAAC = 'vip-abandonamount-count-';

    /**
     * 当天新资源总数
     */
    const TODAYNEWRCOUNT = 'vip-newamount-count-';

    /**
     * 当天放弃资源总数
     */
    const TODAYABANRCOUNT = 'vip-abandonamount-count-';

    /**
	 * 二线成功分配数
	 */
    const TWOLINESUCCAMOUNT = 'give-sell-sell-succ-amount-';

    /**
     * viewCompose视图缓存
     */
    const DepartViewArray = 'view-department-array-';

    /**
     * 正在拨打电话用户
     */
    const DIALINGUSERVSRESOURCE = 'dialing-user-to-resource-array';

	/**
	 * 数据导出队列缓存数据
	 * @var string
	 */
	const EXPORTSERVICEQUEUE = 'export-queue-array';

	/**
	 * 数据导出完成进度
	 * @var string
	 */
	const EXPORTCOMPELTEPROCESS = 'export-process';

    /**
     * 支付链接支付成功缓存id
     */
    const PAYMENTBYOPENINGLINK = 'payment-opening-link-array';

    /**
     * 支付链接支付成功缓存id
     */
    const CONSULTCREATEINGPROCESS = 'consult-create-data-array';
}
