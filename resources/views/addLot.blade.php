@extends('layouts.app')

@section('content')
    <div class="container">
        @isset($error)
            <div class = "alert alert-danger" style="margin:10px">
                Sorry, error has been occurred: {{$error}}
            </div>
            @endisset
        @isset($lot)
                <div class = "alert alert-success" style="margin:10px">
                    Lot has been added successfully!
                </div>
            @endisset
        <form class = "form"  method="POST" action = "{{route('addLot')}}">
            @csrf
            <div class="form-group">
                <lable for = "price">Price</lable>
                <input  class="form-control" type = "number" name="price" id="price" value = "{{old('price')}}">
                @if($errors->has('price'))
                    <small class = "text-danger"> {{$errors->first('price')}} </small>
                @endif
            </div>
            <div class="form-group">
                <lable for = "date-time-open">Date time open</lable>
                <input class="form-control" type = "datetime" name = "date_time_open" id="date-time-open" placeholder="yyyy/mm/dd hh:mm:ss" value = "{{old('date_time_open')}}">
                @if($errors->has('date_time_open'))
                    <small class = "text-danger"> {{$errors->first('date_time_open')}} </small>
                @endif
            </div>
            <div class="form-group">
                <lable for = "date-time-close">Date time close</lable>
                <input class="form-control" type = "datetime" name = "date_time_close" id="date-time-close" placeholder="yyyy/mm/dd hh:mm:ss" value = "{{old('date_time_close')}}">
                @if($errors->has('date_time_close'))
                    <small class = "text-danger"> {{$errors->first('date_time_close')}} </small>
                @endif
            </div>
            <div class="form-group">
                <lable for = "currency-id">Currency id</lable>
                <input class="form-control" type = "number"  name = "currency_id" id="currency-id" value = "{{old('currency_id')}}">
                @if($errors->has('currency_id'))
                    <small class = "text-danger"> {{$errors->first('currency_id')}} </small>
                @endif
            </div>
            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>
    @endsection