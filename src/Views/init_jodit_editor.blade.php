@push('head')
    <script>
        let jodieEditorConnectorURL = '{{ url("/") }}/vendor/lnch/laravel-blog/connector/index.php';
    </script>
    <link rel="stylesheet" href="{{ asset("vendor/lnch/laravel-blog/js/jodit/jodit.min.css") }}" />
    <script src="{{ asset("vendor/lnch/laravel-blog/js/jodit/jodit.min.js") }}"></script>
    <script src="{{ asset("vendor/lnch/laravel-blog/js/init-jodit-editor.js") }}"></script>
@endpush