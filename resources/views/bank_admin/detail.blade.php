@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
$name[] = 'id_bank';
$name[] = 'branch';
$name[] = 'acc_name';
$name[] = 'acc_number';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} Bank Admin</h6>
                <form action="" method="post" enctype="multipart/form-data">
                    @csrf                    
                    <div class="row">
                        <div class="form-group col">
                            <label for="">Bank</label>
                             <select class="form-control js-example-basic-single" name="{{$name[0]}}" id="{{$name[0]}}"
                                     style="width:100%" data-maximum-selection-length="10" disabled="">
                                 <option value="">Pilih Bank</option>
                                 @foreach($bank as $dat)
                                     <!-- <option value="{{$data->id_bank}}">{{$data->nama_bank}}</option> -->
                                     <option value="<?= $dat->{$name[0]} ?>"
                                        {{(old($name[0]) == $dat->{$name[0]}) ? 'selected' : ''}}
                                        {{Helper::showDataSelected($data,$name[0],$dat->{$name[0]})}}>
                                        {{$dat->nama_bank}}
                                     </option>
                                 @endforeach
                             </select>                
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Branch</label>
                            <input type="text" class="form-control @error($name[1]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[1])}}" name="{{$name[1]}}" readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Acc Name</label>
                            <input type="text" class="form-control @error($name[2]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[2])}}" name="{{$name[2]}}" readonly/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Acc Number</label>
                            <input type="text" class="form-control @error($name[3]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[3])}}" name="{{$name[3]}}" readonly/>
                        </div>
                    </div>
                    <a class="btn btn-success" href="{{url('bank-admin')}}">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="{{asset('/')}}assets/js/select2.js"></script>
@error($name[0]) 
<script>
    $($(".js-example-basic-single").select2("container")).addClass("is-invalid"); 
</script>
@enderror
<script>
    $(document).ready(function () {
      $('.js-example-basic-single').select2({
          placeholder: "Pilih Bank",
          // maximumSelectionLength: 10
      });
    });    
</script>
@endpush