@extends('template')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h4>Data Booking</h4><br>
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="">Status</label>
                        <select class="form-control js-example-basic-single" name="status" id="status" style="width:100%">
                                <option value="0">All</option>
                            @foreach($status as $data)
                                <option value="{{$data->id_status_booking}}">
                                    @if($data->id_status_booking == 1)
                                        New
                                    @else  
                                        {{$data->nama_status_booking}}
                                    @endif
                                </option>
                            @endforeach
                        </select>                  
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table w-100">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Address</th>
                                        <th>Check-in</th>
                                        <th>Check-out</th>
                                        <th>Guests</th>
                                        <th>Pets</th>
                                        <th>Subtotal</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
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

        $('body').on('click', '.editPost', function () {                
            var id = $(this).data('id');
            var id_ref = $(this).data('id_ref');
            $('#ajaxModelexa').modal('show');
            $('#id').val(id);
            $('#id_ref').val(id_ref);
            
        });
                
    });

    function read_data() {
        let status = $('#status').val();
        $('.table').DataTable({
            processing: true,
            serverSide: true,

            "scrollX": true,
            ajax: {
                url: '{{ url("booking/data") }}',
                type: 'GET',    
                data: {
                    status : status,
                }                
            },
            rowReorder: {
                selector: 'td:nth-child(1)'
            },

            responsive: true,
            columns: [{
                    data: 'kode_booking',
                    name: 'kode_booking',                        
                },
                {
                    data: 'status',
                    name: 'status',                        
                },
                {
                    data: 'date',
                    name: 'date',                        
                },
                {
                    data: 'alamat',
                    name: 'm_properti.judul',                        
                },
                {
                    data: 'in',
                    name: 'in',                        
                },
                {
                    data: 'out',
                    name: 'out',                        
                },
                {
                    data: 'tamu',
                    name: 'tamu',                        
                },
                {
                    data: 'pet',
                    name: 'pet',                        
                },
                {
                    data: 'harga_total',
                    name: 'harga_total',                        
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

    $('#status').on('change',function(){            
        $('.table').DataTable().destroy();            
        read_data();            
    });
</script>
@endpush