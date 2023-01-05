@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
$name[] = 'nama_depan';
$name[] = 'nama_belakang';
$name[] = 'alamat';
$name[] = 'nama_kota';
$name[] = 'nama_provinsi';
$name[] = 'kode_pos';
$name[] = 'tentang';
$name[] = 'jenis_kelamin';
$name[] = 'no_telfon_lain';
$name[] = 'nama_foto';
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} Profile</h6>
                <div class="col-12">
                    <div id="msg">
                        
                    </div>
                </div>
                <form action="{{$url}}" method="post" enctype="multipart/form-data">
                    @csrf                    
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Nama Depan</label>
                            <input type="text" class="form-control @error($name[0]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[0])}}" name="{{$name[0]}}" />
                        </div>
                    </div>                    
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Nama Belakang</label>
                            <input type="text" class="form-control @error($name[1]) is-invalid @enderror" value="{{Helper::showData($data,$name[1])}}" name="{{$name[1]}}" />
                        </div>
                    </div>                    
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">alamat</label>
                            <input type="text" class="form-control @error($name[2]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[2])}}" name="{{$name[2]}}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Nama Kota</label>
                            <input type="text" class="form-control @error($name[3]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[3])}}" name="{{$name[3]}}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Nama Provinsi</label>
                            <input type="text" class="form-control @error($name[4]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[4])}}" name="{{$name[4]}}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Kode Pos</label>
                            <input type="text" class="form-control @error($name[5]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[5])}}" name="{{$name[5]}}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">tentang</label>
                            <input type="text" class="form-control @error($name[6]) is-invalid @enderror"
                                value="{{Helper::showData($data,$name[6])}}" name="{{$name[6]}}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Jenis Kelamin</label>                            
                            <select class="form-control js-example-basic-single @error($name[7]) invalid @enderror" name="{{$name[7]}}" id="">
                                <option value="1" {{(old($name[7]) == 1) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[7],1)}}>Laki-laki</option>
                                <option value="2" {{(old($name[7]) == 2) ? 'selected' : ''}} {{Helper::showDataSelected($data,$name[7],2)}}>Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label for="exampleInputEmail1">Foto Profile</label>
                            <input type="file" class="form-control" name="{{$name[9]}}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label style="font-size: 0.875rem;line-height: 1;vertical-align: top;margin-bottom: .5rem;">No Telfon Lain</label>
                        </div>
                        <div class="col-12 telfon_lain">
                        @if($data == null)
                            <div class="input-group mb-3">
                                <input id="btn-input" type="text" class="form-control"/>
                                <div class="input-group-append">
                                    <button class="btn btn-warning btn-sm send-button" id="btn-chat">Tambah</button>
                                </div>
                            </div>
                        @else
                            <?php
                                $var = json_decode($data->no_telfon_lain, TRUE);
                            ?>
                            @foreach($var as $key => $t)
                                <div class="input-group mb-3">
                                    <input id="btn-input" type="text" class="form-control" name="notelpon[]" value="{{$t}}"/>
                                    <div class="input-group-append">
                                        @if($key == 0)
                                        <button class="btn btn-warning btn-sm send-button tambah" type="button">Tambah</button>
                                        @else
                                        <button class="btn btn-warning btn-sm send-button hapus" type="button">hapus</button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        </div>
                    </div><br>
                    <input type="submit" class="btn btn-success" value="Simpan" />
                    <input type="button" class="btn btn-info ubah" value="Ubah Password" />
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ajaxModelexa" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Ubah Password</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="col-12">
        <div id="msg1">
            
        </div>
      </div>
      <form id="postForm" name="postForm">
      <div class="modal-body">
        <div class="form-group col">
            <label for="exampleInputEmail1">Password Lama</label>
            <input type="text" class="form-control" name="password_lama" />
            <input type="hidden" class="form-control" name="id_user" id="id_user"/>
        </div>
        <div class="form-group col">
            <label for="exampleInputEmail1">Password</label>
            <input type="text" class="form-control" name="password_baru" />
            <input type="hidden" class="form-control" name="id_user" id="id_user"/>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
        <button type="button" class="btn btn-primary" id="savedata">Simpan</button>
      </div>
      </form>
    </div>
  </div>
</div>

@endsection
@push('js')
<script>
    $('.tambah').click(function(){
        var html ='<div class="input-group mb-3">\
                    <input id="btn-input" type="text" class="form-control" name="notelpon[]" value=""/>\
                    <div class="input-group-append">\
                        <button class="btn btn-warning btn-sm send-button hapus" type="button" onclick="hapus(this);">hapus</button>\
                    </div>\
                </div>';
      $(".telfon_lain").append(html);
    });
    $('.hapus').click(function(e){
        e.preventDefault();
        $(this).parent().parent().remove();
    });
    function hapus(o){        
        $(o).parent().parent().remove();
    }

    $('body').on('click', '.ubah', function () {    
        $('#ajaxModelexa').modal('show');        
    });

    $('#savedata').click(function (e) {
        $.ajax({
            data: $('#postForm').serialize(),
            url: "{{ url('user/ubah-pass') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                if (data.status == 0) {
                    $("#msg1").html("<div class='alert alert-danger alert-block'>\
                    <button type='button' class='close' data-dismiss='alert'>&times;</button>"+data.msg+"</div>");

                }else{
                    $('#postForm').trigger("reset");
                    $('#ajaxModelexa').modal('hide');

                    $("#msg").html("<div class='alert alert-success alert-block'>\
                    <button type='button' class='close' data-dismiss='alert'>&times;</button>"+data.msg+"</div>");
                }                
            },
            error: function (data) {
                console.log('Error:', data);
                $('#savedata').html('Simpan');
            }
        });
    });
</script>
@endpush