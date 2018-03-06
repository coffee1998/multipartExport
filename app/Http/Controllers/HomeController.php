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
    }   

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = \App\User::latest('created_at');

        if ($request->get('user_id', 0)) {
            $users = $users->where('id', $request->get('user_id', 0));
        }

        if ($request->get('email', 0)) {
            $users = $users->where('email', $request->get('email', 0));
        }

        if ($request->input('export') == 1) {
            return (new \App\Services\ExportService())->handle($users, 'exportUsers');
        }

        $users = $users->paginate();

        return view('home', compact('users'));
    }

    public function downloadExportCsv()
    {
        $fileName = request('export_name');

        if (empty($fileName)) {
            return back()->with('error', '下载失败，文件不存在');
        }

        $exportService = app('export.csv');

        $exportService->setFileName($fileName);

        if (!$exportService->fileExist()) {
            return back()->with('error', '下载失败，文件不存在');
        }

        return $exportService->download();
    }
}
