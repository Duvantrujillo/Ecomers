@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<style>
  [x-cloak] { display: none !important; }
</style>
   @include('components.cart.drawer')
    @include('partials.header')
    @include('partials.products-recommended')
    @include('partials.footer')
@endsection
