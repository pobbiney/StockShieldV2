@if(session('message_success'))
    <div class="rp-alert success">
        <i class="bi bi-check-circle"></i> {{ session('message_success') }}
    </div>
@endif

@if(session('message_error'))
    <div class="rp-alert error">
        <i class="bi bi-exclamation-circle"></i> {{ session('message_error') }}
    </div>
@endif

@if(isset($liststock))
    @if($liststock->count() > 0)
        <div class="rp-alert success">
            <i class="bi bi-check-circle"></i> {{ $liststock->count() }} result(s) found
        </div>
    @elseif(request()->isMethod('post') || request()->filled('department') || request()->filled('start_date'))
        <div class="rp-alert error">
            <i class="bi bi-exclamation-circle"></i> No results found
        </div>
    @endif
@endif

@if(isset($reportData))
    @if(count($reportData) > 0)
        <div class="rp-alert success">
            <i class="bi bi-check-circle"></i> {{ count($reportData) }} result(s) found
        </div>
    @elseif(request()->isMethod('post') || request()->filled('start_date'))
        <div class="rp-alert error">
            <i class="bi bi-exclamation-circle"></i> No results found
        </div>
    @endif
@endif
