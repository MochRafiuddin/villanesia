@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
$name[] = 'username';
$name[] = 'password';
$name[] = 'email';
$name[] = 'no_telfon';
$name[] = 'id_role';
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
                                value="{{Helper::showData($data,$name[0])}}" name="{{$name[0]}}" />
                        </div>
                    </div>
                    @if($data == null)
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Password</label>
                            <input type="password" class="form-control @error($name[1]) is-invalid @enderror" value="" name="{{$name[1]}}" />
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Email</label>
                            <input type="text" class="form-control @error($name[2]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[2])}}" name="{{$name[2]}}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Phone</label>
                            <input type="text" class="form-control @error($name[3]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[3])}}" name="{{$name[3]}}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Role</label>
                            <select class="form-control js-example-basic-single @error($name[4]) invalid @enderror" name="{{$name[4]}}" id="{{$name[4]}}" style="width:100%">
                                <option value="" selected disabled>Pilih</option>
                                @foreach($role as $pro)
                                    <option value="{{$pro->id_role}}" {{(old($name[4]) == $pro->id_role) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[4],$pro->id_role)}}>{{$pro->nama_role}}</option>
                                @endforeach
                            <select>
                        </div>
                    </div>
                    <input type="submit" class="btn btn-success" value="Simpan" />
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
