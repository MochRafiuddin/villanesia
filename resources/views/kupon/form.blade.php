@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
$name[] = 'kode_kupon';
$name[] = 'nama_kupon';
$name[] = 'deskripsi';
$name[] = 'tanggal_mulai';
$name[] = 'tanggal_selesai';
$name[] = 'kuota_kupon';
$name[] = 'satuan';
$name[] = 'nominal';
$name[] = 'maks_diskon';
$name[] = 'min_transaksi';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} Kupon</h6>
                <form action="{{$url}}" method="post" enctype="multipart/form-data">
                    @csrf                    
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Kode</label>
                            <input type="text" class="form-control @error($name[0]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[0])}}" name="{{$name[0]}}"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Nama</label>
                            <input type="text" class="form-control @error($name[1]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[1])}}" name="{{$name[1]}}"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Deskripsi</label>
                            <textarea class="form-control @error($name[2]) is-invalid @enderror" name="{{$name[2]}}" id="{{$name[2]}}">{{Helper::showData($data,$name[2])}}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Tanggal Mulai</label>                            
                            @php
                                if($data != null){
                                    $hsl2=date('d-m-Y',strtotime(Helper::showDataDate($data,$name[3])));
                                }else{
                                    $hsl2="";
                                }
                            @endphp
                            <input class="form-control pickerdate @error($name[3]) is-invalid @enderror" type="text" name="{{$name[3]}}" id="{{$name[3]}}" value="{{$hsl2}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Tanggal Selesai</label>
                            @php
                                if($data != null){
                                    $hsl3=date('d-m-Y',strtotime(Helper::showDataDate($data,$name[4])));
                                }else{
                                    $hsl3="";
                                }
                            @endphp
                            <input class="form-control pickerdate @error($name[4]) is-invalid @enderror" type="text" name="{{$name[4]}}" id="{{$name[4]}}" value="{{$hsl3}}">                            

                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Kuota Kupon</label>
                            <input type="text" class="form-control @error($name[5]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[5])}}" name="{{$name[5]}}"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Minimum Transaksi</label>
                            <input type="text" class="form-control @error($name[9]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[9])}}" name="{{$name[9]}}"/>
                        </div>
                    </div>                    
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Satuan</label>
                            <select class="form-control js-example-basic-single" name="{{$name[6]}}" id="{{$name[6]}}"
                                     style="width:100%">                           
                                <option value="" selected disabled>Pilih Satuan</option>                                
                                <option value="1" {{(old($name[6]) == 1) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[6],1)}}>
                                        Persen
                                </option>                                
                                <option value="2" {{(old($name[6]) == 2) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[6],2)}}>
                                        Rupiah
                                </option>
                             </select>                
                        </div>
                    </div>                   
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Nominal</label>
                            <input type="text" class="form-control @error($name[7]) is-invalid @enderror"
                            value="{{Helper::showData($data,$name[7])}}" name="{{$name[7]}}"/>
                        </div>
                    </div>                                        
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Maks Diskon</label>
                            <input type="text" class="form-control @error($name[8]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[8])}}" name="{{$name[8]}}" placeholder="Diisi maksimal nilai rupiahnya"/>
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
<script>    
    $(".pickerdate").datepicker( {
            format: "dd-mm-yyyy",
            orientation: "bottom"
        });
</script>
@endpush
