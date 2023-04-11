@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
$name[] = 'nama_tipe_properti';
$name[] = 'gambar';
$name[] = 'urutan';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} Tipe Properti</h6>
                <form action="{{$url}}" method="post" enctype="multipart/form-data">
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
                                value="{{Helper::showData($data,$name[0])}}" name="{{$name[0]}}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Urutan</label>
                            <input type="text" class="form-control @error($name[2]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[2])}}" name="{{$name[2]}}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Gambar <b>(1000x500 / ratio 2:1)</b></label>
                            <!-- <input type="text" class="form-control @error($name[1]) is-invalid @enderror" value="{{Helper::showData($data,$name[1])}}" name="{{$name[1]}}" /> -->
                            <input type="file" class="dropify form-control @error($name[1]) is-invalid @enderror" name="{{$name[1]}}"
                             @if($data) data-default-file="{{asset('upload/tipe_properti/'.$data->gambar)}}" @endif/>
                        </div>                        
                    </div>
                    <input type="submit" class="btn btn-success" value="Simpan" />
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="{{asset('/')}}assets/js/dropify.js"></script>
@endpush