@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
$name[] = 'nama_fasilitas';
$name[] = 'icon';
$name[] = 'tampil_depan';
$name[] = 'gambar';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} Fasilitas</h6>
                <form action="" method="post" enctype="multipart/form-data">
                    @csrf            
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Bahasa</label><br>
                            <i class="flag-icon {{$bahasa->logo}}"></i> {{$bahasa->nama_bahasa}}
                            <input type="hidden" name="id" value="{{$id}}">
                            <input type="hidden" name="id_bahasa" value="{{$bahasa->id_bahasa}}">
                        </div>
                    </div>        
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Nama</label>
                            <input type="text" class="form-control @error($name[0]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[0])}}" name="{{$name[0]}}" readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Icon</label><br>                            
                            <img class="rounded" width="20%" src="{{asset('upload/fasilitas/'.$data->icon)}}" alt="{{$data->nama_fasilitas}}">
                        </div>                        
                    </div>
                    <a class="btn btn-success" href="{{url('fasilitas')}}">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="{{asset('/')}}assets/js/dropify.js"></script>
@endpush