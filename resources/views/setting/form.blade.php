@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
$name[] = 'kode';
$name[] = 'nama';
$name[] = 'nilai';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} Seting</h6>
                <form action="{{$url}}" method="post" enctype="multipart/form-data">
                    @csrf                    
                    <div class="row">
                        <div class="form-group col-12">
                            <label for="exampleInputEmail1">Kode</label>
                            <input type="text" class="form-control @error($name[0]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[0])}}" name="{{$name[0]}}" readonly/>
                        </div>
                        <div class="form-group col-12">
                            <label for="exampleInputEmail1">Nama</label>
                            <input type="text" class="form-control @error($name[1]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[1])}}" name="{{$name[1]}}" readonly/>
                        </div>
                        @if($data->id == 4)
                            <div class="form-group col-12">
                                <label for="exampleInputEmail1">Nilai</label>
                                <select class="form-control js-example-basic-single" name="{{$name[2]}}" id="{{$name[2]}}"
                                        style="width:100%">                                      
                                    <option value="0" {{(old($name[2]) == 0) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[1],0)}}>
                                            Tidak Tampil
                                    </option>                                
                                    <option value="1" {{(old($name[2]) == 1) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[1],1)}}>
                                            Tampil
                                    </option>
                                </select>                
                            </div>                        
                        @else
                        <div class="form-group col-12">
                            <label for="exampleInputEmail1">Nilai</label>
                            <input type="text" class="form-control @error($name[2]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[2])}}" name="{{$name[2]}}"/>
                        </div>
                        @endif
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