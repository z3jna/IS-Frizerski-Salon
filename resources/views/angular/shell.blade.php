@extends('layouts.app')

@section('content')
<script>
    window.salonRoutes = {
        phpBase: @json(\App\Support\FrontendUrl::appBase()),
        angularBase: @json(\App\Support\FrontendUrl::angularBase()),
    };
</script>
<app-root></app-root>
@endsection
