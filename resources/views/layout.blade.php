<!DOCTYPE html>
<html>
<head>
    <title>{{ seo('title') }}</title>
    <meta charset="utf-8">
    <meta name="description" content="{{ seo('description') }}">
    <meta name="keyword" content="{{ seo('keywords') }}">
    <link href="{{ asset('assets/admin/css/app.min.css') }}" rel="stylesheet">
    @foreach($extensions as $extension)
        @if($extension->getStylesheet())
            @foreach($extension->getStylesheet() as $stylesheet)
                <link href="{{ $stylesheet }}" rel="stylesheet">
            @endforeach
        @endif
    @endforeach
</head>
<body>
<div id="app">
    <img src="{{ asset('assets/admin/images/loading.svg') }}"
         style="position: absolute;
             background: #fff;
             height: 80%;
             left: 50%;
             top: 50%;
             -moz-transform: translate(-50%, -50%);
             -o-transform: translate(-50%, -50%);
             -webkit-transform: translate(-50%, -50%);
             transform: translate(-50%, -50%);">
</div>
<script>
    window.admin = "{{ url('admin') }}";
    window.api = "{{ url('api') }}";
    window.asset = "{{ asset('assets') }}";
    window.csrf_token = "{{ csrf_token() }}";
    window.domain = "{{ url('') }}";
    window.extensions = [
        @foreach($extensions as $extension)
            "{{ $extension->getIdentification() }}",
        @endforeach
    ];
    window.local = {!! $translations !!};
    window.token = "{{ url('admin/token') }}";
    window.upload = "{{ url('editor') }}";
    window.url = "{{ url('') }}";
    window.UEDITOR_HOME_URL = "{{ asset('assets/neditor') }}/";
</script>
<script src="{{ asset('assets/admin/js/manifest.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/vendor.min.js') }}"></script>
@foreach($extensions as $extension)
    @if($extension->getScript())
        <script src="{{ $extension->getScript() }}"></script>
    @endif
@endforeach
<script src="{{ asset('assets/admin/js/app.min.js') }}"></script>
</body>