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
                            <label for="exampleInputEmail1">Tampil pada</label>
                            <select class="form-control js-example-basic-single" name="{{$name[1]}}" id="{{$name[1]}}" style="width:100%">
                                <option value="1" {{(old($name[1]) == 1) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[1],1)}}>
                                    Splash Screen
                                </option>
                                <option value="2" {{(old($name[1]) == 2) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[1],2)}}>
                                    image slide
                                </option>
                             </select>
                        </div>
                    </div>
                    @if($data->id_ss != 1 && $data->id_ss != 3 && $data->id_ss != 4)
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Detail text</label>
                            <input type="text" class="form-control @error($name[2]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[2])}}" name="{{$name[2]}}" />
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
                            <label for="exampleInputEmail1">Gambar <b id="desk">{{$desk}}</b></label>                            
                            <input type="file" class="dropify form-control" name="{{$name[3]}}" @if($data) data-default-file="{{asset('upload/splash_slide/'.$data->gambar)}}" @endif/>                            
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
<script>
    $('#tipe').on('change',function(){
        if (this.value == 1) {
            $('#desk').html('(1024x2048 / ratio 1:2)');
        }else{
            $('#desk').html('(425x425 / ratio 1:1)');
        }
    });
</script>
@endpush
