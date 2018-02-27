@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!

                    <a href="/order" class="btn btn-primary">抢购</a>

                    @if (session('msg'))
                        <div class="alert alert-success">
                            {{ session('msg') }}
                        </div>
                    @endif

                    <br/>抢购成功列表：
                    <table class="table">
                        <th>用户ID</th>
                        <th>用户名</th>
                        <th>订单号</th>
                        <th>抢购时间</th>
                        @foreach($orders as $order)
                        <tr>
                            <td>{{$order['user_id']}}</td>
                            <td>{{$order['user_name']}}</td>
                            <td>{{$order['order_no']}}</td>
                            <td>{{$order['created_at']}}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script src="https://cdn.bootcss.com/jquery/1.8.0/jquery-1.8.0.js"></script>
<script type="text/javascript">

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function order() {

        $.ajax({
            type: 'post',
            url: '/order',
            success: function(data){
                alert(data.msg);
            },
        });
    }
</script>
