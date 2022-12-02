@extends('app')
@section('content-app')
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth">
                <div class="row w-100">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left p-5">
                            <div class="brand-logo">
                                <img src="{{asset('/')}}assets/images/VILLANESIA.png" alt="logo">
                            </div>
                            <h4>Hello! let's get started</h4>
                            <h6 class="font-weight-light">Sign in to continue.</h6>
                            @if(session('success'))
                                <p class="alert alert-danger text-center">{{ session('success') }}</p>
                        	@endif
                            <form class="pt-3" action="{{url('auth')}}" method="post">
                                @csrf
                                <div class="form-group">
                                    <input type="text" name="username" class="form-control form-control-lg" placeholder="Username">
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control form-control-lg" placeholder="Password">
                                </div>
                                <div class="mt-3">
                                    <input type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn"
                                        value="SIGN IN"/>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->    
@endsection