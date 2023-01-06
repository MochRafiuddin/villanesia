@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
$name[] = 'nama_banner';
$name[] = 'status';
$name[] = 'image_banner';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} Banner</h6>
                <form action="{{$url}}" method="post" enctype="multipart/form-data">
                    @csrf                    
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Nama</label>
                            <input type="text" class="form-control @error($name[0]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[0])}}" name="{{$name[0]}}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Status</label>                            
                            <select class="form-control js-example-basic-single @error($name[1]) invalid @enderror" name="{{$name[1]}}" id="">
                                <option value="1" {{(old($name[1]) == '1') ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[1],'1')}}>Tampil</option>
                                <option value="0" {{(old($name[1]) == '0') ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[1],'0')}}>Tidak Tampil</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Gambar</label>                            
                            <input type="file" class="dropify form-control @error($name[2]) is-invalid @enderror" name="{{$name[2]}}" @if($data) data-default-file="{{asset('upload/banner/'.$data->image_banner)}}" @endif/>                            
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
