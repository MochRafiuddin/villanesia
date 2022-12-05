@extends('template')
@section('content')
<?php 
use App\Traits\Helper;
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <?php
                        if ($data->id_status_booking == 1 || $data->id_status_booking == 2 || $data->id_status_booking == 5) {
                            $status = "<small class='bg-success text-white'>&nbsp;&nbsp;".$data->nama_status_booking."&nbsp;&nbsp;</small>";
                        }else {
                            $status = "<small class='bg-danger text-white'>&nbsp;&nbsp;".$data->nama_status_booking."&nbsp;&nbsp;</small>";
                        }
                    ?>
                    <h6 class="card-title">Reservation #{{$data->kode_booking}} {!!$status!!}</h6>
                    <!-- @if($data->id_status_booking == 3 || $data->id_status_booking == 4)
                        <div class="form-group">
                            <label for="message-text" class="col-form-label"><b>Decline Reason</b></label>
                            {{$data->alasan_reject}}
                        </div>
                    @endif -->
                    <table width="100%">
                    @if($data->id_status_booking == 3 || $data->id_status_booking == 4)
                        <tr>
                            <td style="padding-bottom: 15px;padding-right: 15px;"><b>Decline Reason</b></td>
                            <td colspan="2" style="padding-bottom: 15px;">{{$data->alasan_reject}}</td>
                        </tr>
                    @endif
                        <tr>
                            <td style="padding-bottom: 15px;padding-right: 15px;"><b>Date: </b><br>{{date('F d, Y', strtotime($data->created_date))}}<br> {{date('H:i', strtotime($data->created_date))}}</td>
                            <td style="padding-bottom: 15px;">
                                <p><b>From: </b>{{$data->username}}</p>
                                <p><b>Renter Detail: </b></p>
                                <p><b>Listing Name: </b> {{$data->judul}}</p>
                            </td>
                            <td style="padding-bottom: 15px;"></td>
                            <td style="padding-bottom: 15px;"></td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 15px;"><b>Details </b></td>
                            <td style="padding-bottom: 15px;">
                            <?php
                            if ($data->tipe_booking == 5) {                    
                                $date_mulai = date('d-m-Y', strtotime($data->tanggal_mulai.' '.$data->jam_mulai)).' at '.date('H:i', strtotime($data->tanggal_mulai.' '.$data->jam_mulai));
                                $date_selesai = date('d-m-Y', strtotime($data->tanggal_mulai.' '.$data->jam_selesai)).' at '.date('H:i', strtotime($data->tanggal_mulai.' '.$data->jam_selesai));
                                $durasi = $data->durasi_inap_jam;
                            }else {
                                $date_mulai = date('d-m-Y', strtotime($data->tanggal_mulai));
                                $date_selesai = date('d-m-Y', strtotime($data->tanggal_selesai));
                                $durasi = $data->durasi_inap;
                            }
                            ?>
                                <p> Check In: <b>{{$date_mulai}}</b></p>
                                <p> Check Out: <b>{{$date_selesai}}</b></p>
                            </td>
                            <td style="padding-bottom: 15px;">
                            <?php
                                if ($data->tipe_booking == 1) {
                                    $txtd = "<p> Days: <b>".$data->durasi_inap."</b></p>";
                                }elseif ($data->tipe_booking == 2) {
                                    $txtd = "<p> Nights: <b>".$data->durasi_inap."</b></p>";
                                }elseif ($data->tipe_booking == 3) {
                                    $txtd = "<p> Week: <b>".$data->durasi_inap."</b></p>";
                                }elseif ($data->tipe_booking == 4) {
                                    $txtd = "<p> Mounth: <b>".$data->durasi_inap."</b></p>";
                                }else {
                                    $txtd = "<p> Hours: <b>".$data->durasi_inap_jam."</b></p>";
                                }
                            ?>
                               {!!$txtd!!}
                               <p> Guest: <b>{{$data->tamu_dewasa + $data->tamu_anak + $data->tamu_bayi}}</b></p>
                            </td>
                            <td style="padding-bottom: 15px;" width="100px"></td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 15px;"><b>Payment </b></td>
                            <td style="padding-bottom: 15px;">
                            <?php
                            if ($data->tipe_booking == 1) {
                                $txt = $data->durasi_inap." Day";
                            }elseif ($data->tipe_booking == 2) {
                                if ($harga_satuan->count() > 2) {
                                    $txt = $data->durasi_inap." Nights (with custom period)";
                                }else {
                                    $txt = $data->durasi_inap." Nights";
                                }
                            }elseif ($data->tipe_booking == 3) {
                                if ($data->extra_hari > 0) {
                                    $txt = $data->durasi_inap." Week (with extra day)";
                                }else {
                                    $txt = $data->durasi_inap." Week";
                                }
                            }elseif ($data->tipe_booking == 4) {
                                if ($data->extra_hari > 0) {
                                    $txt = $data->durasi_inap." Mounth (with extra day)";
                                }else {
                                    $txt = $data->durasi_inap." Mounth";
                                }                                
                            }else {
                                $txt = $data->durasi_inap_jam." Hours";
                            }
                            ?>
                                <p>{{$txt}}</p>
                                <p>Cleaning fee</p>
                                <p>Security deposit</p>
                                <p>Taxes {{$data->persen_pajak}}%</p>
                            <?php
                                if ($extra_service->count() > 0) {
                                    echo "<p>Service</p>";
                                }

                                if ($data->total_booking_extra != 0) {
                                    echo "<p>Extra Expenses</p>";
                                }
                                if ($data->total_booking_discount != null) {
                                    echo "<p>Discount</p>";
                                }
                            ?>
                                <hr>
                                <p><b>Total</b></p>
                            </td>                            
                            <td colspan="2" style="padding-bottom: 15px;" class="text-right">
                                <p>Rp.{{Helper::ribuan(ceil($harga_satuan->sum('harga_final')))}}</p>
                                <p>Rp.{{Helper::ribuan(ceil($data->cleaning_fee))}}</p>
                                <p>Rp.{{Helper::ribuan(ceil($data->security_deposit))}}</p>
                                <p>Rp.{{Helper::ribuan(ceil($data->nominal_pajak))}}</p>
                            <?php
                                if ($extra_service->count() > 0) {
                                    echo "<p>Rp.".Helper::ribuan(ceil($data->total_extra_service))."</p>";
                                }
                                if ($data->total_booking_extra != 0) {
                                    echo "<p>Rp.".Helper::ribuan(ceil($data->total_booking_extra))."</p>";
                                }
                                if ($data->total_booking_discount != null) {
                                    echo "<p>- Rp.".Helper::ribuan(ceil($data->total_booking_discount))."</p>";
                                }
                            ?>
                                <hr>
                                <p><b>Rp.{{Helper::ribuan(ceil($data->harga_total))}}</b></p>
                            </td>
                        </tr>
                    </table>                    
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-title">Payment</h6>
                    <table width="100%">
                        <tr>
                            <td style="padding-bottom: 10px;" ><p>{{$txt}}</p></td>
                            <td style="padding-bottom: 10px;" class="text-right"><p>Rp.{{Helper::ribuan(ceil($harga_satuan->sum('harga_final')))}}</p></td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 10px;" ><p>Cleaning fee</p></td>
                            <td style="padding-bottom: 10px;" class="text-right"><p>Rp.{{Helper::ribuan(ceil($data->cleaning_fee))}}</p></td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 10px;" ><p>Security deposit</p></td>
                            <td style="padding-bottom: 10px;" class="text-right"><p>Rp.{{Helper::ribuan(ceil($data->security_deposit))}}</p></td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 10px;" ><p>Taxes {{$data->persen_pajak}}%</p></td>
                            <td style="padding-bottom: 10px;" class="text-right"><p>Rp.{{Helper::ribuan(ceil($data->nominal_pajak))}}</p></td>
                        </tr>
                        @if($extra_service->count() > 0)
                        <tr>
                            <td style="padding-bottom: 10px;" ><p>Service</p></td>
                            <td style="padding-bottom: 10px;" class="text-right"><p>Rp.{{Helper::ribuan(ceil($data->total_extra_service))}}</p></td>
                        </tr>
                        @endif
                        @if($data->total_booking_extra != 0)
                        <tr>
                            <td style="padding-bottom: 10px;" ><p>Extra Expenses</p></td>
                            <td style="padding-bottom: 10px;" class="text-right"><p>Rp.{{Helper::ribuan(ceil($data->total_booking_extra))}}</p></td>
                        </tr>
                        @endif
                        @if($data->total_booking_discount != null)
                        <tr>
                            <td style="padding-bottom: 10px;" ><p>Discount</p></td>
                            <td style="padding-bottom: 10px;" class="text-right"><p>- Rp.{{Helper::ribuan(ceil($data->total_booking_discount))}}</p></td>
                        </tr>
                        @endif
                        <tr>
                            <td style="padding-top: 10px; border-top: 1px solid black;">Total</td>
                            <td style="padding-top: 10px; border-top: 1px solid black;" class="text-right">Rp.{{Helper::ribuan(ceil($data->harga_total))}}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    @if($data->id_status_booking == 1)
                    <button type="button" class="btn btn-success col-12 mb-3 confirm">Confirm Availability</button>                    
                    <button type="button" class="btn btn-secondary col-12 mb-3" data-toggle="modal" data-target="#exampleModal">Decline</button>
                    <button class="btn btn-secondary col-12 mb-3" data-toggle="modal" data-target="#ModalExtra">Extra Expenses</button>
                    <button class="btn btn-secondary col-12 mb-3" data-toggle="modal" data-target="#ModalDiscount">Discount</button>
                    @elseif($data->id_status_booking == 2)                    
                    <button type="button" class="btn btn-danger col-12 mb-3"  data-toggle="modal" data-target="#exampleModal">Decline</button>
                    @endif
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{url('booking/decline/'.$id)}}" method="post">
        @csrf
        <div class="modal-content">
            <!-- <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Decline Reason</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> -->
            <div class="modal-body">
                <div class="form-group">
                    <label for="message-text" class="col-form-label">Decline Reason</label>
                    <textarea class="form-control" name="decline" id="decline" style="height:130px;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Submit</button>
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
            </div>
        </div>
        </form>
    </div>
