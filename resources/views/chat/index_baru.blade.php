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
                        @foreach($pesan as $key => $p)
                        <div class="mail-list klik" nama="David" id_pesan="{{$p->id_pesan}}">
                            <div class="content" id="div_{{$p->id_pesan}}">
                                <p class="sender-name">{{$p->judul}}</p>
                                <p class="message_text" id="message_text_{{$p->id_pesan}}">{{$p->pesan_terakhir}}</p>
                            </div>
                        </div>
                        @endforeach
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
                <!-- <div class="col-md-12">
                    <button class="btn btn-danger btn-sm" id="btn-coba" onclick="pindah()">pindah</button>
                    <button class="btn btn-default btn-sm" id="btn-coba" onclick="insert_baru()">Insert baru</button>
                    <button class="btn btn-warning btn-sm" id="btn-coba" onclick="insert_detail()">Insert detail saja</button>
                </div> -->
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
    function pindah(id_pesan,pesan){              
        $("#div_list #div_"+id_pesan).parents('.mail-list').hide().prependTo("#div_list").slideDown();
        $("#message_text_"+id_pesan).text(pesan);
    }

    $('.klik').click(function(){
        $('.more').hide();
        $('.chat_view').show();

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
                nilai = res.data.length - 5;
                awal = res.data.length;
                res.data.forEach(function(data){
                    pushJsonDetail(data);
                });
                // console.log(data_list_detail);
                res.data.forEach(e => {
                    var mydate = new Date(e.created_date);                    
                    var date = moment(mydate).format('YYYY-MM-DD HH:mm:ss');
                    if (e.id_user ==1) {                        
                        var html = '<li class="left clearfix list_chat" style="width: 58%;border-radius: 10px 10px 0px 10px;background: #7fdbb4;margin-left: 40%;padding:10px">\
                            <img src="assets/images/avatar.png" width="12%" alt="User Avatar" class="img-circle pull-left" style="margin-top:13px;margin-right:10px;"/>\
                        <div class="chat-body clearfix">\
                            <div class="header">\
                                <strong class="primary-font">'+e.nama_depan+' '+e.nama_belakang+'</strong> <small class="pull-right text-muted">\
                                <span class="mdi mdi-clock"></span>'+date+'</small>\
                            </div>\
                            <p id="'+e.id_pesan_detail+'">\
                                '+e.pesan+'\
                            </p>\
                            <span onclick="deleteMessage('+e.id_pesan_detail+')" class="mdi mdi-delete"></span>\
                        </div>\
                        </li>';
                        
                        $('.chat').append(html);
                    }else {

                        var html = '<li class="clearfix list_chat" style="width: 60%;border-radius: 10px 10px 10px 0px;background: #fbfbfc; padding:10px">\
                        <img src="assets/images/avatar.png" width="12%" alt="User Avatar" class="img-circle pull-right" style="margin-top:13px;margin-right:10px;"/>\
                        <div class="chat-body clearfix">\
                            <div class="header">\
                                <small class=" text-muted">\
                                    <span class="mdi mdi-clock"></span>'+date+'</small>\
                                <strong class="pull-right primary-font">'+e.nama_depan+' '+e.nama_belakang+'</strong>\
                            </div>\
                            <p id="'+e.id_pesan_detail+'-message">\
                                '+e.pesan+'\
                            </p>\
                            <span onclick="deleteMessage('+e.id_pesan_detail+')" class="mdi mdi-delete"></span>\
                        </div>\
                        </li>';

                        $('.chat').append(html);

                    }
                    $(".panel-body").animate({ scrollTop: $('.panel-body').prop("scrollHeight") }, 250);
                    $('.list_chat').hide().slice(-5).show();
                });                
                load_kanan();
            }
        });
    });

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
                var mydate = new Date(e.data.created_date);                    
                var date = moment(mydate).format('YYYY-MM-DD HH:mm:ss');
                var html = '<li class="left clearfix list_chat" style="width: 58%;border-radius: 10px 10px 0px 10px;background: #7fdbb4;margin-left: 40%;padding:10px">\
                <img src="assets/images/avatar.png" width="12%" alt="User Avatar" class="img-circle pull-left" style="margin-top:13px;margin-right:10px;"/>\
                <div class="chat-body clearfix">\
                    <div class="header">\
                        <strong class="primary-font">'+e.data.nama_depan+' '+e.data.nama_belakang+'</strong> <small class="pull-right text-muted">\
                        <span class="mdi mdi-clock"></span>'+date+'</small>\
                    </div>\
                    <p id="'+e.data.id_pesan_detail+'">\
                        '+e.data.pesan+'\
                    </p>\
                    <span onclick="deleteMessage('+e.data.id_pesan_detail+')" class="mdi mdi-delete"></span>\
                </div>\
                </li>';
                pushJsonDetail(e.data);
                $('.chat').append(html);
                $(".panel-body").animate({ scrollTop: $('.panel-body').prop("scrollHeight") }, 100);
                $('#btn-input').val('');
                pindah(e.data.id_pesan,e.data.pesan);
            }
        });
    });

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
        nilai = nilai - 5;
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
        let len =  data_list_detail.length - 1 ;
        console.log(data_list_detail[len]);        
    db.collection("h_pesan_detail")
    .where('id_pesan_detail','>',data_list_detail[len]['id_pesan_detail'])
    .where('id_pesan','==',id_pesan)
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
            $("#div_list #div_"+tampung_id+" .message_text").html(data.pesan_terakhir);
            $("#div_list #div_"+tampung_id).parents('.mail-list').hide().prependTo("#div_list").slideDown();
        }else{
            //diappend
            var html = '<div class="mail-list klik" nama="baru">\
                <div class="content" id="div_'+tampung_id+'">\
                    <p class="sender-name">'+data.judul+'</p>\
                    <p class="message_text">'+data.pesan_terakhir+'</p>\
                    <p class="waktu">18:08</p>\
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
        console.log(data.pesan_terakhir);
        
        //diappend
        if(tampung_id_user != 1){
            var html = '<li class="clearfix" style="width: 58%;border-radius: 10px 10px 10px 0px;background: #fbfbfc; padding:10px">\
                <img src="assets/images/avatar.png" width="12%" alt="User Avatar" class="img-circle pull-right" style="margin-top:13px;margin-right:10px;"/>\
                <div class="chat-body clearfix">\
                    <div class="header">\
                        <small class=" text-muted">\
                            <span class="mdi mdi-clock"></span>'+waktu+'</small>\
                        <strong class="pull-right primary-font">User 4</strong>\
                    </div>\
                    <p id="'+id_pesan_detail+'-message">'+pesan+'</p>\
                </div>\
            </li>';
            $('.chat').append(html);
            $(".panel-body").animate({ scrollTop: $('.panel-body').prop("scrollHeight") }, 1000);
            console.log("di append h_pesan_detail");
            data_list_detail.push(data);
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