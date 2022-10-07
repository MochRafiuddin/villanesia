@extends('template')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h4>Data Tipe Properti</h4><br>
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="">Bahasa</label>
                        <select class="form-control js-example-basic-single" name="id_bahasa" id="id_bahasa" style="width:100%">                            
                            @foreach($bahasa as $data)
                                <option value="{{$data->id_bahasa}}" {{(1 == $data->is_default) ? 'selected' : ''}}>{{$data->nama_bahasa}}</option>
                            @endforeach
                        </select>                  
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col text-right">                        
                        <a href="{{url('tipe-properti/create')}}" class="btn btn-info">Tambah</a>                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table w-100">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Bahasa</th>
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
        <h5 class="modal-title" id="exampleModalLongTitle">Bahasa</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">        
        <input type="hidden" name="id" id="id">        
        <input type="hidden" name="id_ref" id="id_ref">        
        @foreach($bahasa as $bhs)
            <button type="button" class="btn btn-secondary" onclick="myFunction({{$bhs->id_bahasa}})"><i class="flag-icon {{$bhs->logo}}"></i> {{$bhs->nama_bahasa}}</button>
        @endforeach
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

        function read_data() {
            let bahasa = $('#id_bahasa').val();
            $('table').DataTable({
                processing: true,
                serverSide: true,

                "scrollX": true,
                ajax: {
                    url: '{{ url("tipe-properti/data") }}',
                    type: 'GET',
                    data: {
                        bahasa : bahasa,
                    }
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
                        data: 'bahasa',
                        name: 'bahasa',                        
                    },
                    {
                        data: 'nama_tipe_properti',
                        name: 'nama_tipe_properti',                        
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
            var id_ref = $(this).data('id_ref');
            $('#ajaxModelexa').modal('show');
            $('#id').val(id);
            $('#id_ref').val(id_ref);
        });

        $('#id_bahasa').on('change',function(){            
            $('table').DataTable().destroy();            
            read_data();            
        });
    });

    function myFunction(kode) {
        var id = $('#id').val();        
        var id_ref = $('#id_ref').val();
        $.ajax({
            url: '{{ url("tipe-properti/bahasa") }}',
            type: 'get',
            data: { id:id, id_ref:id_ref, kode:kode},
            dataType: 'json',
          success: function (data) {            
            if (data == 'tambah') {
                window.location = "{{url('tipe-properti/create-bahasa')}}/"+id+"/"+kode;
            }else{
                window.location = "{{url('tipe-properti/show')}}/"+data;
            }         
          },
      });
    }
</script>
@endpush