</div>

<div class="modal fade" id="ModalExtra" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{url('booking/extra/'.$id)}}" method="post">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Extra Expense</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="list-extra">
                @foreach($extra as $ex)
                    <div class="row detail-extra mb-2">
                        <div class="col-5">
                            <input type="text" class="form-control" name="nama_biaya_extra[]" value="{{$ex->nama_biaya_extra}}">
                        </div>
                        <div class="col-5">
                            <input type="text" class="form-control" name="harga[]" value="{{$ex->harga}}">
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-danger delete-extra" id="delete-extra">X</button>
                        </div>
                    </div>
                @endforeach
                </div>
                <div class="form-group">
                    <label for="message-text" class="col-form-label">Expense Name</label>
                    <input type="text" class="form-control exname" name="exname">
                </div>
                <div class="form-group">
                    <label for="message-text" class="col-form-label">Expense Value</label>
                    <input type="text" class="form-control valex" name="valex">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="add-extra">Add More</button>
                <button type="submit" class="btn btn-success">Save Expense</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
        </form>
    </div>
</div>

<div class="modal fade" id="ModalDiscount" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="{{url('booking/discount/'.$id)}}" method="post">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Discount</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="list-discount">
                @foreach($discount as $dis)
                    <div class="row detail-discount mb-2">
                        <div class="col-5">
                            <input type="text" class="form-control" name="nama_biaya_discount[]" value="{{$dis->nama_biaya_discount}}">
                        </div>
                        <div class="col-5">
                            <input type="text" class="form-control" name="harga[]" value="{{$dis->harga}}">
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-danger delete-discount" id="delete-discount">X</button>
                        </div>
                    </div>
                @endforeach
                </div>
                <div class="form-group">
                    <label for="message-text" class="col-form-label">Discount Name</label>
                    <input type="text" class="form-control disname" name="disname">
                </div>
                <div class="form-group">
                    <label for="message-text" class="col-form-label">Discount Value</label>
                    <input type="text" class="form-control valdis" name="valdis">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="add-discount">Add More</button>
                <button type="submit" class="btn btn-success">Save Discount</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
        </form>
    </div>
