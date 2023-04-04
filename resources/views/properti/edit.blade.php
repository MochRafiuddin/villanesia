@extends('template')
@section('content')
<?php 
use App\Traits\Helper;  
?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">{{$titlePage}} Properti</h6>
                <form action="{{$url}}" method="post" enctype="multipart/form-data">
                    @csrf            
                    <div class="row">
                        @if(Session::get('msg'))
                        <div class="col-12">
                            <div class="alert alert-success" role="alert">
                                {{Session::get('msg')}}
                            </div>
                        </div>
                        @endif
                        <!-- <div class="col-6">
                            <input type="botton" class="btn btn-inverse-dark col-12 btn-sm" value="View" />
                        </div> -->
                        <div class="col-12">                            
                            <input type="submit" class="btn btn-success col-12 btn-sm" value="Simpan" />
                        </div>
                    </div><br>
                    <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="information-tab" data-toggle="tab" href="#information" role="tab" aria-controls="information" aria-selected="true">Information</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="harga-tab" data-toggle="tab" href="#harga" role="tab" aria-controls="harga" aria-selected="false">Harga</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="media-tab" data-toggle="tab" href="#media" role="tab" aria-controls="media" aria-selected="false">Media</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="fitur-tab" data-toggle="tab" href="#fitur" role="tab" aria-controls="fitur" aria-selected="false">Fitur</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="lokasi-tab" data-toggle="tab" href="#lokasi" role="tab" aria-controls="lokasi" aria-selected="false">Lokasi</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="tidur-tab" data-toggle="tab" href="#tidur" role="tab" aria-controls="tidur" aria-selected="false">Kamar Tidur</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="ketentuan-tab" data-toggle="tab" href="#ketentuan" role="tab" aria-controls="ketentuan" aria-selected="false">Ketentuan</a>
                    </li>
                  </ul>
                  <div class="tab-content">
                    <div class="tab-pane fade show active" id="information" role="tabpanel" aria-labelledby="information-tab">
                      <div class="media">                        
                        <div class="media-body">
                            <div class="row">
                                <div class="form-group col">
                                    <label for="exampleInputEmail1">Bahasa</label><br>
                                    <i class="flag-icon {{$bahasa->logo}}"></i> {{$bahasa->nama_bahasa}}                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col">
                                    <label for="id_jenis_tempat">Jenis Tempat</label>
                                    <input type="hidden" name="id_properti" id="id_properti" value="{{$id}}">
                                    <input type="hidden" name="id_tipe_booking" id="id_tipe_booking" value="{{$data->id_tipe_booking}}">
                                    <input type="hidden" name="id_bahasa" id="id_bahasa" value="{{$bahasa->id_bahasa}}">
                                    <input type="hidden" name="id_ref_bahasa" id="id_ref_bahasa" value="{{$data->id_ref_bahasa}}">
                                    <input type="hidden" name="deleted" id="deleted" value="{{$data->deleted}}">
                                    <select class="form-control js-example-basic-single" name="id_jenis_tempat" id="id_jenis_tempat" style="width:100%">                           
                                        <option value="" selected disabled>Pilih</option>
                                        @foreach($jenis as $d)
                                        <option value="{{$d->id_ref_bahasa}}" {{($data->id_jenis_tempat == $d->id_ref_bahasa) ? 'selected' : ''}}>{{$d->nama_jenis_tempat}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col">
                                    <label for="judul">Judul</label>
                                    <input type="text" class="form-control" name="judul" value="{{$data->judul}}"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col">
                                    <label for="deskripsi">Deskripsi</label>
                                    <textarea class="" name="deskripsi" id="tinyMceExample" >{{$data->deskripsi}}</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col">
                                    <label for="perlu_diketahui">Perlu Tahu</label>
                                    <textarea class="" name="perlu_diketahui" id="tinyMceExample" >{{$data->perlu_diketahui}}</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="id_tipe_properti">Tipe Properti</label>
                                    <select class="form-control js-example-basic-single" name="id_tipe_properti" id="id_tipe_properti" style="width:100%">                           
                                        <option value="" selected disabled>Pilih</option>
                                        @foreach($tipe as $d)
                                            <option value="{{$d->id_ref_bahasa}}" {{($data->id_tipe_properti == $d->id_ref_bahasa) ? 'selected' : ''}}>{{$d->nama_tipe_properti}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label for="jumlah_kamar_tidur">Jumlah Kamar Tidur</label>
                                    <input type="text" class="form-control" name="jumlah_kamar_tidur" value="{{$data->jumlah_kamar_tidur}}"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="jumlah_tamu">Jumlah Tamu</label>
                                    <input type="text" class="form-control" name="jumlah_tamu" value="{{$data->jumlah_tamu}}"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="jumlah_tempat_tidur">Jumlah Tempat Tidur</label>
                                    <input type="text" class="form-control" name="jumlah_tempat_tidur" value="{{$data->jumlah_tempat_tidur}}"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="jumlah_kamar_mandi">Jumlah Kamar Mandi</label>
                                    <input type="text" class="form-control" name="jumlah_kamar_mandi" value="{{$data->jumlah_kamar_mandi}}"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="ukuran">Ukuran</label>
                                    <input type="text" class="form-control" name="ukuran" value="{{$data->ukuran}}"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="satuan_ukuran">Satuan</label>
                                    <input type="text" class="form-control" name="satuan_ukuran" value="{{$data->satuan_ukuran}}"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="nama_properti">Nama Villa</label>
                                    <input type="text" class="form-control" name="nama_properti" value="{{$data->nama_properti}}"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="sarapan">Sarapan</label>
                                    <input type="text" class="form-control" name="sarapan" value="{{$data->sarapan}}"/>
                                </div>                        
                            </div>
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="harga" role="tabpanel" aria-labelledby="harga-tab">
                      <div class="media">                        
                        <div class="media-body">
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="">Harga {{$tipeId->nama_tipe_booking}}</label>
                                    <input type="text" class="form-control" name="harga_tampil" value="{{$data->harga_tampil}}"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Setelah Harga Label</label>
                                    <input type="text" class="form-control" name="setelah_label_harga" value="{{$data->setelah_label_harga}}"/>
                                </div>
                                @if($data->id_tipe_booking != 3 && $data->id_tipe_booking != 4)
                                <div class="form-group col-6">
                                    <label for="">Harga Weekend</label>
                                    <input type="text" class="form-control" name="harga_weekend" value="{{$data->harga_weekend}}"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Penerapan Harga Weekend</label>
                                    <select class="form-control js-example-basic-single" name="penerapan_harga_weekend" style="width:100%">                           
                                        <option value="" selected disabled>Pilih</option>                                        
                                        @foreach($pekan as $d)
                                            <option value="{{$d->id_ref_bahasa}}" {{($data->penerapan_harga_weekend == $d->id_ref_bahasa) ? 'selected' : ''}}>{{$d->detail_akhir_pekan}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            </div>
                            @if($data->id_tipe_booking != 3 && $data->id_tipe_booking != 4 && $data->id_tipe_booking != 5)
                            <div class="row">
                                <div class="form-group col">
                                    <H4>Harga Jangka Panjang</H4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-6">
                                    <label for="">Mingguan - 7+ hari</label>
                                    <input type="text" class="form-control" name="harga_weekly" value="{{$data->harga_weekly}}"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Bulanan - 30+ hari</label>
                                    <input type="text" class="form-control" name="harga_monthly" value="{{$data->harga_monthly}}"/>
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
                                            @foreach($extra as $ex)
                                          <div class="row" id="layanan">
                                            <div class="form-group col-4">
                                                <label for="">Nama</label>
                                                <input type="text" class="form-control" name="nama_service[]" value="{{$ex->nama_service}}"/>
                                            </div>
                                            <div class="form-group col-3">
                                                <label for="">harga</label>
                                                <input type="text" class="form-control" name="harga_layanan[]" value="{{$ex->harga}}"/>
                                            </div>
                                            <div class="form-group col-3">
                                                <label for="">Tipe</label>
                                                <select class="form-control js-example-basic-single" name="tipe_layanan[]" style="width:100%">                           
                                                    <option value="" selected disabled>Pilih</option>                                        
                                                    <option value="1" {{($ex->tipe == 1) ? 'selected' : ''}}>Single Fee</option>
                                                    <option value="2" {{($ex->tipe == 2) ? 'selected' : ''}}>{{$tipeId->nama_tipe_booking}}</option>
                                                    <option value="3" {{($ex->tipe == 3) ? 'selected' : ''}}>Per Guest</option>
                                                    <option value="4" {{($ex->tipe == 4) ? 'selected' : ''}}>{{$tipeId->nama_tipe_booking}} Per Guest</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-2">
                                                <label for=""> </label>
                                                <input type="button" class="btn btn-danger form-control hapus_layanan" value="Delete">
                                            </div>
                                          </div>
                                          @endforeach
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
                                    <select class="form-control js-example-basic-single" name="tamu_tambahan" style="width:100%">
                                        <option value="0" {{($data->tamu_tambahan == 0) ? 'selected' : ''}}>Tidak</option>
                                        <option value="1" {{($data->tamu_tambahan == 1) ? 'selected' : ''}}>Ya</option>
                                    </select>
                                </div>
                                <div class="form-group col-4">
                                    <label for="">Biaya Tamu</label>
                                    <input type="text" class="form-control" name="harga_tamu_tambahan" value="{{$data->harga_tamu_tambahan}}"/>
                                </div>
                                <div class="form-group col-4">
                                    <label for="">Jumlah Tamu</label>
                                    <input type="text" class="form-control" name="jumlah_tamu_tambahan" value="{{$data->jumlah_tamu_tambahan}}"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Biaya Kebersihan</label>
                                    <input type="text" class="form-control" name="biaya_kebersihan" value="{{$data->biaya_kebersihan}}"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Tipe</label>
                                    <select class="form-control js-example-basic-single" name="biaya_kebersihan_tipe" style="width:100%">
                                    @if($data->id_tipe_booking == 3)
                                        <option value="1" {{($data->biaya_kebersihan_tipe == 1) ? 'selected' : ''}}>Weekly</option>
                                    @elseif($data->id_tipe_booking == 4)
                                        <option value="1" {{($data->biaya_kebersihan_tipe == 1) ? 'selected' : ''}}>Monthly</option>
                                    @elseif($data->id_tipe_booking == 5)
                                        <option value="1" {{($data->biaya_kebersihan_tipe == 1) ? 'selected' : ''}}>Hourly</option>
                                    @else                                            
                                        <option value="1" {{($data->biaya_kebersihan_tipe == 1) ? 'selected' : ''}}>Daily</option>
                                    @endif
                                        <option value="2" {{($data->biaya_kebersihan_tipe == 2) ? 'selected' : ''}}>Per Stay</option>
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Uang Jaminan</label>
                                    <input type="text" class="form-control" name="uang_jaminan" value="{{$data->uang_jaminan}}"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Pajak</label>
                                    <input type="text" class="form-control" name="pajak" value="{{$data->pajak}}"/>
                                </div>
                            </div>
                            @if($data->id_tipe_booking != 3 && $data->id_tipe_booking != 4 && $data->id_tipe_booking != 5)
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
                                        <button class="btn btn-danger simpan-periode">Simpan</button>                                    
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
                                    @foreach($harga_periode as $per)    
                                        <tr>
                                            <td>{{date('d-m-Y',strtotime($per->start_date))}}</td>
                                            <td>{{date('d-m-Y',strtotime($per->end_date))}}</td>
                                            <td>{{$per->min_durasi_inap}}</td>
                                            <td>{{$per->harga}}</td>
                                            <td>{{$per->harga_tamu_tambahan}}</td>
                                            <td>{{$per->harga_weekend}}</td>                                            
                                            <td><input type="button" class="btn btn-danger btn-sm" value="Hapus" onclick="myFunction(this,{{$per->id_properti_periode_cus}})"></td>
                                        </tr>
                                    @endforeach    
                                    <tbody>
                                    </table>
                                </div>
                            </div>
                            @endif
                        </div>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="media" role="tabpanel" aria-labelledby="media-tab">
                        <div class="row">
                            <div class="col-12">
                                <input type="file" class="form-control dropify" name="file[]" id="multiupload" multiple/>
                            </div>                                                                
                        </div><br>                                                                                
                        <div class="row pre-gambar">
                            @foreach($galery as $as)
                            <div class="col-3">
                                <img class="rounded" width="100%" src="{{asset('upload/properti/'.$as->nama_file)}}" alt="">
                                <div class="d-flex justify-content-between">
                                    <div class="form-check">
                                        <input type="hidden" name="featured_image[]" id="bintang" value="{{$as->featured_image}}">
                                        @if($as->featured_image == 1)
                                        <a class="text-primary feature" ><span class="mdi mdi-star mdi-24px"></span></a>
                                        @else
                                        <a class="text-primary feature" ><span class="mdi mdi-star-outline mdi-24px"></span></a>
                                        @endif
                                    </div>
                                    <div class="form-check">
                                        <input type="hidden" name="nama_file[]" value="{{$as->nama_file}}">
                                        <input type="hidden" name="id_tipe[]" value="{{$as->id_tipe}}">
                                        <a class="text-primary delete" ><span class="mdi mdi-delete mdi-24px"></span></a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-pane fade" id="fitur" role="tabpanel" aria-labelledby="fitur-tab">
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
                                    <input type="checkbox" class="form-check-input" name="amenities[]" value="{{$a->id_ref_bahasa}}" 
                                    {{Helper::showDataChecked2($map_a,'id_amenities',$a->id_ref_bahasa,)}}>{{$a->nama_amenities}}</label>
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
                                    <input type="checkbox" class="form-check-input" name="fasilitas[]" value="{{$a->id_ref_bahasa}}" {{Helper::showDataChecked2($map_f,'id_fasilitas',$a->id_ref_bahasa,)}}>
                                        {{$a->nama_fasilitas}}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-pane fade" id="lokasi" role="tabpanel" aria-labelledby="lokasi-tab">
                        <div class="row">                                                                     
                            <div class="form-group col-8">
                                <label for="">Alamat</label>
                                <input type="text" class="form-control" name="" id="pac-input" value="{{$data->alamat}}"/>
                                <input type="hidden" name="alamat" id="alamat" value="{{$data->alamat}}"/>
                            </div>
                            <div class="form-group col-4">
                                <label for="">Apt, Suite</label>
                                <input type="text" class="form-control" name="apt_suite" value="{{$data->apt_suite}}"/>
                            </div>                            
                            <div class="form-group col-4">
                                <label for="">Negara</label>
                                <!-- <input type="text" class="form-control" name="" value=""/> -->
                                <select class="form-control js-example-basic-single" name="id_negara" id="id_negara" style="width:100%">                           
                                        <option value="" selected disabled>Pilih</option>
                                        @foreach($negara as $ne)
                                            <option value="{{$ne->id_negara}}" {{($data->id_negara == $ne->id_negara) ? 'selected' : ''}}>{{$ne->nama_negara}}</option>
                                        @endforeach
                                    </select>
                            </div>
                            <div class="form-group col-4">
                                <label for="">Provinsi</label>
                                <!-- <input type="text" class="form-control" name="" value=""/> -->
                                <select class="form-control js-example-basic-single" name="id_provinsi" id="id_provinsi" style="width:100%">                           
                                        <option value="" selected disabled>Pilih</option>
                                        @foreach($provinsi as $pro)
                                            <option value="{{$pro->id_provinsi}}" {{($data->id_provinsi == $pro->id_provinsi) ? 'selected' : ''}}>{{$pro->nama_provinsi}}</option>
                                        @endforeach
                                    </select>
                            </div>
                            <div class="form-group col-4">
                                <label for="">Kota</label>
                                <!-- <input type="text" class="form-control" name="" value=""/> -->
                                <select class="form-control js-example-basic-single" name="id_kota" id="id_kota" style="width:100%">                           
                                    <option value="" selected disabled>Pilih</option>
                                        @foreach($kota as $ko)
                                            <option value="{{$ko->id_kota}}" {{($data->id_kota == $ko->id_kota) ? 'selected' : ''}}>{{$ko->nama_kota}}</option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label for="">Kode Pos</label>
                                <input type="text" class="form-control" name="kode_pos" value="{{$data->kode_pos}}"/>
                            </div>
                            <div class="form-group col-6">
                                <label for="">Area</label>
                                <input type="text" class="form-control" name="area" value="{{$data->area}}"/>
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
                                    <input type="text" name="latitude" id="latitude" class="form-control" placeholder="Latitude" value="{{$data->latitude}}"/>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Longitude</label>
                                    <input type="text" name="longitude" id="longitude" class="form-control" placeholder="Longitude" value="{{$data->longitude}}"/>
                                </div>
                            </div>  
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tidur" role="tabpanel" aria-labelledby="tidur-tab">
                        <div id="tidur-awal">
                            @foreach($tidur as $ti)
                            <div class="row tidur-conten">
                                <div class="form-group col-6">
                                    <label for="">Nama Kamar tidur</label>
                                    <input type="text" class="form-control" name="nama_kamar_tidurs[]" value="{{$ti->nama_kamar_tidur}}"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Jumlah Tamu</label>
                                    <input type="text" class="form-control" name="jumlah_tamu_tidurs[]" value="{{$ti->jumlah_tamu}}"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Jumlah Tempat Tidur</label>
                                    <input type="text" class="form-control" name="jumlah_tempat_tidurs[]" value="{{$ti->jumlah_tempat_tidur}}"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Jenis Tempat Tidur</label>
                                    <input type="text" class="form-control" name="jenis_tempat_tidurs[]" value="{{$ti->jenis_tempat_tidur}}"/>
                                </div>
                                <div class="col-2">
                                    <input type="button" class="btn btn-danger btn-sm hapus-tidur" value="Hapus"/>
                                </div>
                                <div class="col-12">
                                    <label for=""></label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="col-12 text-right">
                                <input type="button" class="btn btn-danger tambah-tidur" value="Tambah"/>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="ketentuan" role="tabpanel" aria-labelledby="ketentuan-tab">                        
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="">Kebijakan Pembatalan</label>
                                    <textarea class="form-control" name="kebijakan_pembatalan" id="" style="height:130px;">{{$data->kebijakan_pembatalan}}</textarea>
                                </div>
                                <div class="form-group col-6">
                                    @if($data->id_tipe_booking == 3)
                                        <label for="">Minimum Jumlah Minggu</label>
                                    @elseif($data->id_tipe_booking == 4)
                                        <label for="">Minimum Jumlah Bulan</label>
                                    @elseif($data->id_tipe_booking == 5)
                                        <label for="">Minimum Jam Pemesanan</label>
                                    @else                                            
                                        <label for="">Minimum Hari Pemesanan</label>
                                    @endif                                    
                                    <input type="text" class="form-control" name="min_durasi_inap" value="{{$data->min_durasi_inap}}"/>
                                </div>                                
                                @if($data->id_tipe_booking != 5)
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
                                    <input type="text" class="form-control" name="max_durasi_inap" value="{{$data->max_durasi_inap}}"/>
                                </div>
                                @endif
                                @if($data->id_tipe_booking != 3 && $data->id_tipe_booking != 4 && $data->id_tipe_booking != 5)
                                <div class="form-group col-6">
                                    <label for="">Check-in Setelah</label>
                                    <input type="text" class="form-control timepicker" name="jam_checkin" value="{{date('H:i', strtotime($data->jam_checkin))}}"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Check-out Sebelum</label>
                                    <input type="text" class="form-control timepicker" name="jam_checkout" value="{{date('H:i', strtotime($data->jam_checkout))}}"/>
                                </div>
                                @endif
                                @if($data->id_tipe_booking != 1 && $data->id_tipe_booking != 2 && $data->id_tipe_booking != 3 && $data->id_tipe_booking != 4)
                                <div class="col-12">
                                    <H4>Jam Operasional</H4><br>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Jam Mulai</label>
                                    <input type="text" class="form-control timepicker" name="jam_operasional_mulai" value="{{date('H:i', strtotime($data->jam_operasional_mulai))}}"/>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Jam Selesai</label>
                                    <input type="text" class="form-control timepicker" name="jam_operasional_selesai" value="{{date('H:i', strtotime($data->jam_operasional_selesai))}}"/>
                                </div>
                                @endif
                                <div class="form-group col-6">
                                    <label for="">Merokok Diperbolehkan</label>
                                    <select class="form-control js-example-basic-single" name="merokok" style="width:100%">
                                        <option value="0" {{($data->merokok == 0) ? 'selected' : ''}}>Tidak</option>
                                        <option value="1" {{($data->merokok == 1) ? 'selected' : ''}}>Ya</option>
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Hewan Peliharaan Diperbolehkan</label>
                                    <select class="form-control js-example-basic-single" name="binatang" style="width:100%">
                                        <option value="0" {{($data->binatang == 0) ? 'selected' : ''}}>Tidak</option>
                                        <option value="1" {{($data->binatang == 1) ? 'selected' : ''}}>Ya</option>
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Acara Diperbolehkan</label>
                                    <select class="form-control js-example-basic-single" name="acara" style="width:100%">
                                        <option value="0" {{($data->acara == 0) ? 'selected' : ''}}>Tidak</option>
                                        <option value="1" {{($data->acara == 1) ? 'selected' : ''}}>Ya</option>
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label for="">Anak Diperbolehkan</label>
                                    <select class="form-control js-example-basic-single" name="anak" style="width:100%">
                                        <option value="0" {{($data->anak == 0) ? 'selected' : ''}}>Tidak</option>
                                        <option value="1" {{($data->anak == 1) ? 'selected' : ''}}>Ya</option>
                                    </select>
                                </div>
                                <div class="form-group col-12">
                                    <label for="">Aturan Tambahan</label>
                                    <textarea class="form-control" name="aturan_tambahan" id="" style="height:130px;">{{$data->aturan_tambahan}}</textarea>
                                </div>
                            </div>                                                
                    </div>
                  </div>                    
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="{{asset('/')}}assets/js/tabs.js"></script>
<script src="{{asset('/')}}assets/vendors/tinymce/tinymce.min.js"></script>
<script src="{{asset('/')}}assets/js/dropify.js"></script>
<script type='text/javascript'>             
    tinymce.init({ selector:'textarea', menubar:'', theme: 'modern'});
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
            $(this).html('Sending..');
            var id = $('#id_ref_bahasa').val();
            var tanggal_mulai_periode = $('#tanggal_mulai_periode').val();
            var tanggal_selesai_periode = $('#tanggal_selesai_periode').val();
            var harga_periode = $('#harga_periode').val();
            var harga_tamu_periode = $('#harga_tamu_periode').val();
            var akhir_pekan_periode = $('#akhir_pekan_periode').val();
            var min_durasi_inap_periode = $('#min_durasi_inap_periode').val();
            // console.log(id+','+tanggal_mulai_periode);
            $.ajax({
                data: { id:id, 
                        tanggal_mulai_periode:tanggal_mulai_periode,
                        tanggal_selesai_periode:tanggal_selesai_periode,
                        harga_periode:harga_periode,
                        harga_tamu_periode:harga_tamu_periode,
                        akhir_pekan_periode:akhir_pekan_periode,
                        min_durasi_inap_periode:min_durasi_inap_periode,
                      },
                url: '{{ url("properti/periode-save") }}',
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('.simpan-periode').html('Simpan');
                    let html ='<tr>\
                                <td>'+data['start_date']+'</td>\
                                <td>'+data['end_date']+'</td>\
                                <td>'+data['durasi_inap']+'</td>\
                                <td>'+data['harga']+'</td>\
                                <td>'+data['harga_tamu_tambahan']+'</td>\
                                <td>'+data['harga_weekend']+'</td>\
                                <td><input type="button" class="btn btn-danger btn-sm" value="Hapus" onclick="myFunction(this,'+data['id_periode']+')"></td>\
                            </tr>';
                    $('.t-properti').append(html);

                    $('#tanggal_mulai_periode').val('');
                    $('#tanggal_selesai_periode').val('');
                    $('#harga_periode').val('');
                    $('#harga_tamu_periode').val('');
                    $('#akhir_pekan_periode').val('');
                    $('#min_durasi_inap_periode').val('');
                },
                error: function (data) {
                    console.log('Error:', data);
                    $('.simpan-periode').html('Simpan');
                }
            });
        });

    function myFunction(selectObject,id) {        
        $.ajax({
            data: { id:id},
            url: '{{ url("properti/periode-delete") }}',
            type: "GET",
            dataType: 'json',
            success: function (data) {    
                $(selectObject).parent().parent().remove();
            }
        });                

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
            var lati = document.getElementById('latitude').value;
            var long = document.getElementById('longitude').value;
          var latlng = new google.maps.LatLng(lati, long);
            var map = new google.maps.Map(document.getElementById('map'), {
                center: latlng,
                zoom: 15,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            var marker1 = new google.maps.Marker({
                position: latlng,
                map: map,
                title: '',
                draggable: true
            })
            google.maps.event.addListener(marker1, 'dragend', function(marker1) {
                var latLng = marker1.latLng;
                document.getElementById('latitude').value = latLng.lat();
                document.getElementById('longitude').value = latLng.lng();
            });
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
          autocomplete.addListener('place_changed', function() {            
            marker.setVisible(false);
            var place = autocomplete.getPlace();
            marker1.setMap(null);
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
                
            google.maps.event.addListener(marker, 'dragend', function(marker) {
                var latLng = marker.latLng;
                document.getElementById('latitude').value = latLng.lat();
                document.getElementById('longitude').value = latLng.lng();
            });

            marker.setPosition(place.geometry.location);
            marker.setVisible(true);
            document.getElementById('latitude').value = place.geometry.location.lat();
            document.getElementById('longitude').value = place.geometry.location.lng();
            
            var place = autocomplete.getPlace();
            // var inputValue = place.name + " " + place.formatted_address;
            // console.log(inputValue);
            document.getElementById('alamat').value = place.formatted_address;
          });
        }
        
      </script>    
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB-W-RsTAPM3gMXac5yEMIxNbip9mSEVuo&callback=initAutocomplete&libraries=places&v=weekly" defer></script>
@endpush