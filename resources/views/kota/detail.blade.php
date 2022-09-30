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
                <h6 class="card-title">{{$titlePage}} Fasilitas</h6>
                <form action="" method="post" enctype="multipart/form-data">
                    @csrf                    
                    <div class="row">
                        <div class="form-group col-12">
                            <label for="exampleInputEmail1">Nama</label>
                            <input type="text" class="form-control @error($name[0]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[0])}}" name="{{$name[0]}}" readonly/>
                        </div>
                        <div class="form-group col-12">
                            <label for="exampleInputEmail1">Provinsi</label>
                            <select class="form-control js-example-basic-single" name="id_provinsi" id="id_provinsi" style="width:100%" readonly>
                                <option value="" selected disabled>Pilih</option>
                                @foreach($provinsi as $pro)
                                    <option value="{{$pro->id_provinsi}}" {{($data->id_provinsi == $pro->id_provinsi) ? 'selected' : ''}}>{{$pro->nama_provinsi}}</option>
                                @endforeach
                            <select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Gambar</label><br>
                            <!-- <input type="text" class="form-control @error($name[1]) is-invalid @enderror" value="{{Helper::showData($data,$name[1])}}" name="{{$name[1]}}" /> -->
                            <img class="rounded" width="40%" src="{{asset('upload/kota/'.$data->gambar)}}" alt="{{$data->nama_kota}}">
                        </div>                        
                    </div>
                    <a class="btn btn-success" href="{{url('kota')}}">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="{{asset('/')}}assets/js/dropify.js"></script>
@endpush