</div>
@endsection
@push('js')
<script>
    $(document).ready(function () {
        $("#add-extra").click(function(e){
            var nama = $(".exname").val();
            var val = $(".valex").val();
          let html = '<div class="row detail-extra mb-2">\
                        <div class="col-5">\
                            <input type="text" class="form-control" name="nama_biaya_extra[]" value="'+nama+'">\
                        </div>\
                        <div class="col-5">\
                            <input type="text" class="form-control" name="harga[]" value="'+val+'">\
                        </div>\
                        <div class="col-2">\
                            <button type="button" class="btn btn-danger delete-extra" id="delete-extra">X</button>\
                        </div>\
                    </div>';
          $('.list-extra').append(html); 
      });

      $('.list-extra').on('click', 'button.delete-extra', function(events){
          events.preventDefault();
          let idx = $(this).closest('.detail-extra').index();
          $(this).parent().parent().remove();
      });

      $("#add-discount").click(function(e){
            var nama = $(".disname").val();
            var val = $(".valdis").val();
          let html = '<div class="row detail-discount mb-2">\
                        <div class="col-5">\
                            <input type="text" class="form-control" name="nama_biaya_discount[]" value="'+nama+'">\
                        </div>\
                        <div class="col-5">\
                            <input type="text" class="form-control" name="harga[]" value="'+val+'">\
                        </div>\
                        <div class="col-2">\
                            <button type="button" class="btn btn-danger delete-discount" id="delete-discount">X</button>\
                        </div>\
                    </div>';
          $('.list-discount').append(html); 
      });

      $('.list-discount').on('click', 'button.delete-discount', function(events){
          events.preventDefault();
          let idx = $(this).closest('.detail-discount').index();
          $(this).parent().parent().remove();
      });

      $('.confirm').click(function(e){
        $('.confirm').text('Loding....');
        $.ajax({
            url: "{{url('booking/confirm/'.$id)}}",
            type: "POST",            
            contentType: false,
            cache: false,
            processData:false,
            success: function(res){                
                $('.confirm').text('Confirm Availability');
                window.location = "{{url('/booking/detail/'.$id)}}";                
            }
        });
     });

    });
</script>
@endpush