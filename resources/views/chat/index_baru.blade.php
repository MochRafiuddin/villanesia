@push('css-app')
    <link rel="stylesheet" href="{{asset('/')}}assets/chat/style.css" />
    <style>

        .waktu {
            float: right;
            margin-top: -35px;
            margin-right: -50px;
            font-size: 13px;
        }

        .badge-cus {
            float: right;
            margin-top: -15px;
            margin-right: -50px;
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
                        @foreach($pesan as $key => $p)
                        <div class="mail-list klik" nama="David" id_pesan="{{$p->id_pesan}}">
                            <div class="content" id="div_{{$p->id_pesan}}">
                                <p class="sender-name">{{$p->judul}}</p>
                                <p class="message_text" id="message_text_{{$p->id_pesan}}">{{$p->pesan_terakhir}}</p>
                                <div class="waktu">
                                    @if(date('Y-m-d',strtotime($p->waktu_pesan_terakhir)) < date('Y-m-d'))
                                    {{date('d-m-y',strtotime($p->waktu_pesan_terakhir))}}
                                    @else
                                    {{date('H:i',strtotime($p->waktu_pesan_terakhir))}}
                                    @endif
                                </div>
                                @if($p->penerima_lihat == 0)
                                    <div class="badge-cus"></div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>                
                <div class="card rounded-0 d-none d-md-block col-9 bg-white" style="padding-right:5px!important;padding-left:5px!important">
                    <div class="chat_view">
                    <div class="card-header bg-primary text-white">
                        <span class="mdi mdi-comment"></span><span class="title_chat"> Chat</span>
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
                                <button class="btn btn-warning btn-sm send-buttons" id="btn-chats">
                                    Send
                                </button>
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
        div_klik();
        fungsi_div_klik();
        $('.more').hide();
        $('.chat_view').hide();
        var Json_pesan = <?php echo json_encode($pesan); ?>;
        Json_pesan.forEach(function(data){
            pushJson(data);
        });
        load_kiri();
        // load_kanan(0);
    });
    var nilai = 0;
    var awal = 0;
    var id_pesan = 0;
    let len = 0;

    const data_list_awal = [];

    var data_list_detail = [];

    function pushJson(data){
        data_list_awal.push({
            id_pesan:data.id_pesan,
            judul:data.judul,
            pesan_terakhir:data.pesan_terakhir,            
        });
    }

    function pushJsonDetail(data){
        var mydate = new Date(data.created_date);
        var date = moment(mydate).format('YYYY-MM-DD HH:mm:ss');
        data_list_detail.push({
            id_pesan:data.id_pesan,
            id_pesan_detail:data.id_pesan_detail,
            id_ref:data.id_ref,
            id_tipe:data.id_tipe,
            pesan:data.pesan,
            waktu:date,
            id_user:data.id_user,
            nama_depan:data.nama_depan,
            nama_belakang:data.nama_belakang,
            foto:data.nama_foto,
        });
    }

    function div_klik(){

        $("div .klik").mouseover(function() {
            $(this).css("background",'#e6e9ed');
        });
        $("div .klik").mouseout(function() {
            $(this).removeAttr("style");
        });

    }
    function pindah(id_pesan,pesan,waktu_pesan_terakhir){          
        var mydate = new Date(waktu_pesan_terakhir);                    
        var date = moment(mydate).format('HH:mm');    
        $("#div_list #div_"+id_pesan).parents('.mail-list').hide().prependTo("#div_list").slideDown();
        $("#message_text_"+id_pesan).text(pesan);
        $("#div_list #div_"+id_pesan).append("<div class='badge-cus'></div>");
        $("#div_list #div_"+id_pesan+" .waktu").text(date);
    }

    function fungsi_div_klik(){
        $('.klik').click(function(){
            $('.more').hide();       
            $(this).find('.badge-cus').remove(); 

            nilai = 0;
            awal = 0;

            data_list_detail = [];

            var namaChat = $(this).attr('nama');
            id_pesan = $(this).attr('id_pesan');
            // console.log(id_pesan);

            $('.chat').remove();

            $('.klik').removeClass('new_mail');
            $(this).addClass('new_mail');

            var html = '<ul class="chat"> </ul>';
            $('.card-body').append(html);
            // datachat(namaChat);

            $.ajax({
                url: "{{url('chat/get-chat')}}/"+id_pesan,
                type: "GET",            
                success: function(res){
                    $('.chat_view').show();
                    var url = '{{url("booking/detail")}}/'+res.title.id_ref;
                    var head_chat="<a href='"+url+"' class='text-white'> "+res.title.id_ref+'</a>'+' - '+res.title.nama_depan+' '+res.title.nama_belakang;
                    $('.title_chat').html(head_chat);
                    nilai = res.data.length - 10;
                    awal = res.data.length;
                    res.data.forEach(function(data){
                        pushJsonDetail(data);
                    });
                    // console.log(data_list_detail);
                    res.data.forEach(e => {
                        var mydate = new Date(e.created_date);                    
                        var date = moment(mydate).format('YYYY-MM-DD HH:mm:ss');
                        var foto ='';
                        if (e.nama_foto != null) {
                            var foto ='{{asset("upload/profile_img/")}}/'+e.nama_foto;
                        }else{                        
                            var foto ='{{asset("assets/images/avatar.png")}}';
                        }
                        if (e.id_user ==1) {
                                var html = '<li class="left clearfix list_chat" style="width: 50%;border-radius: 10px 10px 0px 10px;background: #7fdbb4;margin-left: 49%;padding:10px">\
                                <div class="chat-body clearfix text-right">\
                                    <div class="header">\
                                        <small><span class="mdi mdi-clock"></span>'+date+'</small>\
                                    </div>\
                                    <p id="'+e.id_pesan_detail+'">\
                                        '+e.pesan+'\
                                    </p>\
                                </div>\
                                </li>';
                                $('.chat').append(html);
                        }else {
                            if (e.id_tipe == 2) {
                                var html = '<li class="text-center clearfix list_chat" style="width: 32%;border-radius: 10px 10px 10px 10px;background: #c6cbdf;margin-left: 35%;padding:5px">\
                                    <small class="">\
                                        Admin has confirmed the availability of the villa\
                                    </small>\
                                </li>';
                                $('.chat').append(html);
                            }else{
                            var html = '<li class="clearfix list_chat" style="width: 50%;border-radius: 10px 10px 10px 0px;background: #fbfbfc; padding:10px">\
                            <div class="chat-body clearfix">\
                                <div class="header">\
                                    <small class=" text-muted">\
                                        <span class="mdi mdi-clock"></span>'+date+'</small>\
                                </div>\
                                <p id="'+e.id_pesan_detail+'-message">\
                                    '+e.pesan+'\
                                </p>\
                            </div>\
                            </li>';

                            $('.chat').append(html);
                            }
                        }
                        $(".panel-body").animate({ scrollTop: $('.panel-body').prop("scrollHeight") }, 250);
                        $('.list_chat').hide().slice(-10).show();
                    });                
                    load_kanan();
                }
            });
        });
    }

    $('#btn-input').keypress(function(e){
        if(e.which == 13){
            kirim_pesan();
        }
    });

    $('.send-buttons').click(function(){        
        kirim_pesan();
    });

    function kirim_pesan(){
        var pesan = $('#btn-input').val();        
        $.ajax({
            url: "{{url('chat/tambah-chat-detail')}}",
            type: "post",
            data: { 
                id_pesan:id_pesan,
                pesan:pesan,
            },            
            success: function(e){
                var mydate = new Date(e.data.created_date);                    
                var date = moment(mydate).format('YYYY-MM-DD HH:mm:ss');
                    if (e.data.nama_foto != null) {
                        var foto ='{{asset("upload/profile_img")}}/'+e.data.nama_foto;
                    }else{
                        var foto ='{{asset("assets/images/avatar.png")}}';
                    }
                var html = '<li class="left clearfix list_chat" style="width: 50%;border-radius: 10px 10px 0px 10px;background: #7fdbb4;margin-left: 49%;padding:10px">\
                <div class="chat-body clearfix text-right">\
                    <div class="header">\
                        <small><span class="mdi mdi-clock"></span>'+date+'</small>\
                    </div>\
                    <p id="'+e.data.id_pesan_detail+'">\
                        '+e.data.pesan+'\
                    </p>\
                </div>\
                </li>';
                pushJsonDetail(e.data);
                $('.chat').append(html);
                $(".panel-body").animate({ scrollTop: $('.panel-body').prop("scrollHeight") }, 100);
                $('#btn-input').val('');
                pindah(e.data.id_pesan,e.data.pesan,e.created_date);
            }
        });
    }

    $('.panel-body').scroll(function() {
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
        nilai = nilai - 10;
        if (nilai <= 0) {
            nilai = 0;
        }
        if (nilai == 0) {
            $('.more').hide();
        }
        $('.list_chat').slice(nilai,awal).show();
    });

    function sendMessage_update(object, table){
        console.log(object)
        db.collection(table).add(object).then(added => {
            console.log("message sent ",added)
        }).catch(err => {
            console.err("Error occured",err)
        })

    }

    db.collection("h_pesan").where('id_pesan','==',id_pesan).get().then((querySnapshot) => {
        querySnapshot.forEach((doc) => {
            var doc_id = `${doc.id}`;
            var doc_type = `${doc.type}`;
            console.log(doc_id+" --- "+doc_type);
            db.collection("h_pesan").doc(doc_id).update({pesan_terakhir: pesan_kirim, waktu_pesan_terakhir: moment().format("YYYY-MM-DD HH:mm:ss")});
            //console.log(`${doc.id} => ${doc.data()}`);
        });
    });
        //.doc("3BCDGaz11uUaDfEPlEkH")
    function load_kiri(){
        db.collection("h_pesan").where('id_user_penerima','==',1).onSnapshot(onResult, onError);
        function onResult(QuerySnapshot) {  
            if (QuerySnapshot == null) {
            return
            }
            QuerySnapshot.docChanges().forEach(change => {
            if (change === undefined) {
                return
            }
              console.log(change);
            if(change.type == 'modified'){
                update_chat_list(change.doc.data());
                console.log(change.doc.id+" --- "+change.doc.data().waktu_pesan_terakhir);
                // update_chat(change.doc.data());
            } else if(change.type == 'added'){
                console.log(change.doc.id+" --- "+change.doc.data().waktu_pesan_terakhir);
            }
            
            })
        }
    }
    function load_kanan(){        
        len =  data_list_detail.length - 1 ;
        console.log(data_list_detail[len]);        
        db.collection("h_pesan_detail")
        .where('id_pesan_detail','>',data_list_detail[len]['id_pesan_detail'])
        .where('id_pesan','==',parseInt(id_pesan))
        .orderBy('id_pesan_detail','desc')
        .limit(1).onSnapshot(onResultDetail, onErrorDetail);
        function onResultDetail(QuerySnapshot) {  
            if (QuerySnapshot == null) {
              return
            }
            QuerySnapshot.docChanges().forEach(change => {
              if (change === undefined) {
                return
              }
              console.log(change.doc.data());
              console.log(change.type);
              if(change.type == 'added'){
                update_chat_detail(change.doc.data());
                console.log(change.doc.id+" --- h_pesan_detail "+change.doc.data().waktu_pesan_terakhir);
                // update_chat(change.doc.data());
              } else if(change.type == 'modified'){
                console.log(change.doc.id+" --- h_pesan_detail "+change.doc.data().waktu_pesan_terakhir);
              }
              
            })
        }
    }

    function update_chat_list(data){

        var tampung_id = data.id_pesan;
        // console.log(doc.doc.data().pesan_terakhir);
        var hasil_search = data_list_awal.find(o => o.id_pesan === tampung_id); 
        if(hasil_search){
            pindah(data.id_pesan,data.pesan_terakhir,data.waktu_pesan_terakhir);
        }else{
            //diappend
            var html = '<div class="mail-list klik" nama="baru" id_pesan="'+tampung_id+'">\
                <div class="content" id="div_'+tampung_id+'">\
                    <p class="sender-name">'+data.judul+'</p>\
                    <p class="message_text">'+data.pesan_terakhir+'</p>\
                    <div class="badge-cus"></div>\
                </div>\
            </div>';
            $("#div_list").append(html);
            $("#div_list #div_"+tampung_id).parents('.mail-list').hide().prependTo("#div_list").slideDown();
            console.log("di append");
            var data_new = { "id_pesan":tampung_id, "judul":data.judul, "pesan_terakhir": data.pesan_terakhir };
            data_list_awal.push(data_new);
            div_klik();
            fungsi_div_klik();
        }

    }
    function update_chat_detail(data){

        var tampung_id_user = data.id_user;
        var waktu = data.created_date;
        var pesan = data.pesan;
        var id_pesan_detail = data.id_pesan_detail;
        var nama_depan = '';
        var nama_belakang = '';
        var foto = '';
        console.log(data.pesan);

        $.each(data_list_detail, function(i, v) {
            if (v.id_user == tampung_id_user) {
                nama_depan = v.nama_depan;
                nama_belakang = v.nama_belakang;
                foto = v.foto;
                // console.log(v);
            }
        });

        if (foto != null) {
            var foto ='{{asset("upload/profile_img")}}/'+foto;
        }else{
            var foto ='{{asset("assets/images/avatar.png")}}';
        }
        
        //diappend
        if(tampung_id_user != 1){
            if (data.id_tipe == 2) {
                var html = '<li class="text-center clearfix list_chat" style="width: 32%;border-radius: 10px 10px 10px 10px;background: #c6cbdf;margin-left: 35%;padding:5px">\
                    <small class="">\
                        Admin has confirmed the availability of the villa\
                    </small>\
                </li>';
                $('.chat').append(html);
                pindah(tampung_id_user,pesan,data.created_date)
            }else{
            var html = '<li class="clearfix" style="width: 50%;border-radius: 10px 10px 10px 0px;background: #fbfbfc; padding:10px">\
                <div class="chat-body clearfix">\
                    <div class="header">\
                        <small class=" text-muted">\
                            <span class="mdi mdi-clock"></span>'+waktu+'</small>\
                    </div>\
                    <p id="'+id_pesan_detail+'-message">'+pesan+'</p>\
                </div>\
            </li>';
            $('.chat').append(html);
            $(".panel-body").animate({ scrollTop: $('.panel-body').prop("scrollHeight") }, 1000);
            }
            console.log("di append h_pesan_detail");
            data_list_detail.push(data);
            len =  data_list_detail.length - 1 ;
        }        

    }    
    function onError(QuerySnapshot){
        if(QuerySnapshot !== null){
            console.log("Error "+QuerySnapshot);
        }

    }

    function onErrorDetail(QuerySnapshot){
        if(QuerySnapshot !== null){
            console.log("Error detail"+QuerySnapshot);
        }

    }
</script>

@endpush