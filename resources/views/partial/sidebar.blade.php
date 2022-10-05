<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">            
            <a class="nav-link" href="">
                <i class="mdi mdi-view-dashboard-outline menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>            
        </li>
        <li><hr></li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#master" aria-expanded="false" aria-controls="master">
                <i class="mdi mdi-puzzle-outline menu-icon"></i>
                <span class="menu-title">Master</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="master">
                <ul class="nav flex-column sub-menu">                    
                    <li class="nav-item"> <a class="nav-link" href="{{url('/tipe-properti')}}">Tipe Properti</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{url('/tipe-booking')}}">Tipe Booking</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{url('/jenis-tempat')}}">Jenis Tempat</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{url('/akhir-pekan')}}">Akhir Pekan</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{url('/negara')}}">Negara</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{url('/provinsi')}}">Provinsi</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{url('/kota')}}">Kota</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{url('/fasilitas')}}">Fasilitas</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{url('/amenities')}}">Amenities</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{url('/promosi-kendaraan')}}">Promosi Kendaraan</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{url('/promosi-wisata')}}">Promosi Wisata</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{url('/concierge-service')}}">Concierge Sevice</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{url('/master-bank')}}">Bank</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{url('/bank-admin')}}">Bank Admin</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{url('/ads')}}">Ads</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{url('/kupon')}}">Kupon</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{url('/about-us')}}">About Us</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{url('/faq')}}">Faq</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{url('/term-condition')}}">Term Condition</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{url('/privacy-policy')}}">Privacy Policy</a></li>
                </ul>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#Properti" aria-expanded="false" aria-controls="Properti">
                <i class="mdi mdi-puzzle-outline menu-icon"></i>
                <span class="menu-title">Properti</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="Properti">
                <ul class="nav flex-column sub-menu">                    
                    <li class="nav-item"> <a class="nav-link" href="{{url('/properti-add')}}">Tambah Properti</a></li>
                    <li class="nav-item"> <a class="nav-link" href="{{url('/list-properti')}}">List Properti</a></li>
                </ul>
            </div>
        </li>
    </ul>
</nav>