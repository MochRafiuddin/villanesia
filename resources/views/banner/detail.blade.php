@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
$name[] = 'nama_banner';
$name[] = 'status';
$name[] = 'image_banner';
$name[] = 'redirect_url';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} Banner</h6>
                <form action="" method="post" enctype="multipart/form-data">
                    @csrf                    
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Nama</label>
                            <input type="text" class="form-control @error($name[0]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[0])}}" name="{{$name[0]}}" readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Status</label>                            
                            <select class="form-control js-example-basic-single @error($name[1]) invalid @enderror" name="{{$name[1]}}" id="" readonly>
                                <option value="1" {{Helper::showDataSelected($data,$name[1],'1')}}>Tampil</option>
                                <option value="0" {{Helper::showDataSelected($data,$name[1],'0')}}>Tidak Tampil</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Redirect Url</label>
                            <input type="text" class="form-control @error($name[3]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[3])}}" name="{{$name[3]}}" readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Gambar</label><br>                            
                            <img class="rounded-0" width="40%" src="{{asset('upload/banner/'.$data->image_banner)}}" alt="{{$data->nama_banner}}">
                        </div>                        
                    </div>
                    <a class="btn btn-success" href="{{url('banner')}}">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="{{asset('/')}}assets/js/dropify.js"></script>
@endpush
