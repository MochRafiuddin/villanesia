@push('css-app')
    <link rel="stylesheet" href="{{asset('/')}}assets/chat/style.css" />
    <style>

        .waktu {
            float: right;
            margin-top: -35px;
            margin-right: -60px;
        }

        .badge-cus {
            float: right;
            margin-top: -15px;
            margin-right: -60px;
            padding: 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.5rem;  
            background-color: #ff5e5e; 
        }

    </style>
@endpush
@extends('template')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="email-wrapper wrapper">
            <div class="row align-items-stretch">
                <div class="mail-list-container col-md-3 pt-4 pb-4 border-right bg-white panel-list">
                    <div class="border-bottom pb-4 px-3">
                      <h3>Messages</h3>
                    </div>
                    <div id="div_list">
                        
                    </div>
                </div>                
                <div class="card rounded-0 d-none d-md-block col-9 bg-white" style="padding-right:5px!important;padding-left:5px!important">
                    <div class="chat_view">
                    <div class="card-header bg-primary text-white">
                        <span class="mdi mdi-comment"></span> Chat
                    </div>                    
                    <div class="card-body panel-body" style="padding:0!important;background: #f3f3f9;">
                    <div class='more' style="width: 7%;border-radius: 10px 10px 10px 10px;background: #fbfbfc; padding:5px;margin-left: 44%;">More</div><br>
                        <ul class="chat" style="">
                            
                        </ul>
                    </div>
                    <div class="card-footer">
                        <div class="input-group mb-3">
                            <input id="btn-input" type="text" class="form-control input-sm" placeholder="Type your message here..." />
                            <div class="input-group-append">
                                <button class="btn btn-warning btn-sm send-buttons" id="btn-chats">Send</button>
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
        var nilai = 0;
        var awal = 0;
        var id_pesan = 0;
        $('.more').hide();
        $('.chat_view').hide();
        datachatlist();
        div_klik();
    });
    
    function div_klik(){

        $("div .klik").mouseover(function() {
            $(this).css("background",'#e6e9ed');
        });
        $("div .klik").mouseout(function() {
            $(this).removeAttr("style");
        });

    }
    function pindah(id_pesan,pesan){              
        $("#div_list #div_"+id_pesan).parents('.mail-list').hide().prependTo("#div_list").slideDown();
        $("#message_text_"+id_pesan).text(pesan);
    }

    function klik(as){
        $('.more').hide();
        $('.chat_view').show();

        nilai = 0;
        awal = 0;

        var namaChat = $(as).attr('nama');
        id_pesan = parseInt($(as).attr('id_pesan'));
        // console.log(id_pesan);
        $('.chat').remove();

        $('.klik').removeClass('new_mail');
        $(as).addClass('new_mail');

        var html = '<ul class="chat"> </ul>';
        $('.card-body').append(html);
        var data = datachat(id_pesan);
        // $('.list_chat').hide().slice(-5).show();
        console.log(data);
    };

    $('.send-buttons').click(function(){        
        var pesan = $('#btn-input').val();
        $.ajax({
            url: "{{url('chat/tambah-chat-detail')}}",
            type: "post",
            data: { 
                id_pesan:id_pesan,
                pesan:pesan,
            },            
            success: function(e){
                $(".panel-body").animate({ scrollTop: $('.panel-body').prop("scrollHeight") }, 100);
                $('#btn-input').val('');
                pindah(e.data.id_pesan,e.data.pesan);
            }
        });
    });

    $('.panel-body').scroll(function() {
        // console.log($(this).scrollTop());
        if ($(this).scrollTop() == 0) {
            $('.more').show();
        }else{
            $('.more').hide();
        }
        if (nilai == 0) {
            $('.more').hide();
        }
    });
    $('.more').click(function(){        
        nilai = nilai - 5;
        if (nilai <= 0) {
            nilai = 0;
        }
        if (nilai == 0) {
            $('.more').hide();
        }
        $('.list_chat').slice(nilai,awal).show();
    });
</script>
@endpush