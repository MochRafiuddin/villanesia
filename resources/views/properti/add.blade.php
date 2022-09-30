@extends('template')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} Properti</h6>                
                    @csrf                    
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Tipe Booking</label>
                            <select class="form-control js-example-basic-single" name="tipe_booking" id="tipe_booking" style="width:100%">                           
                                <option value="" selected disabled>Pilih Tipe</option>
                                @foreach($tipe as $data)
                                    <option value="{{$data->id_tipe_booking}}">{{$data->nama_tipe_booking}}</option>
                                @endforeach
                             </select>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script>
    $("#tipe_booking").on('change', function(){    
        window.location = "{{url('/properti-add')}}/"+$(this).val();
    });
</script>
@endpush
