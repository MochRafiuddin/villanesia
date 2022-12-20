@extends('template')
@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <form action="" id="form">
                    <div id="wizard">
                        <!-- SECTION 1 -->
                        <h4>Information</h4>
                        <section>
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="id_jenis_tempat">Jenis Tempat</label>
                                    <input type="hidden" name="id_bahasa" value="{{$bahasa->id_bahasa}}">
                                    <input type="hidden" name="id_properti" id="id_properti" value="">
                                    <input type="hidden" name="id_ref_bahasa" id="id_ref_bahasa" value="">
                                    <input type="hidden" name="deleted" id="deleted" value="">
                                    <input type="hidden" name="id_tipe_booking" value="{{$tipeId->id_tipe_booking}}">
                                    <select class="form-control js-example-basic-single" name="id_jenis_tempat" id="id_jenis_tempat">
                                        <option value="" selected disabled>Pilih</option>
                                        @foreach($jenis as $data)
                                            <option value="{{$data->id_jenis_tempat}}">{{$data->nama_jenis_tempat}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            
                                <div class="form-group col-12">
                                    <label for="judul">Judul</label>
                                    <input type="text" class="form-control" name="judul" id="judul"/>
                                </div>
                            
                                <div class="form-group col-12">
                                    <label for="deskripsi">Deskripsi</label>
                                    <textarea class="form-control" name="deskripsi" id="deskripsi" style="height:130px;"></textarea>
                                </div>
                            
                                <div class="form-group col-12">
                                    <label for="perlu_diketahui">Perlu Tahu</label>
                                    <textarea class="form-control" name="perlu_diketahui" id="perlu_diketahui" style="height:130px;"></textarea>
                                </div>
                            
                                <div class="form-group col-6">
                                    <label for="id_tipe_properti">Tipe Properti</label>
                                    <select class="form-control js-example-basic-single" name="id_tipe_properti" id="id_tipe_properti" style="width:100%">                           
                                        <option value="" selected disabled>Pilih</option>
                                        @foreach($tipe as $data)
                                            <option value="{{$data->id_tipe_properti}}">{{$data->nama_tipe_properti}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label for="jumlah_kamar_tidur">Jumlah Kamar Tidur</label>
                                    <input type="text" class="form-control" name="jumlah_kamar_tidur" id="jumlah_kamar_tidur"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="jumlah_tamu">Jumlah Tamu</label>
                                    <input type="text" class="form-control" name="jumlah_tamu" id="jumlah_tamu"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="jumlah_tempat_tidur">Jumlah Tempat Tidur</label>
                                    <input type="text" class="form-control" name="jumlah_tempat_tidur"/>
                                </div>
                            
                                <div class="form-group col-6">
                                    <label for="jumlah_kamar_mandi">Jumlah Kamar Mandi</label>
                                    <input type="text" class="form-control" name="jumlah_kamar_mandi" id="jumlah_kamar_mandi"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="ukuran">Ukuran</label>
                                    <input type="text" class="form-control" name="ukuran"/>
                                </div>
                            
                                <div class="form-group col-6">
                                    <label for="satuan_ukuran">Satuan</label>
                                    <input type="text" class="form-control" name="satuan_ukuran"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="nama_properti">Nama Villa</label>
                                    <input type="text" class="form-control" name="nama_properti" id="nama_properti"/>
                                </div>
                            
                                <div class="form-group col-6">
                                    <label for="sarapan">Sarapan</label>
                                    <input type="text" class="form-control" name="sarapan"/>
                                </div>
                            </div>
                        </section> <!-- SECTION 2 -->
                        <h4>Harga</h4>
                        <section>
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="">Harga {{$tipeId->nama_tipe_booking}}</label>
                                    <input type="text" class="form-control" name="harga_tampil" id="harga_tampil"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Setelah Harga Label</label>
                                    <input type="text" class="form-control" name="setelah_label_harga" id="setelah_label_harga"/>
                                </div>
                                @if($id != 3 && $id != 4)
                                <div class="form-group col-6">
                                    <label for="">Harga Weekend</label>
                                    <input type="text" class="form-control" name="harga_weekend" id="harga_weekend"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Penerapan Harga Weekend</label>
                                    <select class="form-control js-example-basic-single" name="penerapan_harga_weekend" id="penerapan_harga_weekend" style="width:100%">                           
                                        <option value="" selected disabled>Pilih</option>                                        
                                        @foreach($pekan as $data)
                                            <option value="{{$data->id_akhir_pekan}}">{{$data->detail_akhir_pekan}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            </div>
                            @if($id != 3 && $id != 4 && $id != 5)
                            <div class="row">
                                <div class="form-group col">
                                    <H4>Harga Jangka Panjang</H4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="">Mingguan - 7+ hari</label>
                                    <input type="text" class="form-control" name="harga_weekly" id="harga_weekly"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Bulanan - 30+ hari</label>
                                    <input type="text" class="form-control" name="harga_monthly" id="harga_monthly"/>
                                </div>
                            </div>
                            @endif
                            <div class="row">
                                <div class="form-group col">
                                    <H4>Harga Layanan Ekstra</H4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col">
                                    <div class="card">
                                        <div class="card-body card-header">
                                          <div class="layanan-conten">
                                          
                                          </div>
                                        </div>
                                        <div class="card-footer text-right">
                                            <input type="button" class="btn btn-danger btn-sm" id="tambah_layanan" value="Tambah">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col">
                                    <H4>Tambahan Biaya</H4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-4">
                                    <label for="">Tamu Tambahan</label>
                                    <select class="form-control js-example-basic-single" name="tamu_tambahan">
                                        <option value="0" >Tidak</option>
                                        <option value="1" >Ya</option>
                                    </select>
                                </div>
                                <div class="form-group col-4">
                                    <label for="">Biaya Tamu</label>
                                    <input type="text" class="form-control" name="harga_tamu_tambahan" value=""/>
                                </div>
                                <div class="form-group col-4">
                                    <label for="">Jumlah Tamu</label>
                                    <input type="text" class="form-control" name="jumlah_tamu_tambahan" value=""/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Biaya Kebersihan</label>
                                    <input type="text" class="form-control" name="biaya_kebersihan" value=""/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Tipe</label>
                                    <select class="form-control js-example-basic-single" name="biaya_kebersihan_tipe">
                                        <option value="2" >Per Stay</option>
                                        @if($tipeId->id_tipe_booking == 3)
                                            <option value="1" >Weekly</option>
                                        @elseif($tipeId->id_tipe_booking == 4)
                                            <option value="1" >Monthly</option>
                                        @elseif($tipeId->id_tipe_booking == 5)
                                            <option value="1" >Hourly</option>
                                        @else
                                            <option value="1" >Daily</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Uang Jaminan</label>
                                    <input type="text" class="form-control" name="uang_jaminan" value=""/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Pajak</label>
                                    <input type="text" class="form-control" name="pajak" value=""/>
                                </div>
                            </div>
                            @if($id != 3 && $id != 4 && $id != 5)
                            <div class="row">
                                <div class="form-group col">
                                    <H4>Kustom Harga Periode</H4>
                                </div>
                            </div>
                            <div class="row">                                
                                <div class="form-group col-6">
                                    <label for="">Mulai Tanggal</label>
                                    <input class="form-control pickerdate" type="text" name="tanggal_mulai_periode" id="tanggal_mulai_periode">
                                </div>                                
                                <div class="form-group col-6">
                                    <label for="">Sampai Tanggal</label>
                                    <input class="form-control pickerdate2" type="text" name="tanggal_selesai_periode" id="tanggal_selesai_periode">
                                </div>                          
                                <div class="form-group col-3">
                                    <label for="">Harga</label>
                                    <input type="text" class="form-control" name="harga_periode" id="harga_periode" value=""/>
                                </div>
                                <div class="form-group col-3">
                                    <label for="">Harga Tamu Tambahan</label>
                                    <input type="text" class="form-control" name="harga_tamu_periode" id="harga_tamu_periode" value=""/>
                                </div>
                                <div class="form-group col-3">
                                    <label for="">Harga Akhir Pekan</label>
                                    <input type="text" class="form-control" name="akhir_pekan_periode" id="akhir_pekan_periode" value=""/>
                                </div>
                                <div class="form-group col-3">
                                    <label for="">Min Durasi Inap</label>
                                    <input type="text" class="form-control" name="min_durasi_inap_periode" id="min_durasi_inap_periode" value=""/>
                                </div>
                                <div class="col-2">                                    
                                    <input type="button" class="btn btn-danger simpan-periode" value="Simpan">
                                </div>
                            </div><br>
                            <div class="row">
                                <div class="col">
                                    <H4>Harga Periode</H4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <table class="table table-borderless table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tanggal Mulai</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Min Durasi Inap</th>
                                            <th>Harga</th>
                                            <th>Harga Tamu Tambahan</th>
                                            <th>Harga Akhir Pekan</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead> 
                                    <tbody class="text-center t-properti">
                                    
                                    <tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </section> <!-- SECTION 3 -->
                        <h4>Media</h4>
                        <section>
                            <div class="row">
                                <div class="col-12">
                                    <input type="file" class="form-control dropify" name="file[]" id="multiupload" multiple/>
                                </div>                                                                
                            </div><br>                                                                                
                            <div class="row pre-gambar">
                                
                            </div>
                        </section> <!-- SECTION 4 -->
                        <h4>Fitur</h4>
                        <section>
                            <div class="row">
                                <div class="col">
                                    <H4>Amenities</H4>
                                </div>
                            </div>
                            <div class="row">
                                @foreach($amenities as $a)
                                <div class="col-3">                                    
                                    <div class="form-check form-check-flat form-check-primary">
                                        <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" name="amenities[]" value="{{$a->id_amenities}}">
                                            {{$a->nama_amenities}}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <br><div class="row">
                                <div class="col">
                                    <H4>Facilities</H4>
                                </div>
                            </div>
                            <div class="row">
                                @foreach($fasilitas as $a)
                                <div class="col-3">                                    
                                    <div class="form-check form-check-flat form-check-primary">
                                        <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" name="fasilitas[]" value="{{$a->id_fasilitas}}">
                                            {{$a->nama_fasilitas}}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </section> <!-- SECTION 5 -->                        
                        <h4>Lokasi</h4>
                        <section>
                            <div class="row">                                                                     
                                <div class="form-group col-8">
                                    <label for="">Alamat</label>
                                    <input type="text" class="form-control" name="" id="pac-input" value=""/>
                                    <input type="hidden" name="alamat" id="alamat" value=""/>
                                </div>
                                <div class="form-group col-4">
                                    <label for="">Apt, Suite</label>
                                    <input type="text" class="form-control" name="apt_suite" value=""/>
                                </div>
                                <div class="form-group col-4">
                                    <label for="">Negara</label>
                                    <!-- <input type="text" class="form-control" name="negara" value=""/> -->
                                    <select class="form-control js-example-basic-single" name="id_negara" id="id_negara" style="width:100%">                           
                                        <option value="" selected disabled>Pilih</option>
                                        @foreach($negara as $data)
                                            <option value="{{$data->id_negara}}">{{$data->nama_negara}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-4">
                                    <label for="">Provinsi</label>
                                    <!-- <input type="text" class="form-control" name="id_provinsi" value=""/> -->
                                    <select class="form-control js-example-basic-single" name="id_provinsi" id="id_provinsi" style="width:100%">                           
                                        <option value="" selected disabled>Pilih</option>
                                        @foreach($provinsi as $data)
                                            <option value="{{$data->id_provinsi}}">{{$data->nama_provinsi}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-4">
                                    <label for="">Kota</label>
                                    <!-- <input type="text" class="form-control" name="id_kota" value=""/> -->
                                    <select class="form-control js-example-basic-single" name="id_kota" id="id_kota" style="width:100%">                           
                                        <option value="" selected disabled>Pilih</option>                                        
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Area</label>
                                    <input type="text" class="form-control" name="area" value=""/>
                                </div>                                
                                <div class="form-group col-6">
                                    <label for="">Kode Pos</label>
                                    <input type="text" class="form-control" name="kode_pos" value=""/>
                                </div>                                
                                <br>
                                <div class="col">                                                                
                                    <label for="">Cari lokasi</label>
                                    <div id="map" style="width:100%;height:400px;"></div>
                                </div>                                                                                      
                            </div><br>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Latitude</label>
                                        <input type="text" name="latitude" id="latitude" class="form-control" placeholder="Latitude"/>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Longitude</label>
                                        <input type="text" name="longitude" id="longitude" class="form-control" placeholder="Longitude"/>
                                    </div>
                                </div>  
                            </div>
                        </section> <!-- SECTION 6 -->
                        <h4>Kamar Tidur</h4>
                        <section>
                            <div id="tidur-awal">
                                <!-- <div class="row tidur-conten">
                                    <div class="form-group col-6">
                                        <label for="">Nama Kamar tidur</label>
                                        <input type="text" class="form-control" name="nama_kamar_tidurs[]" value=""/>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="">Jumlah Tamu</label>
                                        <input type="text" class="form-control" name="jumlah_kamar_tidurs[]" value=""/>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="">Jumlah Tempat Tidur</label>
                                        <input type="text" class="form-control" name="jumlah_tempat_tidurs[]" value=""/>
                                    </div>
                                    <div class="form-group col-6">
                                        <label for="">Jenis Tempat Tidur</label>
                                        <input type="text" class="form-control" name="jenis_tempat_tidurs[]" value=""/>
                                    </div>
                                    <div class="col-2">
                                        <input type="button" class="btn btn-danger btn-sm hapus-tidur" value="Hapus"/>
                                    </div>
                                    <div class="col-12">
                                        <label for=""></label>
                                    </div>
                                </div> -->
                            </div>
                            <div class="row">
                                <div class="col-10">
                                </div>
                                <div class="col-2 text-right">
                                    <input type="button" class="btn btn-danger tambah-tidur" value="Tambah"/>
                                </div>
                            </div>
                        </section> <!-- SECTION 7 -->
                        <h4>Ketentuan</h4>
                        <section>
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="">Kebijakan Pembatalan</label>
                                    <textarea class="form-control" name="kebijakan_pembatalan" id="kebijakan_pembatalan" style="height:130px;"></textarea>
                                </div>
                                <div class="form-group col-6">
                                        @if($tipeId->id_tipe_booking == 3)
                                            <label for="">Minimum Jumlah Minggu</label>
                                        @elseif($tipeId->id_tipe_booking == 4)
                                            <label for="">Minimum Jumlah Bulan</label>
                                        @elseif($tipeId->id_tipe_booking == 5)
                                            <label for="">Minimum Jam Pemesanan</label>
                                        @else                                            
                                            <label for="">Minimum Hari Pemesanan</label>
                                        @endif
                                    <input type="text" class="form-control" name="min_durasi_inap" value=""/>
                                </div>
                                @if($id != 5)
                                <div class="form-group col-6">
                                    @if($tipeId->id_tipe_booking == 3)
                                            <label for="">Maximum Jumlah Minggu</label>
                                        @elseif($tipeId->id_tipe_booking == 4)
                                            <label for="">Maximum Jumlah Bulan</label>
                                        @elseif($tipeId->id_tipe_booking == 5)
                                            <label for="">Maximum Jam Pemesanan</label>
                                        @else                                            
                                            <label for="">Maximum Hari Pemesanan</label>
                                        @endif                                    
                                    <input type="text" class="form-control" name="max_durasi_inap" value=""/>
                                </div>
                                @endif
                                @if($id != 3 && $id != 4 && $id != 5)
                                <div class="form-group col-6">
                                    <label for="">Check-in Setelah</label>
                                    <input type="text" class="form-control timepicker" name="jam_checkin" value=""/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Check-out Sebelum</label>
                                    <input type="text" class="form-control timepicker" name="jam_checkout" value=""/>
                                </div>
                                @endif
                                @if($id != 1 && $id != 2 && $id != 3 && $id != 4)
                                <div class="col-12">
                                    <H4>Jam Operasional</H4><br>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Jam Mulai</label>
                                    <input type="text" class="form-control timepicker" name="jam_operasional_mulai" value=""/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Jam Selesai</label>
                                    <input type="text" class="form-control timepicker" name="jam_operasional_selesai" value=""/>
                                </div>
                                @endif
                                <div class="form-group col-6">
                                    <label for="">Merokok Diperbolehkan</label>
                                    <select class="form-control js-example-basic-single" name="merokok" style="width:100%">
                                        <option value="0">Tidak</option>
                                        <option value="1">Ya</option>
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Hewan Peliharaan Diperbolehkan</label>
                                    <select class="form-control js-example-basic-single" name="binatang" style="width:100%">
                                        <option value="0">Tidak</option>
                                        <option value="1">Ya</option>
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Acara Diperbolehkan</label>
                                    <select class="form-control js-example-basic-single" name="acara" style="width:100%">
                                        <option value="0">Tidak</option>
                                        <option value="1">Ya</option>
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Anak Diperbolehkan</label>
                                    <select class="form-control js-example-basic-single" name="anak" style="width:100%">
                                        <option value="0">Tidak</option>
                                        <option value="1">Ya</option>
                                    </select>
                                </div>
                                <div class="form-group col-12">
                                    <label for="">Aturan Tambahan</label>
                                    <textarea class="form-control" name="aturan_tambahan" id="aturan_tambahan" style="height:130px;"></textarea>
                                </div>
                            </div>
                        </section> <!-- SECTION 7 -->
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="{{asset('/')}}assets/vendors/tinymce/tinymce.min.js"></script>
<script src="{{asset('/')}}assets/js/dropify.js"></script>
<script type='text/javascript'> 
$( document ).ready(function() {
    var $input = $('<li class="li-draf" aria-disabled="false"><a href="javascript:void(0)" role="menuitem" class="simpan_draf text-center" onclick="simpan_draf()"><span id="spinner" class="circle-loader d-none" style="margin-left:-20px"></span><span id="txt-btn">Simpan Draf</a></li>');
    $input.appendTo($('ul[aria-label=Pagination]'));
});
    tinymce.init({ selector:'textarea', menubar:'', theme: 'modern'});
    $("#wizard").steps({
        headerTag: "h4",
        bodyTag: "section",
        transitionEffect: "fade",
        enableAllSteps: false,
        transitionEffectSpeed: 500,
        onStepChanging: function (event, currentIndex, newIndex) {                     
            var id_jenis_tempat = document.getElementById("id_jenis_tempat");
            var judul = document.getElementById("judul");
            var id_tipe_properti = document.getElementById("id_tipe_properti");
            var jumlah_kamar_tidur = document.getElementById("jumlah_kamar_tidur");
            var jumlah_tamu = document.getElementById("jumlah_tamu");
            var jumlah_kamar_mandi = document.getElementById("jumlah_kamar_mandi");
            var nama_properti = document.getElementById("nama_properti");
            var harga_tampil = document.getElementById("harga_tampil");
            var alamat = document.getElementById("pac-input");

            if(newIndex == 6){
                $('ul[aria-label=Pagination] li[class="li-draf"]').remove();
            }
            
            if(currentIndex == 0){
                if ($('#id_jenis_tempat').val() == null) {                
                    id_jenis_tempat.classList.add("invalid");                
                    return false;
                }else{
                    id_jenis_tempat.classList.remove("invalid");                
                }

                if ($('#judul').val().length == 0) {
                    judul.classList.add("invalid");                
                    return false;
                }else{
                    judul.classList.remove("invalid");
                }

                if ($('#id_tipe_properti').val() == null) {                
                    id_tipe_properti.classList.add("invalid"); 
                    return false;
                }else{
                    id_tipe_properti.classList.remove("invalid");                
                }

                if ($('#jumlah_kamar_tidur').val().length == 0) {
                    jumlah_kamar_tidur.classList.add("invalid");
                    return false;
                }else{
                    jumlah_kamar_tidur.classList.remove("invalid");
                }

                if ($('#jumlah_tamu').val().length == 0) {
                    jumlah_tamu.classList.add("invalid");      
                    return false;
                }else{
                    jumlah_tamu.classList.remove("invalid");
                }

                if ($('#jumlah_kamar_mandi').val().length == 0) {
                    jumlah_kamar_mandi.classList.add("invalid"); 
                    return false;
                }else{
                    jumlah_kamar_mandi.classList.remove("invalid");
                }

                if ($('#nama_properti').val().length == 0) {
                    nama_properti.classList.add("invalid");
                    return false;
                }else{
                    nama_properti.classList.remove("invalid");
                }
            }

            if(currentIndex == 1){
                if ($('#harga_tampil').val().length == 0) {
                    harga_tampil.classList.add("invalid");
                    return false;
                }else{
                    harga_tampil.classList.remove("invalid");
                }
            }

            if(currentIndex == 4){
                if ($('#alamat').val().length == 0) {
                    alamat.classList.add("invalid");
                    return false;
                }else{
                    alamat.classList.remove("invalid");
                }
            }
            return true;

        },        
        onFinished: function(event, currentIndex) {
            $('a[href$="finish"]').text('');
            var $input = $('<span id="spinner" class="circle-loader" style="margin-left:-20px">');
            $input.appendTo('a[href$="finish"]');
            $('#deleted').val(1);
            var des = tinyMCE.get('deskripsi').getContent();
            var tahu = tinyMCE.get('perlu_diketahui').getContent();
            var batal = tinyMCE.get('kebijakan_pembatalan').getContent();
            var tambah = tinyMCE.get('aturan_tambahan').getContent();
            $('#deskripsi').val(des);
            $('#perlu_diketahui').val(tahu);
            $('#kebijakan_pembatalan').val(batal);
            $('#aturan_tambahan').val(tambah);
            $.ajax({
                url: "{{url('properti/create-save')}}",
                type: "POST",
                data:  new FormData($('#form')[0]),
                contentType: false,
                cache: false,
                processData:false,
                success: function(res){
                    window.location = "{{route('properti-index')}}";
                }
            });
        },
        labels: {
            finish: "Simpan",
            next: "Next",
            previous: "Previous"
        }
    });
    // Custom Steps Jquery Steps
    // $('.wizard > .steps li a').click(function(){
    // 	$(this).parent().addClass('checked');
	// 	$(this).parent().prevAll().addClass('checked');
	// 	$(this).parent().nextAll().removeClass('checked');
    // });
    // Custom Button Jquery Steps
    // $('.forward').click(function(){        
    //     $("#wizard").steps('next');
    // })
    // $('.backward').click(function(){
    //     $("#wizard").steps('previous');
    // })
    function simpan_draf() {
        $('#deleted').val(2);
        $("#spinner").removeClass('d-none');
        $('#txt-btn').text('');
        var des = tinyMCE.get('deskripsi').getContent();
        var tahu = tinyMCE.get('perlu_diketahui').getContent();
        var batal = tinyMCE.get('kebijakan_pembatalan').getContent();
        var tambah = tinyMCE.get('aturan_tambahan').getContent();
        $('#deskripsi').val(des);
        $('#perlu_diketahui').val(tahu);
        $('#kebijakan_pembatalan').val(batal);
        $('#aturan_tambahan').val(tambah);
        // var formData = new FormData(this);
        $.ajax({
            url: "{{url('properti/create-save')}}",
            type: "POST",
            data:  new FormData($('#form')[0]),
            contentType: false,
            cache: false,
            processData:false,
            success: function(res){
                // window.location = "{{route('properti-index')}}";
                $("#spinner").addClass('d-none');
                $('#txt-btn').text('Simpan Draf');
                $('#id_properti').val(res.id);
                $('#id_ref_bahasa').val(res.id);
            }
        });
    }

    $('#tanggal_mulai_periode').on('change',function(){
        var date2 = $('.pickerdate').datepicker('getDate', '+1d'); 
        // date2.setDate(date2.getDate()+1); 
        // console.log(date2);
        $(".pickerdate2").datepicker("destroy");
        $(".pickerdate2").datepicker( {
            format: "dd-mm-yyyy",
            startDate: date2,
        });      
    }); 

    $("#multiupload").on('change', function(){    
      var formData = new FormData();
      let TotalFiles = $('#multiupload')[0].files.length; //Total files
      let files = $('#multiupload')[0];
      for (let i = 0; i < TotalFiles; i++) {
          formData.append('files' + i, files.files[i]);
        let html ='<div class="col-3 loding-gambar">\
                        <div class="d-flex justify-content-center">\
                            <div id="spinner" class="circle-loader"></div>\
                        </div>\
                        <div class="d-flex justify-content-between">\
                            <div class="form-check">\
                                <a class="text-primary" ><span class="mdi mdi-star-outline mdi-24px"></span></a>\
                            </div>\
                            <div class="form-check">\
                                <a class="text-primary" ><span class="mdi mdi-delete mdi-24px"></span></a>\
                            </div>\
                        </div>\
                    </div>';
        $('.pre-gambar').append(html);          
      }
      formData.append('TotalFiles', TotalFiles);      

        $.ajax({
            url: "{{url('properti/upload')}}",
            type: "POST",
            data:  formData,
            contentType: false,
            cache: false,
            processData:false,
            success: function(res){
            $('.loding-gambar').remove();
                for (let index = 0; index < res.gambar.length; index++) {
                    const element = res.gambar[index];
                    var img = "{{asset('upload/properti')}}/"+element;
                    let html ='<div class="col-3">\
                                    <img class="rounded" width="100%" src="'+img+'" alt="">\
                                        <div class="d-flex justify-content-between">\
                                            <div class="form-check">\
                                                <input type="hidden" name="featured_image[]" id="bintang" value="0">\
                                                <a class="text-primary feature" ><span class="mdi mdi-star-outline mdi-24px"></span></a>\
                                            </div>\
                                            <div class="form-check">\
                                                <input type="hidden" name="nama_file[]" value="'+element+'">\
                                                <input type="hidden" name="id_tipe[]" value="1">\
                                                <a class="text-primary delete" ><span class="mdi mdi-delete mdi-24px"></span></a>\
                                            </div>\
                                        </div>\
                                </div>';
                    $('.pre-gambar').append(html);          
                }
            }
        });
    });
    

    $('.pre-gambar').on('click', 'a.feature', function(events){
          events.preventDefault();
        let idx = $(this).closest('#layanan').index();                
        $('.feature span').removeClass('mdi-star').addClass('mdi-star-outline');
        $("input[name='featured_image[]']").val(0);        
        $(this).prev().val(1)
        $(this).children().removeClass('mdi-star-outline').addClass('mdi-star');        
        // console.log(img);        
    });
    $('.pre-gambar').on('click', 'a.delete', function(events){
          events.preventDefault();
        let idx = $(this).closest('#layanan').index();
        var img = $(this).prev().prev().val();
        $(this).parent().parent().parent().remove();
        // console.log(img);
        $.ajax({
            url: "{{url('properti/delete-img')}}/"+img,
            type: "GET",            
            contentType: false,
            cache: false,
            processData:false,
            success: function(res){

            }
        });
    });
    // Checkbox
    $('.checkbox-circle label').click(function(){
        $('.checkbox-circle label').removeClass('active');
        $(this).addClass('active');
    })
    $(".pickerdate").datepicker( {
            format: "dd-mm-yyyy",
            orientation: "bottom"
    });
    
    $('.timepicker').timepicker({
        timeFormat: 'H:mm',
        interval: 30,
        minTime: '00:00',
        maxTime: '23:00',
        startTime: '01:00',
        dynamic: false,
        dropdown: true,
        scrollbar: true
    });

    $('.layanan-conten').on('click', 'input.hapus_layanan', function(events){
          events.preventDefault();
          let idx = $(this).closest('#layanan').index();
          $(this).parent().parent().remove();
    });
    $("#tambah_layanan").click(function(e){
          let html ='<div class="row" id="layanan">\
                    <div class="form-group col-4">\
                        <label for="">Nama</label>\
                        <input type="text" class="form-control" name="nama_service[]" value=""/>\
                    </div>\
                    <div class="form-group col-3">\
                        <label for="">harga</label>\
                        <input type="text" class="form-control" name="harga_layanan[]" value=""/>\
                    </div>\
                    <div class="form-group col-3">\
                        <label for="">Tipe</label>\
                        <select class="form-control js-example-basic-single" name="tipe_layanan[]" style="width:100%">\
                            <option value="" selected disabled>Pilih</option>\
                            <option value="1">Single Fee</option>\
                            <option value="2">{{$tipeId->nama_tipe_booking}}</option>\
                            <option value="3">Per Guest</option>\
                            <option value="4">{{$tipeId->nama_tipe_booking}} Per Guest</option>\
                        </select>\
                    </div>\
                    <div class="form-group col-2">\
                        <label for=""> </label>\
                        <input type="button" class="btn btn-danger form-control hapus_layanan" value="Delete">\
                    </div>\
                    </div>';
        $('.layanan-conten').append(html);          
    });

    $('#tidur-awal').on('click', 'input.hapus-tidur', function(events){
          events.preventDefault();
          let idx = $(this).closest('#tidur-awal').index();
          $(this).parent().parent().remove();
    });
    $(".tambah-tidur").click(function(e){
          let html ='<div class="row tidur-conten">\
                                <div class="form-group col-6">\
                                    <label for="">Nama Kamar tidur</label>\
                                    <input type="text" class="form-control" name="nama_kamar_tidurs[]" value=""/>\
                                </div>\
                                <div class="form-group col-6">\
                                    <label for="">Jumlah Tamu</label>\
                                    <input type="text" class="form-control" name="jumlah_tamu_tidurs[]" value=""/>\
                                </div>\
                                <div class="form-group col-6">\
                                    <label for="">Jumlah Tempat Tidur</label>\
                                    <input type="text" class="form-control" name="jumlah_tempat_tidurs[]" value=""/>\
                                </div>\
                                <div class="form-group col-6">\
                                    <label for="">Jenis Tempat Tidur</label>\
                                    <input type="text" class="form-control" name="jenis_tempat_tidurs[]" value=""/>\
                                </div>\
                                <div class="col-2">\
                                    <input type="button" class="btn btn-danger btn-sm hapus-tidur" value="Hapus"/>\
                                </div>\
                                <div class="col-12">\
                                    <label for=""></label>\
                                </div>\
                            </div>';
        $('#tidur-awal').append(html);          
    });

        $('.simpan-periode').click(function (e) {
            e.preventDefault();
            $(this).html('Loading ...');
            var tanggal_mulai_periode = $('#tanggal_mulai_periode').val();
            var tanggal_selesai_periode = $('#tanggal_selesai_periode').val();
            var harga_periode = $('#harga_periode').val();
            var harga_tamu_periode = $('#harga_tamu_periode').val();
            var akhir_pekan_periode = $('#akhir_pekan_periode').val();
            var min_durasi_inap_periode = $('#min_durasi_inap_periode').val();

            $('.simpan-periode').html('Simpan');
                    let html ='<tr>\
                                <td><input type="hidden" name="tanggal_mulai_periode[]" value="'+tanggal_mulai_periode+'"/>'+tanggal_mulai_periode+'</td>\
                                <td><input type="hidden" name="tanggal_selesai_periode[]" value="'+tanggal_selesai_periode+'"/>'+tanggal_selesai_periode+'</td>\
                                <td><input type="hidden" name="min_durasi_inap_periode[]" value="'+min_durasi_inap_periode+'"/>'+min_durasi_inap_periode+'</td>\
                                <td><input type="hidden" name="harga_periode[]" value="'+harga_periode+'"/>'+harga_periode+'</td>\
                                <td><input type="hidden" name="harga_tamu_periode[]" value="'+harga_tamu_periode+'"/>'+harga_tamu_periode+'</td>\
                                <td><input type="hidden" name="akhir_pekan_periode[]" value="'+akhir_pekan_periode+'"/>'+akhir_pekan_periode+'</td>\
                                <td><input type="button" class="btn btn-danger btn-sm" value="Hapus" onclick="myFunction(this)"></td>\
                            </tr>';
            $('.t-properti').append(html);

            $('#tanggal_mulai_periode').val('');
            $('#tanggal_selesai_periode').val('');
            $('#harga_periode').val('');
            $('#harga_tamu_periode').val('');
            $('#akhir_pekan_periode').val('');
            $('#min_durasi_inap_periode').val('');
        });

    function myFunction(selectObject) {                
        $(selectObject).parent().parent().remove();            
    }
    $('#id_provinsi').on('change',function(){
            let provinsi = $(this).val();
            document.getElementById('id_kota').options[0].text = 'loading ...';
            $.ajax({
                url: '{{ url("/properti/kota-provinsi/") }}',
                type: "GET",
                data: { provinsi:provinsi } ,                
                success: function(res){
                    document.getElementById('id_kota').options[0].text = 'Pilih';
                    $('#id_kota').html(res.data);
                }
            });
        }); 
    $("#pac-input").keyup(function(){        
        $("#alamat").val($("#pac-input").val());
    });
</script>
<script>
        function initAutocomplete() {
          var map = new google.maps.Map(
            document.getElementById('map'), {
            center: {lat: -6.231775793909771,  lng: 106.84323894367465},
            zoom: 10,
            mapTypeControl: true,
            mapTypeControlOptions: {
              style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
              position: google.maps.ControlPosition.BOTTOM_LEFT,
            },
            }
          );          
            function placeMarker(location, map) {
                if ( marker ) {
                    marker.setPosition(location);
                } else {
                    marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    draggable: true,
                    });
                }                
                document.getElementById('latitude').value = location.lat();
                document.getElementById('longitude').value = location.lng();
            }
          var input = document.getElementById('pac-input');
        //   map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);

          var autocomplete = new google.maps.places.Autocomplete(input);
          autocomplete.bindTo('bounds', map);
          var marker = new google.maps.Marker({
              //draggable :true,
              map: map,
              draggable: true,
              anchorPoint: new google.maps.Point(0, -29)
          });
          google.maps.event.addListener(map, "click", (event) => {
            placeMarker(event.latLng, map);
          });
          google.maps.event.addListener(marker, 'dragend', function(marker) {
                var latLng = marker.latLng;
                document.getElementById('latitude').value = latLng.lat();
                document.getElementById('longitude').value = latLng.lng();
          });
          autocomplete.addListener('place_changed', function() {
            marker.setVisible(false);
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                //window.alert("Autocomplete's returned place contains no geometry");
                return;
            }
            if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }
                
            // google.maps.event.addListener(marker, 'dragend', function(marker) {
            //     var latLng = marker.latLng;
            //     document.getElementById('latitude').value = latLng.lat();
            //     document.getElementById('longitude').value = latLng.lng();
            // });

            marker.setPosition(place.geometry.location);
            marker.setVisible(true);
            document.getElementById('latitude').value = place.geometry.location.lat();
            document.getElementById('longitude').value = place.geometry.location.lng();
            var place = autocomplete.getPlace();
            var inputValue = place.name + " " + place.formatted_address;
            // console.log(inputValue);
            document.getElementById('alamat').value = place.formatted_address;
          });
        }
        
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB-W-RsTAPM3gMXac5yEMIxNbip9mSEVuo&callback=initAutocomplete&libraries=places&v=weekly" defer></script>
@endpush