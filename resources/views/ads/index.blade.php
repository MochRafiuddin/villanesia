@extends('template')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h4>Data Ads</h4><br>
                <div class="row mb-4">
                    <div class="col text-right">                        
                        <a href="{{url('ads/create')}}" class="btn btn-info">Tambah</a>                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table w-100">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <!-- <th>Konten</th> -->
                                        <th>Tipe Konten</th>
                                        <!-- <th>Redirect Url</th> -->
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="modal fade" id="ajaxModelexa" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Setting Ads</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="post" id='postForm'>
        <div class="modal-body">
            <div class="row">
                <div class="form-group col">
                    <label for="exampleInputEmail1">Tanggal Mulai</label>
                    <input class="form-control pickerdate" type="text" id='tgl_mulai' name="tgl_mulai">
                    <input type="hidden" id='id' name="id">
                </div>
            </div>
            <div class="row">
                <div class="form-group col">
                    <label for="exampleInputEmail1">Tanggal Selesai</label>
                    <input class="form-control pickerdate" type="text" id='tgl_selesai' name="tgl_selesai">
                </div>
            </div>
            <div class="row">
                <div class="form-group col">
                    <label for="exampleInputEmail1">Jam Mulai</label>
                    <input type="text" class="form-control jam_mulai" id='endTime' name="jam_mulai" required>                          
                </div>
            </div>
            <div class="row">
                <div class="form-group col">
                    <label for="exampleInputEmail1">Jam Selesai</label>
                    <input type="text" class="form-control jam_selesai" id='endTime' name="jam_selesai" required>
                </div>
            </div>
        </div>      
        <div class="modal-footer">
            <div class="form-group">           
                <button type="button" class="btn btn-success" id="savedata">Simpan</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Keluar</button>
            </div>
        </div>
      </form>
    </div>
  </div>
</div>    
    <!-- content-wrapper ends -->
    <!-- partial:partials/_footer.html -->
    @include("partial.footer")
    <!-- partial -->
</div>
@endsection
@push('js')
<script>
    $(document).ready(function () {
        read_data();

        function read_data() {
            $('table').DataTable({
                processing: true,
                serverSide: true,

                "scrollX": true,
                ajax: {
                    url: '{{ url("ads/data") }}',
                },
                rowReorder: {
                    selector: 'td:nth-child(1)'
                },

                responsive: true,
                columns: [{
                        "data": 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '4%',
                        className: 'text-center'
                    },
                    {
                        data: 'nama_ads',
                        name: 'nama_ads',                        
                    },                    
                    {
                        data: 'tipe_konten_ads',
                        name: 'tipe_konten_ads',                        
                    },
                    {
                        data: 'status',
                        name: 'status',                        
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        }
         $(document).on('click','.delete',function(e){
            e.preventDefault();
            Swal.fire({
                title: 'Kamu Yakin?',
                text: "Menghapus data ini",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak'
                }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = $(this).attr('href');
                }
                })
        })

        $('body').on('click', '.editPost', function () {                            
            var id = $(this).data('id');                        
            $.ajax({
                    url: '{{ url("ads/setting") }}',
                    type: 'get',
                    data: { id:id},
                    dataType: 'json',
                success: function (data) {
                    if (data) {    
                        $('#ajaxModelexa').modal('show');
                        $('#id').val(id);
                        $('#tgl_mulai').val(data['tgl_mulai']);                        
                        $('#tgl_selesai').val(data['tgl_selesai']);                        
                        $('.jam_mulai').val(data['jam_mulai']);                        
                        $('.jam_selesai').val(data['jam_selesai']);                        
                    }        
                },
            });

        });

        $('#savedata').click(function (e) {
            e.preventDefault();
            $(this).html('Sending..');
        
            $.ajax({
                data: $('#postForm').serialize(),
                url: '{{ url("ads/setting-save") }}',
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#postForm').trigger("reset");
                    $('#ajaxModelexa').modal('hide');
                    $("table").DataTable().destroy();
                    read_data();
                    $('#savedata').html('Simpan');
                },
                error: function (data) {
                    console.log('Error:', data);
                    $('#savedata').html('Simpan');
                }
            });
        });

    });

    $('input[id$="endTime"]').inputmask("hh:mm", {
        placeholder: "HH:MM", 
        insertMode: false, 
        showMaskOnHover: false,        
      }
    );
    $(".pickerdate").datepicker( {
        format: "dd-mm-yyyy",
        orientation: "bottom"
    });
    
</script>
@endpush