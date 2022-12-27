@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
$name[] = 'username';
$name[] = 'password';
$name[] = 'email';
$name[] = 'no_telfon';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} User</h6>
                <form action="{{$url}}" method="post" enctype="multipart/form-data">
                    @csrf                    
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Username</label>
                            <input type="text" class="form-control @error($name[0]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[0])}}" name="{{$name[0]}}" readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Email</label>
                            <input type="text" class="form-control @error($name[2]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[2])}}" name="{{$name[2]}}" readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Phone</label>
                            <input type="text" class="form-control @error($name[3]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[3])}}" name="{{$name[3]}}" readonly/>
                        </div>
                    </div>
                    <a href="{{url('user')}}" class="btn btn-success">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
