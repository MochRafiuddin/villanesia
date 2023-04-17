@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
$name[] = 'nama_ss';
$name[] = 'tipe';
$name[] = 'detail_text';
$name[] = 'gambar';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} Splash Slide</h6>
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
                            <label for="exampleInputEmail1">Tipe</label>
                            <?php 
                                if($data->tipe == 1){
                                    $ads = "Splash Screen";
                                }else{
                                    $ads = "Image Slide";
                                }
                            ?>
                            <input type="text" class="form-control @error($name[1]) is-invalid @enderror"
                                value="{{$ads}}" name="{{$name[1]}}" readonly/>
                        </div>
                    </div>
                    @if($data->id_ss != 3 && $data->id_ss != 4)
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Detail</label>
                            <input type="text" class="form-control @error($name[2]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[2])}}" name="{{$name[2]}}" readonly/>
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="form-group col">
                            @php
                                if($data->tipe == 1){
                                    $desk='(1024x2048 / ratio 1:2)';
                                }else{
                                    $desk='(425x425 / ratio 1:1)';
                                }
                            @endphp
                            <label for="exampleInputEmail1">Gambar <b>{{$desk}}</b></label><br>                            
                            <img class="rounded" width="40%" src="{{asset('upload/splash_slide/'.$data->gambar)}}" alt="{{$data->nama_ss}}">
                        </div>                        
                    </div>
                    <a class="btn btn-success" href="{{url('splash-slide')}}">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection