@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
$name[] = 'nama_ads';
$name[] = 'tipe_konten_ads';
$name[] = 'redirect_url_ads';
$name[] = 'status';
$name[] = 'posisi';
$name[] = 'konten_ads';
$name[] = 'list_properti';
$name[] = 'tipe_redirect_url';
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
                                <!-- <option value="2" {{(old($name[1]) == 2) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[1],2)}}>
                                        video
                                </option> -->
                             </select>                
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Tipe Redirect Url</label>
                            <select class="form-control @error($name[7]) is-invalid @enderror" name="{{$name[7]}}" id="{{$name[7]}}" style="width:100%" disabled="">
                                <option value="0" selected disabled>Pilih Tipe</option>
                                <option value="1" {{(old($name[7]) == 1) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[7],1)}}>
                                        Dalam Aplikasi
                                </option>                                
                                <option value="2" {{(old($name[7]) == 2) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[7],2)}}>
                                        Luar Aplikasi
                                </option>
                             </select>                
                        </div>
                    </div>
                    @if($data->tipe_redirect_url == 2)
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Redirect Url</label>
                            <input type="text" class="form-control @error($name[2]) is-invalid @enderror"
                            value="{{Helper::showData($data,$name[2])}}" name="{{$name[2]}}" readonly/>
                        </div>
                    </div>
                    @else
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">List Properti</label>                            
                            <select class="js-example-basic-multiple list_properti" multiple="multiple" style="width:100%" name="list_properti" readonly>
                                    @foreach($list_properti as $list)
                                        <option value="{{$list->id_properti}}" selected>{{$list->judul}}</option>
                                    @endforeach
                            </select>                            
                        </div>
                    </div>
                    @endif
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
                                <!-- <option value="2" {{(old($name[3]) == 2) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[3],2)}}>
                                        complete
                                </option>
                                <option value="3" {{(old($name[3]) == 3) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[3],3)}}>
                                        banned
                                </option> -->
                             </select>
                        </div>
                    </div>                    
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Posisi</label>
                            <select class="form-control @error($name[4]) is-invalid @enderror" name="{{$name[4]}}" id="{{$name[4]}}"style="width:100%" disabled>
                                <option value="1" {{(old($name[4]) == 1) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[4],1)}}>
                                        Property
                                </option>                                
                                <option value="2" {{(old($name[4]) == 2) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[4],2)}}>
                                        Promotion Transportation
                                </option>
                             </select>
                        </div>
                    </div>                    
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Konten</label><br>
                            <!-- <input type="file" class="dropify form-control @error($name[5]) is-invalid @enderror" name="{{$name[5]}}"  @if($data) data-default-file="{{asset('upload/ads/'.$data->konten_ads)}}" @endif/> -->
                            <!-- {{asset('upload/promosi_kendaraan/1663228135032.jpg')}} -->
                            <img class="rounded" src="{{asset('upload/ads/'.$data->konten_ads)}}" alt="{{$data->nama_ads}}">
                        </div>
                    </div>
                    <a class="btn btn-success" href="{{url('ads')}}">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="{{asset('/')}}assets/js/select2.js"></script>
<script>
    $( document ).ready(function() {        
        $(".js-example-basic-multiple").select2({
            disabled: true
        }); 
    });
</script>
@endpush