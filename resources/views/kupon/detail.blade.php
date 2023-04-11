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
$name[] = 'kuota_terpakai';
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
                <form action="" method="post" enctype="multipart/form-data">
                    @csrf                    
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Kode</label>
                            <input type="text" class="form-control @error($name[0]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[0])}}" name="{{$name[0]}}" readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Nama</label>
                            <input type="text" class="form-control @error($name[1]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[1])}}" name="{{$name[1]}}" readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Deskripsi</label>
                            <textarea class="form-control @error($name[2]) is-invalid @enderror" name="{{$name[2]}}" id="{{$name[2]}}" readonly>{{Helper::showData($data,$name[2])}}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Tanggal Mulai</label>
                            <input class="form-control pickerdate @error($name[3]) is-invalid @enderror" type="text" name="{{$name[3]}}" id="{{$name[3]}}" value="{{Helper::showData($data,$name[3])}}" readonly/>                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Tanggal Selesai</label>
                            <input class="form-control pickerdate @error($name[4]) is-invalid @enderror" type="text" name="{{$name[4]}}" id="{{$name[4]}}" value="{{Helper::showData($data,$name[4])}}" readonly/>                            

                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Kuota Kupon</label>
                            <input type="text" class="form-control @error($name[5]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[5])}}" name="{{$name[5]}}" readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Minimum Transaksi</label>
                            <input type="text" class="form-control @error($name[10]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[10])}}" name="{{$name[10]}}" readonly/>
                        </div>
                    </div>                    
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Kuota Tersisa</label>
                            <input type="text" class="form-control @error($name[6]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[6])}}" name="{{$name[6]}}" readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Satuan</label>
                            <select class="form-control js-example-basic-single" name="{{$name[7]}}" id="{{$name[7]}}" disabled=""
                                     style="width:100%">                                
                                <option value="1" {{(old($name[7]) == 1) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[7],1)}}>
                                        Persen
                                </option>                                
                                <option value="2" {{(old($name[7]) == 2) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[7],2)}}>
                                        Rupiah
                                </option>
                             </select>                
                        </div>
                    </div>                   
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Nominal</label>
                            <input type="text" class="form-control @error($name[8]) is-invalid @enderror"
                            value="{{Helper::showData($data,$name[8])}}" name="{{$name[8]}}" readonly/>
                        </div>
                    </div>                                        
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Maks Diskon</label>
                            <input type="text" class="form-control @error($name[9]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[9])}}" name="{{$name[9]}}" readonly/>
                        </div>
                    </div>
                    <a class="btn btn-success" href="{{url('kupon')}}">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script>    
    $(".pickerdate").datepicker( {
        format: "mm",
        startView: "months", 
        minViewMode: "months"
    });
</script>
@endpush