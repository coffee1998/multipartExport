<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class HomeController extends Controller
{

    public $msg = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        $goodsId = 1;   //商品ID号，这里我写死为1

        $this->user_queue_key = "goods:{$goodsId}:user";    //抢购成功队列的key，使用redis的hashes方式存储

        $this->goods_number_key = "goods:{$goodsId}";   //商品库存key，使用redis的lists方式存储
    }   

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $orders = Redis::hgetall($this->user_queue_key);

        foreach ($orders as $key => $order) {
            $orders[$key] = unserialize($order);
        }

        return view('home', compact('orders'));
    }

    /**
     * 开始秒杀
     *
     */
    public function secKill()
    {

        $isHaveQueue = Redis::hexists($this->user_queue_key, auth()->id());

        if ($isHaveQueue) {
            return back()->with('msg', '您已抢购！');
        }

        $count = Redis::rpop($this->goods_number_key);

        if (!$count) {
            return back()->with('msg', '已经抢光了哦！');
        }

        $this->succUserNumberQueue(auth()->id());

        return back()->with('msg', '抢购成功！');
        
    }

    /**
     * 抢购结果队列，将抢购成功的客户加入队列
     *
     * @return bool
     */
    private function succUserNumberQueue($uid)
    {

        $orderNo = $this->buildOrderNo();

        $userQueue = [
            'user_id' => auth()->id(),
            'user_name' => \Auth::user()->name,
            'order_no' => $orderNo,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $userQueue = serialize($userQueue);

        Redis::hset($this->user_queue_key, $uid, $userQueue);

        return true;
    }

    //生成唯一订单号
    private function buildOrderNo()
    {
      return date('ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

    /**
     * 将商品库存加入redis，生成库存
     *
     */
    public function goodsNumberQueue()
    {
        $store = 1;

        $goodsLen = Redis::llen($this->goods_number_key);

        $count = $store - $goodsLen <= 0 ? 0 : $store - $goodsLen;

        for($i = 0; $i < $count; $i++) {
            Redis::lpush($this->goods_number_key, 1);
        }

        echo '目前库存：' . Redis::llen($this->goods_number_key) . '<br/>';
    }



}
