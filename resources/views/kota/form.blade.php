@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
$name[] = 'nama_kota';
$name[] = 'id_provinsi';
$name[] = 'gambar';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} Kota</h6>
                <form action="{{$url}}" method="post" enctype="multipart/form-data">
                    @csrf                    
                    <div class="row">
                        <div class="form-group col-12">
                            <label for="exampleInputEmail1">Nama</label>
                            <input type="text" class="form-control @error($name[0]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[0])}}" name="{{$name[0]}}" />
                        </div>
                        <div class="form-group col-12">
                            <label for="exampleInputEmail1">Provinsi</label>
                            <select class="form-control js-example-basic-single @error($name[1]) invalid @enderror" name="{{$name[1]}}" id="{{$name[1]}}" style="width:100%">
                                <option value="" selected disabled>Pilih</option>
                                @foreach($provinsi as $pro)
                                    <option value="{{$pro->id_provinsi}}" {{(old($name[1]) == $pro->id_provinsi) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[1],$pro->id_provinsi)}}>{{$pro->nama_provinsi}}</option>
                                @endforeach
                            <select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Gambar <b>(360x360 / ratio 1:1)</b></label>
                            <!-- <input type="text" class="form-control @error($name[1]) is-invalid @enderror" value="{{Helper::showData($data,$name[1])}}" name="{{$name[1]}}" /> -->
                            <input type="file" class="dropify form-control @error($name[2]) is-invalid @enderror" name="{{$name[2]}}" @if($data) data-default-file="{{asset('upload/kota/'.$data->gambar)}}" @endif/>                            
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