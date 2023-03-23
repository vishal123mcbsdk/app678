@extends('vendor.installer.layouts.master')

@section('title', trans('installer_messages.final.title'))
@section('container')
    <p class="paragraph" style="text-align: center;">{{ session('message')['message'] }}</p>
	<p><a href="https://cutt.ly/PLFZenO" target="_blank">Nulled Community</a></p>
    <div class="buttons">
        <a href="{{ url('/') }}" class="button">{{ trans('installer_messages.final.exit') }}</a>
    </div>
@stop
