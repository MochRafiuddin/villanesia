@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
$name[] = 'nama_negara';
$name[] = 'gambar';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} Negara</h6>
                <form action="{{$url}}" method="post" enctype="multipart/form-data">
                    @csrf                    
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Nama</label>
                            <input type="text" class="form-control @error($name[0]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[0])}}" name="{{$name[0]}}" />
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Gambar</label>                            
                            <input type="file" class="dropify form-control @error($name[1]) is-invalid @enderror" name="{{$name[1]}}" @if($data) data-default-file="{{asset('upload/negara/'.$data->gambar)}}" @endif/>                            
                        </div>                        
                    </div> -->
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