@push('css-app')
    <!-- <link href="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css"> -->
    <link rel="stylesheet" href="{{asset('/')}}assets/chat/style.css" />
@endpush
@extends('template')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="email-wrapper wrapper">
            <div class="row align-items-stretch">
                <div class="mail-list-container col-md-3 pt-4 pb-4 border-right bg-white">                    
                    <div class="mail-list klik" nama="David">
                        <div class="content">
                            <p class="sender-name">David Moore</p>
                            <p class="message_text">Hi Emily, Please be informed that the new project presentation is due Monday.</p>
                        </div>
                    </div>
                    <div class="mail-list klik" nama="Microsoft">
                        <div class="content">
                            <p class="sender-name">Microsoft Account Password Change</p>
                            <p class="message_text">Change the password for your Microsoft Account using the security code 35525</p>
                        </div>
                    </div>
                    <div class="mail-list klik" nama="halo">
                        <div class="content">
                            <p class="sender-name">Hallo</p>
                            <p class="message_text">hallll</p>
                        </div>
                    </div>
                </div>
                <div class="mail-view d-none d-md-block col-md-9 col-lg-9 bg-white" style="padding-right:5px!important;padding-left:5px!important">
                    <div class="card rounded-0">
                        <div class="card-header bg-primary text-white">
                            <span class="mdi mdi-comment"></span> Chat
                        </div>                    
                        <div class="card-body panel-body" style="padding:0!important">
                            <ul class="chat" style="background: #f3f3f9;">

                                    
                            </ul>
                        </div>
                        <div class="card-footer">
                            <div class="input-group mb-3">
                                <input id="btn-input" type="text" class="form-control input-sm" placeholder="Type your message here..." />
                                <div class="input-group-append">
                                    <button class="btn btn-warning btn-sm send-button" id="btn-chat">Send</button>
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
    @include("partial.footer")
    <!-- partial -->
</div>
@endsection
@push('js')
<script  src="https://www.gstatic.com/firebasejs/7.13.2/firebase-app.js"></script>
<script  src="https://www.gstatic.com/firebasejs/7.13.2/firebase-auth.js"></script>
<script  src="https://www.gstatic.com/firebasejs/7.13.2/firebase-firestore.js"></script>
<script  src="https://www.gstatic.com/firebasejs/7.13.2/firebase-storage.js"></script>
<script src="{{ asset('/') }}assets/chat/script.js"></script>
<script>
    $(document).ready(function() {
        $("div .klik").mouseover(function() {
            $(this).css("background",'#e6e9ed');
        });
        $("div .klik").mouseout(function() {
            $(this).removeAttr("style");
        });
    });

    $('.klik').click(function(){
        var namaChat = $(this).attr('nama');
        $('.chat').remove();

        $('.klik').removeClass('new_mail');
        $(this).addClass('new_mail');

        var html = '<ul class="chat" style="background: #f3f3f9;"> </ul>';
        $('.card-body').append(html);
        datachat(namaChat);
    });

</script>
@endpush