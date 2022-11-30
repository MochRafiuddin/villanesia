@extends('template')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="card-group col-12">
                <div class="card text-center card-shadow-primary border rounded-0">
                    <div class="card-body">                                                    
                        <h3>Welcome back, host </h3>                        
                    </div>
                </div>
            </div>
            <div class="card-group col-12">
                <div class="card text-center card-shadow-primary border rounded-0">
                    <div class="card-body">                                                                            
                        <h5 class="font-weight-bold">Listings</h6>
                        <h2 class="mb-2">{{$pro}}</h2>
                        <a href="{{url('/properti-add')}}" class="mb-0">Add New</a>
                    </div>
                </div>
                <div class="card text-center card-shadow-primary border rounded-0">
                    <div class="card-body">                                                                            
                        <h5 class="font-weight-bold">Reservations</h6>
                        <h2 class="mb-2">{{$book}}</h2>
                        <a href="{{url('/list-booking')}}" class="mb-0">Manage</a>                                                    
                    </div>
                </div>
            </div>            
        </div> 
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h4>Upcoming Reservations</h4>                            
                            <small><a href="{{url('/list-booking')}}">View All <span class="mdi mdi-arrow-right-bold-circle-outline"></span></a></small>
                        </div>
                        <br><br>                            
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table w-100 tabel-booking">
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
        </div>       
    </div>
    <!-- content-wrapper ends -->
    <!-- partial:partials/_footer.html -->
    <footer class="footer">
        <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2022 <a
                    href="https://www.aptikma.co.id" target="_blank">Aptikma.co.id</a>. All rights
                reserved.</span>
            <!-- <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i
                    class="mdi mdi-heart text-danger"></i></span> -->
        </div>
    </footer>
    <!-- partial -->
</div>
@endsection
@push('js')
<script>
    $(document).ready(function () {
        read_data();

        function read_data() {            
            $('.tabel-booking').DataTable({
                processing: true,
                serverSide: true,

                "scrollX": true,
                ajax: {
                    url: '{{ url("dashboard/data-booking") }}',
                    type: 'GET',                    
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
                        name: 'alamat',                        
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
    });
</script>
@endpush