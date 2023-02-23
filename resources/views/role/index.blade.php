@extends('template')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h4>Data Role</h4><br>
                <div class="row mb-4">
                    <div class="col text-right">                        
                        <a href="{{url('role/create')}}" class="btn btn-info">Tambah</a>                        
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
        <h5 class="modal-title" id="exampleModalLongTitle">Menu</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">        
        <input type="hidden" name="id_role" id="id_role">
        <div class="row">
            @foreach($menu as $key)
                <div class="col-6">
                    <div class="form-check form-check-flat form-check-primary">
                      <label class="form-check-label">
                        <input type="checkbox" class="form-check-input type_checkbox" name="menu[]" id="menu_list" value="{{$key->id_menu}}"> {{$key->nama_menu}}
                      </label>
                    </div>
                </div>
            @endforeach
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="btn_simpan">Simpan</button>
        <button type="button" class="btn btn-light" data-dismiss="modal" aria-label="Close">Keluar</button>
      </div>
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

    });

        function read_data() {
            $('.table').DataTable({
                processing: true,
                serverSide: true,

                "scrollX": true,
                ajax: {
                    url: '{{ url("role/data") }}',
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
                        data: 'nama_role',
                        name: 'nama_role',                        
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

$('body').on('click', '.editPost', function () {                
    var id_role = $(this).data('id');
        $.ajax({
            url: '{{ url("role/get_menu") }}',
            type: 'get',
            data: { id_role:id_role},
            dataType: 'json',
          success: function (res) {            
            $('#ajaxModelexa').modal('show');
            $('#id_role').val(id_role);
            res.data.forEach(function(dataExcel){
                $('input.type_checkbox[value="'+dataExcel.id_menu+'"]').prop('checked', true);
            });
          },
        });
});

$('#ajaxModelexa').on('hidden.bs.modal', function (e) {
    $('input.type_checkbox').prop("checked", "");
})

$('#btn_simpan').on('click',function(){            
    var id_role = $('#id_role').val();            
    var testval = [];
    $('#menu_list:checked').each(function() {
    testval.push($(this).val());
    });
    // console.log(testval);
        $.ajax({
            url: '{{ url("role/save_menu") }}',
            type: 'post',
            data: { id_role:id_role, menu:testval},
            dataType: 'json',
          success: function (data) {            
            $('#ajaxModelexa').modal('hide');
            $('.table').DataTable().destroy();            
            read_data();
          },
        }); 
});
</script>
@endpush