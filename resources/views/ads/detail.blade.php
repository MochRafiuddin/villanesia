@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
$name[] = 'nama_ads';
$name[] = 'tipe_konten_ads';
$name[] = 'redirect_url_ads';
$name[] = 'status';
$name[] = 'konten_ads';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} Ads</h6>
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
                            <label for="exampleInputEmail1">Tipe Konten</label>
                            <select class="form-control js-example-basic-single" name="{{$name[1]}}" id="{{$name[1]}}"
                                     style="width:100%" data-maximum-selection-length="10" disabled="">
                                <option value="">Pilih Tipe</option>
                                <option value="1" {{(old($name[1]) == 1) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[1],1)}}>
                                        image / gif
                                </option>                                
                                <option value="2" {{(old($name[1]) == 2) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[1],2)}}>
                                        video
                                </option>
                             </select>                
                        </div>
                    </div>                   
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Redirect Url</label>
                            <input type="text" class="form-control @error($name[2]) is-invalid @enderror"
                            value="{{Helper::showData($data,$name[2])}}" name="{{$name[2]}}" readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Status</label>
                            <select class="form-control js-example-basic-single" name="{{$name[3]}}" id="{{$name[3]}}"
                                     style="width:100%" data-maximum-selection-length="10" disabled="">
                                <option value="">Pilih Tipe</option>
                                <option value="0" {{(old($name[3]) == 0) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[3],0)}}>
                                        pending
                                </option>                                
                                <option value="1" {{(old($name[3]) == 1) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[3],1)}}>
                                        show(running)
                                </option>
                                <option value="2" {{(old($name[3]) == 2) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[3],2)}}>
                                        complete
                                </option>
                                <option value="3" {{(old($name[3]) == 3) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[3],3)}}>
                                        banned
                                </option>
                             </select>
                        </div>
                    </div>                    
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Konten</label>
                            <input type="text" class="form-control @error($name[4]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[4])}}" name="{{$name[4]}}" readonly/>
                        </div>
                    </div>                    
                    <a class="btn btn-success" href="{{url('ads')}}">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection