@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
$name[] = 'id_tipe';
$name[] = 'judul';
$name[] = 'isi';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} Faq</h6>
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
                            <label for="exampleInputEmail1">Tipe</label>
                            <select class="form-control js-example-basic-single" name="{{$name[0]}}" id="{{$name[0]}}" disabled=""
                                     style="width:100%">                                
                                <option value="1" {{(old($name[0]) == 1) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[0],1)}}>
                                        Renter
                                </option>                                
                                <option value="2" {{(old($name[0]) == 2) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[0],2)}}>
                                        Hoster
                                </option>
                             </select>                
                        </div>
                    </div>                   
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Judul</label>
                            <input type="text" class="form-control @error($name[1]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[1])}}" name="{{$name[1]}}" readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Isi</label>
                            <textarea class="form-control @error($name[2]) is-invalid @enderror" name="{{$name[2]}}" id="{{$name[2]}}" style="height:130px;" readonly>{{Helper::showData($data,$name[2])}}</textarea>
                        </div>
                    </div>                    
                    <a class="btn btn-success" href="{{url('faq')}}">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="{{asset('/')}}assets/js/dropify.js"></script>
@endpush