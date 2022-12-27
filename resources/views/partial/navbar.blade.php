<?php 
use App\Traits\Helper;
?>
<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo w-100"><img src="{{asset('/')}}assets/images/VILLANESIA.webp" alt="logo" /></a>
        <!-- <a class="navbar-brand brand-logo-mini" href="index.html"><img src="{{asset('/')}}assets/images/VILLANESIA.webp" alt="logo" /></a> -->
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-menu"></span>
        </button>
        <ul class="navbar-nav navbar-nav-right">                        
            
            <!-- <li class="nav-item dropdown">
                <a class="nav-link count-indicator dropdown-toggle" id="messageDropdown" href="#" data-toggle="dropdown"
                    aria-expanded="false">
                    <i class="mdi mdi-email-outline mx-0"></i>
                    <span class="count"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                    aria-labelledby="messageDropdown">
                    <div class="dropdown-item">
                        <p class="mb-0 font-weight-normal float-left">You have 7 unread mails
                        </p>
                        <span class="badge badge-info badge-pill float-right">View all</span>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <img src="https://via.placeholder.com/36x36" alt="image" class="profile-pic">
                        </div>
                        <div class="preview-item-content flex-grow">
                            <h6 class="preview-subject ellipsis font-weight-medium">David Grey
                                <span class="float-right font-weight-light small-text">1 Minutes ago</span>
                            </h6>
                            <p class="font-weight-light small-text mb-0">
                                The meeting is cancelled
                            </p>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <img src="https://via.placeholder.com/36x36" alt="image" class="profile-pic">
                        </div>
                        <div class="preview-item-content flex-grow">
                            <h6 class="preview-subject ellipsis font-weight-medium">Tim Cook
                                <span class="float-right font-weight-light small-text">15 Minutes ago</span>
                            </h6>
                            <p class="font-weight-light small-text mb-0">
                                New product launch
                            </p>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <img src="https://via.placeholder.com/36x36" alt="image" class="profile-pic">
                        </div>
                        <div class="preview-item-content flex-grow">
                            <h6 class="preview-subject ellipsis font-weight-medium"> Johnson
                                <span class="float-right font-weight-light small-text">18 Minutes ago</span>
                            </h6>
                            <p class="font-weight-light small-text mb-0">
                                Upcoming board meeting
                            </p>
                        </div>
                    </a>
                </div>
            </li> -->
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                    <img src="https://via.placeholder.com/30x30" alt="profile" />                    
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{Auth::user()->username}}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                    <h6 class="dropdown-header text-center">{{Auth::user()->username}}</h6>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{url('user/profile')}}">
                        <i class="mdi mdi-account-box-outline text-primary"></i>
                        Profile
                    </a>
                    <a class="dropdown-item" href="{{url('logout')}}">
                        <i class="mdi mdi-logout text-primary"></i>
                        Logout
                    </a>
                </div>
            </li>
            <!-- <li class="nav-item nav-settings d-none d-lg-block">
                <a class="nav-link" href="#">
                    <i class="mdi mdi-apps"></i>
                </a>
            </li> -->
        </ul>

    </div>
</nav>
