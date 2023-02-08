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
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} Ads</h6>
                <form action="{{$url}}" method="post" enctype="multipart/form-data">
                    @csrf                    
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Nama</label>
                            <input type="text" class="form-control @error($name[0]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[0])}}" name="{{$name[0]}}"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Tipe Konten</label>
                            <select class="form-control @error($name[1]) is-invalid @enderror" name="{{$name[1]}}" id="{{$name[1]}}"
                                     style="width:100%">
                                <option value="" selected disabled>Pilih Konten</option>
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
                            <label for="exampleInputEmail1">List Properti</label>                            
                            <select class="js-example-basic-multiple list_properti" multiple="multiple" style="width:100%" name="list_properti">
                                @if($data)
                                    @foreach($list_properti as $list)
                                        <option value="{{$list->id_properti}}" selected>{{$list->judul}}</option>
                                    @endforeach
                                @endif
                            </select>
                            <input type="hidden" name="list_pro" class="list_pro" value="{{Helper::showData($data,$name[6])}}">
                            <input type="hidden" value="{{Helper::showData($data,$name[2])}}" name="{{$name[2]}}" id="{{$name[2]}}"/>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Redirect Url</label>
                            <input type="text" class="form-control @error($name[2]) is-invalid @enderror"
                            value="{{Helper::showData($data,$name[2])}}" name="{{$name[2]}}" id="{{$name[2]}}" readonly/>
                        </div>
                    </div> -->
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Status</label>
                            <select class="form-control @error($name[3]) is-invalid @enderror" name="{{$name[3]}}" id="{{$name[3]}}" style="width:100%">
                                <option value="" selected disabled>Pilih Status</option>                                
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
                            <select class="form-control @error($name[4]) is-invalid @enderror" name="{{$name[4]}}" id="{{$name[4]}}"
                                     style="width:100%">                                
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
                            <label for="exampleInputEmail1">Konten</label>
                            <!-- <input type="text" class="form-control @error($name[5]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[5])}}" name="{{$name[5]}}"/> -->
                            <input type="file" class="dropify form-control @error($name[5]) is-invalid @enderror" name="{{$name[5]}}"  @if($data) data-default-file="{{asset('upload/ads/'.$data->konten_ads)}}" @endif/>
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
<script src="{{asset('/')}}assets/js/select2.js"></script>
<script>
    $( document ).ready(function() {
        $('.list_properti').select2({
            placeholder: "-Pilih-",
            minimumInputLength: 2,
            multiple: true,
            ajax: {
                url: '{{url("ads/list-properti")}}',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: $.trim(params.term)
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });
        
        $($(".js-example-basic-single").select2("container")).addClass("is-invalid"); 
    });    

    $('.list_properti').change(function() {
        var hasil = $(this).val();
        $(".list_pro").val(hasil);
        $('#redirect_url_ads').val("aptikmamid.ngrok.io/villanesia/public/api/get-property-by-facilities?id_properti="+$(this).val()+"&page=1&order_by=1&id_bahasa=1");
    });
    
</script>
@endpush
