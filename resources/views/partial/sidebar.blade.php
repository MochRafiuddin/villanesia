<?php 
    use App\Traits\Helper;  
?>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">            
            <a class="nav-link" href="{{url('/dashboard')}}">
                <i class="mdi mdi-view-dashboard-outline menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>            
        </li>
        <li><hr></li>
        @if (Helper::can_akses('Master - Tipe Properti') != null || Helper::can_akses('Master - Negara') != null || Helper::can_akses('Master - Provinsi') != null || Helper::can_akses('Master - Kota') != null || Helper::can_akses('Master - Fasilitas') != null || Helper::can_akses('Master - Promosi Kendaraan') != null || Helper::can_akses('Master - Promosi Wisata') != null || Helper::can_akses('Master - Concierge Sevice') != null || Helper::can_akses('Master - Bank') != null || Helper::can_akses('Master - Bank Admin') != null || Helper::can_akses('Master - Ads') != null || Helper::can_akses('Master - Kupon') != null || Helper::can_akses('Master - About Us') != null || Helper::can_akses('Master - Faq') != null || Helper::can_akses('Master - Term Condition') != null || Helper::can_akses('Master - Privacy Policy') != null || Helper::can_akses('Master - Banner') != null)
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#master" aria-expanded="false" aria-controls="master">
                <i class="mdi mdi-puzzle-outline menu-icon"></i>
                <span class="menu-title">Master</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="master">
                <ul class="nav flex-column sub-menu">                    
                    @if (Helper::can_akses('Master - Tipe Properti'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/tipe-properti')}}">Tipe Properti</a></li>
                    @endif
                    <!-- <li class="nav-item"> <a class="nav-link" href="{{url('/tipe-booking')}}">Tipe Booking</a></li> -->
                    <!-- <li class="nav-item"> <a class="nav-link" href="{{url('/jenis-tempat')}}">Jenis Tempat</a></li> -->
                    <!-- <li class="nav-item"> <a class="nav-link" href="{{url('/akhir-pekan')}}">Akhir Pekan</a></li> -->
                    @if (Helper::can_akses('Master - Negara'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/negara')}}">Negara</a></li>
                    @endif
                    @if (Helper::can_akses('Master - Provinsi'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/provinsi')}}">Provinsi</a></li>
                    @endif
                    @if (Helper::can_akses('Master - Kota'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/kota')}}">Kota</a></li>
                    @endif
                    @if (Helper::can_akses('Master - Fasilitas'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/fasilitas')}}">Fasilitas</a></li>
                    @endif
                    @if (Helper::can_akses('Master - Amenities'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/amenities')}}">Amenities</a></li>
                    @endif
                    @if (Helper::can_akses('Master - Promosi Kendaraan'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/promosi-kendaraan')}}">Promosi Kendaraan</a></li>
                    @endif
                    @if (Helper::can_akses('Master - Promosi Wisata'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/promosi-wisata')}}">Promosi Wisata</a></li>
                    @endif
                    @if (Helper::can_akses('Master - Concierge Sevice'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/concierge-service')}}">Concierge Sevice</a></li>
                    @endif
                    @if (Helper::can_akses('Master - Bank'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/master-bank')}}">Bank</a></li>
                    @endif
                    @if (Helper::can_akses('Master - Bank Admin'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/bank-admin')}}">Bank Admin</a></li>
                    @endif
                    @if (Helper::can_akses('Master - Ads'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/ads')}}">Ads</a></li>
                    @endif
                    @if (Helper::can_akses('Master - Kupon'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/kupon')}}">Kupon</a></li>
                    @endif
                    @if (Helper::can_akses('Master - About Us'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/about-us')}}">About Us</a></li>
                    @endif
                    @if (Helper::can_akses('Master - Faq'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/faq')}}">Faq</a></li>
                    @endif
                    @if (Helper::can_akses('Master - Term Condition'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/term-condition')}}">Term Condition</a></li>
                    @endif
                    @if (Helper::can_akses('Master - Privacy Policy'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/privacy-policy')}}">Privacy Policy</a></li>
                    @endif
                    @if (Helper::can_akses('Master - Banner'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/banner')}}">Banner</a></li>
                    @endif
                </ul>
            </div>
        </li>
        @endif
        @if (Helper::can_akses('Properti - Tambah Properti') != null || Helper::can_akses('Properti - List Properti') != null)
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#Properti" aria-expanded="false" aria-controls="Properti">
                <i class="mdi mdi-puzzle-outline menu-icon"></i>
                <span class="menu-title">Properti</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="Properti">
                <ul class="nav flex-column sub-menu">
                    @if (Helper::can_akses('Properti - Tambah Properti'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/properti-add')}}">Tambah Properti</a></li>
                    @endif
                    @if (Helper::can_akses('Properti - List Properti'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/list-properti')}}">List Properti</a></li>
                    @endif
                </ul>
            </div>
        </li>
        @endif
        @if (Helper::can_akses('Booking - List Booking'))
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#Booking" aria-expanded="false" aria-controls="Booking">
                <i class="mdi mdi-puzzle-outline menu-icon"></i>
                <span class="menu-title">Booking</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="Booking">
                <ul class="nav flex-column sub-menu">                                        
                    @if (Helper::can_akses('Booking - List Booking'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/list-booking')}}">List Booking</a></li>
                    @endif
                </ul>
            </div>
        </li>
        @endif
        @if (Helper::can_akses('Messages'))
        <li class="nav-item">
            <a class="nav-link" href="{{url('/chat')}}">
                <i class="mdi mdi-puzzle-outline menu-icon"></i>
                <span class="menu-title">Messages</span>
                <i class="menu-arrow"></i>
            </a>            
        </li>
        @endif
        @if(Helper::can_akses('Setting - role') != null || Helper::can_akses('Setting - User') != null || Helper::can_akses('Setting - Data Setting') != null)
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#setting" aria-expanded="false" aria-controls="setting">
                <i class="mdi mdi-puzzle-outline menu-icon"></i>
                <span class="menu-title">Setting</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="setting">
                <ul class="nav flex-column sub-menu">                                        
                    @if (Helper::can_akses('Setting - Data Setting'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/setting')}}">Setting</a></li>
                    @endif
                    @if (Helper::can_akses('Setting - User'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/user')}}">User</a></li>
                    @endif
                    @if (Helper::can_akses('Setting - role'))
                    <li class="nav-item"> <a class="nav-link" href="{{url('/role')}}">Role</a></li>
                    @endif
                </ul>
            </div>
        </li>
        @endif
    </ul>
</nav>