@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    @include('partials.header')
    @include('partials.products-recommended')
    @include('partials.footer')
@endsection
