@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">用户列表</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{--You are logged in!--}}

                    <form method="get" id="form2">
                        <div class="row">
                            <div class="col-xs-12">
                                <label class="pull-right"><a href="javascript:;" class="btn btn-primary" data-plugin="exprot-file" data-export-form="form2" data-export-url="{{ route('home') }}">导出Excel</a>&nbsp;&nbsp;&nbsp;&nbsp;</label>
                            </div>
                        </div>
                    </form>

                    @if (session('msg'))
                        <div class="alert alert-success">
                            {{ session('msg') }}
                        </div>
                    @endif

                    <table class="table">
                        <th>用户ID</th>
                        <th>用户名</th>
                        <th>Email</th>
                        <th>注册时间</th>
                        @foreach($users as $user)
                        <tr>
                            <td>{{$user->id}}</td>
                            <td>{{$user->name}}</td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->created_at}}</td>
                        </tr>
                        @endforeach
                    </table>
                    <div class="pagination" style="margin-left:10px;">
                        {!!$users->appends(request()->except('page'))->links()!!}
                    </div>
                    <label class="pull-right" style="padding:40px 40px 0;">Total:{{$users->toTal()}}&nbsp;&nbsp;&nbsp;&nbsp;</label>
                </div>
            </div>
        </div>

        @include('shared.download_process')
    </div>
</div>
@endsection
