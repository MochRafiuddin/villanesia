@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
$name[] = 'judul';
$name[] = 'isi';
$name[] = 'gambar';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} About Us</h6>
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
                            <label for="exampleInputEmail1">Judul</label>
                            <input type="text" class="form-control @error($name[0]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[0])}}" name="{{$name[0]}}" readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Isi</label>
                            <!-- <input type="text" class="form-control @error($name[1]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[1])}}" name="{{$name[1]}}" readonly/> -->
                            <textarea class="" name="{{$name[1]}}" id="tinyMceExample" readonly>{{Helper::showData($data,$name[1])}}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Gambar <b>(1000x500 / ratio 2:1)</b></label><br>
                            <!-- <input type="text" class="form-control @error($name[1]) is-invalid @enderror" value="{{Helper::showData($data,$name[1])}}" name="{{$name[1]}}" /> -->
                            <img class="rounded" width="40%" src="{{asset('upload/about_us/'.$data->gambar)}}" alt="About Us">
                        </div>                        
                    </div>
                    <a class="btn btn-success" href="{{url('about-us')}}">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="{{asset('/')}}assets/js/dropify.js"></script>
<script src="{{asset('/')}}assets/vendors/tinymce/tinymce.min.js"></script>
<script type='text/javascript'>
    tinymce.init({ selector:'textarea', menubar:'', theme: 'modern', readonly : 1});
</script>
@endpush