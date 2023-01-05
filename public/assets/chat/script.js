const firebaseConfig = {
  apiKey: "AIzaSyAeVQgtpSjDS3024llPWjJit5IN0cR9dig",
  authDomain: "jetwing-b221e.firebaseapp.com",
  databaseURL: "https://jetwing-b221e-default-rtdb.firebaseio.com",
  projectId: "jetwing-b221e",
  storageBucket: "jetwing-b221e.appspot.com",
  messagingSenderId: "634264656122",
  appId: "1:634264656122:web:b64d366dcabda024809143",
  measurementId: "G-Z5QH2X5YNY"
};
  // Initialize Firebase
firebase.initializeApp(firebaseConfig);
var db = firebase.firestore();

console.log("Firebase connected");

function datachatlist() {
    
    db.collection("h_pesan").orderBy("waktu_pesan_terakhir","desc").onSnapshot(function (snapshot) {
        snapshot.docChanges().forEach(function (change, ind) {
            var data = change.doc.data();
            if (change.type == "added") {                                    
                var html = '<div class="mail-list klik" nama="baru" id_pesan="'+data.id_pesan+'" onmouseover="div_klik()" onclick="klik(this)">\
                <div class="content" id="div_'+data.id_pesan+'">\
                    <p class="sender-name">'+data.judul+'</p>\
                    <p class="message_text">'+data.pesan_terakhir+'</p>\
                    <p class="waktu">18:08</p>\
                    <div class="badge-cus"></div>\
                </div>\
                </div>';
                $("#div_list").append(html);
                $("#div_list #div_"+data.id_pesan).parents('.mail-list').hide().prependTo("#div_list").slideDown();
                $("#message_text_"+data.id_pesan).text(data.pesan);
            }

            if (change.type == "modified") {
                var html = '<div class="mail-list klik" nama="baru" id_pesan="'+data.id_pesan+'" onmouseover="div_klik()" onclick="klik(this)">\
                <div class="content" id="div_'+data.id_pesan+'">\
                    <p class="sender-name">'+data.judul+'</p>\
                    <p class="message_text">'+data.pesan_terakhir+'</p>\
                    <p class="waktu">18:08</p>\
                    <div class="badge-cus"></div>\
                </div>\
                </div>';
                $("#div_list").append(html);
                $("#div_list #div_"+data.id_pesan).parents('.mail-list').hide().prependTo("#div_list").slideDown();
                $("#message_text_"+data.id_pesan).text(data.pesan);
            }

            if (change.type == "removed") {
                
            }

        })

    })
    
}

function datachat(id_pesan) {
    db.collection("h_pesan_detail").where("id_pesan","==",id_pesan).onSnapshot(function (snapshot) {        
        snapshot.docChanges().forEach(function (change, ind) {
            var e = change.doc.data();
            // if new message added
            if (change.type == "added") {
                var mydate = new Date(e.created_date);                    
                    var date = moment(mydate).format('YYYY-MM-DD HH:mm:ss');
                    if (e.id_user == 1) {                        
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
                $('.list_chat').hide().slice(-5).show();
                
                if (snapshot.docChanges().length - 1 == ind) { // we will scoll down on last message
                    // auto scroll
                    $(".panel-body").animate({ scrollTop: $('.panel-body').prop("scrollHeight") }, 1000);
                }                
            }

            if (change.type == "modified") {
                
            }

            if (change.type == "removed") {

                $("#" + change.doc.id + "-message").html("this message has been deleted")

            }

        })
        nilai = snapshot.size - 5;
        awal = snapshot.size;
    })    
}

function sendMessage(object){
    console.log(object)
    db.collection("chats").add(object).then(added => {
        console.log("message sent ",added)
    }).catch(err => {
        console.err("Error occured",err)
    })

}

function deleteMessage(doc_id){
    var flag = window.confirm("Are you sure to want delete ?")

    if(flag){

        db.collection("chats").doc(doc_id).delete();
        console.log("Deleted");

    }
}

// on click function
$('.send-button').click(function(){

    var message = $('#btn-input').val();

    if(message){
        // insert message 

        sendMessage({
            senderName : name,
            message : message,
            date : moment().format("YYYY-MM-DD HH:mm")
        })

        $('#btn-input').val("")
    }

})

// also we will send message when user enter key
$('#btn-input').keyup(function(event) {

    // get key code of enter
    if(event.keyCode == 13){ // enter
       var message = $('#btn-input').val();

        if(message){
            // insert message 

            sendMessage({
                senderName : name,
                message : message,
                date : moment().format("YYYY-MM-DD HH:mm")
            })

            $('#btn-input').val("")
        }
    }
    // console.log("Key pressed");
})

