@extends('layouts.app')

@section('title', 'Beranda - CBT Examify')

@section('content')
<script>window.location.href = "{{ route('home') }}";</script>
<div class="text-center py-16">
    <p class="text-sm text-slate-400">Mengalihkan ke halaman beranda...</p>
</div>
@endsection
