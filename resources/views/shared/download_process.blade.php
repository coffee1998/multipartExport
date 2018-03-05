@section('layout-script')
    <script type="text/javascript" src="/plugins/loaders/progressbar.min.js"></script>
    <script src="/js/sweet_alert.min.js"></script>
    <script type="text/javascript" src="/js/download_ui.js?v=1.0.0.1"></script>
    <script type="text/template" id="tpl-download-process">
        <div class="panel panel-body border-top-primay text-center" style="padding-top:0;">
            <p class="content-group-sm text-muted" style="color: #dd4b39;">请勿刷新</p>
            <div class="progress content-group-sm" id="h-fill-basic">
                <div class="progress-bar progress-bar-aqua" data-transitiongoal-backup="75" data-transitiongoal="75" style="width: 0%">
                    <span class="sr-only">0%</span>
                </div>
            </div>
            <a class="btn btn-danger legitRipple hidden" id="monitor_download_file-btnIconEl">下载</a>
        </div>
    </script>
@endsection