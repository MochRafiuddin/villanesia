@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
$name[] = 'nama_pro_kendaraan';
$name[] = 'show';
$name[] = 'detail_harga';
$name[] = 'text_to_wa';
$name[] = 'gambar';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} Promosi Kendaraan</h6>
                <form action="" method="post" enctype="multipart/form-data">
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
                                value="{{Helper::showData($data,$name[0])}}" name="{{$name[0]}}" readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Tampil</label>                            
                            <div class="form-check">
                              <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="{{$name[1]}}" id="{{$name[1]}}" value="0" {{Helper::showDataChecked($data,$name[1],0)}} disabled="">
                                Tidak
                              </label>
                            </div>                                                    
                            <div class="form-check">
                              <label class="form-check-label">
                                <input type="radio" class="form-check-input" name="{{$name[1]}}" id="{{$name[1]}}" value="1" {{Helper::showDataChecked($data,$name[1],1)}} disabled="">
                                Tampil
                              </label>
                            </div>                          
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Detail Harga</label>
                            <input type="text" class="form-control @error($name[2]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[2])}}" name="{{$name[2]}}" readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Text WA</label>
                            <input type="text" class="form-control @error($name[3]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[3])}}" name="{{$name[3]}}" readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Gambar <b>1080x1080 / ratio 1:1</b></label><br>
                            <!-- <input type="text" class="form-control @error($name[1]) is-invalid @enderror" value="{{Helper::showData($data,$name[1])}}" name="{{$name[1]}}" /> -->
                            <img class="rounded" width="40%" src="{{asset('upload/promosi_kendaraan/'.$data->gambar)}}" alt="{{$data->nama_promosi_kendaraan}}">
                        </div>                        
                    </div>
                    <a class="btn btn-success" href="{{url('promosi-kendaraan')}}">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="{{asset('/')}}assets/js/dropify.js"></script>
@endpush