@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
$name[] = 'nama_fasilitas';
$name[] = 'tampil_depan';
$name[] = 'icon';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} Fasiitas</h6>
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
                            <label for="exampleInputEmail1">Tampil pada filter</label>
                            <select class="form-control js-example-basic-single" name="{{$name[1]}}" id="{{$name[1]}}" style="width:100%">
                                <option value="0" {{(old($name[1]) == 0) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[1],0)}}>
                                        Tidak
                                </option>
                                <option value="1" {{(old($name[1]) == 1) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[1],1)}}>
                                        Ya
                                </option>
                             </select>
                        </div>
                    </div>                     
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Icon <b>(128x128 / ratio 1:1)</b></label>                            
                            <input type="file" class="dropify form-control" name="{{$name[2]}}" @if($data) data-default-file="{{asset('upload/fasilitas/'.$data->icon)}}" @endif/>                            
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