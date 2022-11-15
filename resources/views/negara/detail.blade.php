@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
$name[] = 'nama_negara';
$name[] = 'iso_code';
$name[] = 'gambar';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} Negara</h6>
                <form action="" method="post" enctype="multipart/form-data">
                    @csrf                    
                    <div class="row">
                        <div class="form-group col-12">
                            <label for="exampleInputEmail1">Nama</label>
                            <input type="text" class="form-control @error($name[0]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[0])}}" name="{{$name[0]}}" readonly/>
                        </div>
                        <div class="form-group col-12">
                            <label for="exampleInputEmail1">Iso Code</label>
                            <input type="text" class="form-control @error($name[1]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[1])}}" name="{{$name[1]}}" readonly/>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Gambar</label><br>                            
                            <img class="rounded" width="40%" src="{{asset('upload/negara/'.$data->gambar)}}" alt="{{$data->nama_negara}}">
                        </div>                        
                    </div> -->
                    <a class="btn btn-success" href="{{url('negara')}}">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="{{asset('/')}}assets/js/dropify.js"></script>
@endpush