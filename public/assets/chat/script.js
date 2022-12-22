var firebaseConfig = {
    apiKey: "AIzaSyB-PX175HnPTIZrRi9mc3JZKXeVppmx93I",
    authDomain: "coba-baru-38348.firebaseapp.com",
    databaseURL: "https://coba-baru-38348-default-rtdb.firebaseio.com",
    projectId: "coba-baru-38348",
    storageBucket: "coba-baru-38348.appspot.com",
    messagingSenderId: "937390030210",
    appId: "1:937390030210:web:100d35521581cd2013239b",
    measurementId: "G-7PGG092Q9N"
  };
  // Initialize Firebase
firebase.initializeApp(firebaseConfig);
var db = firebase.firestore();

console.log("Firebase connected");
firebase
  .firestore()
  .collection("chats")
  .onSnapshot((snapshot) => {
    const data = snapshot.docs.map((doc) => ({
      id: doc.id,
      id: doc.data(),
    }));
    console.log("All data in 'books' collection", data);
  });

function datachat(name) {
    
    // get current username
    // var name = window.prompt("Enter your name");
    // var name = "halo";
    // var avatar = "{!! asset('assets/images/avatar.png') !!}";

    // Getting all message and listeing real time chat

    db.collection("chats").orderBy("date").onSnapshot(function (snapshot) {

        snapshot.docChanges().forEach(function (change, ind) {
            var data = change.doc.data();
            // if new message added
            if (change.type == "added") {
                if (data.senderName == name) { //Which message i sent 

                    var html = `<li class="left clearfix" style="width: 58%;border-radius: 10px 10px 0px 10px;background: #7fdbb4;margin-left: 40%;padding:10px">
                    <img src="assets/images/avatar.png" width="12%" alt="User Avatar" class="img-circle pull-left" style="margin-top:13px;margin-right:10px;"/>
                    <div class="chat-body clearfix">
                        <div class="header">
                            <strong class="primary-font">${data.senderName}</strong> <small class="pull-right text-muted">
                                <span class="mdi mdi-clock"></span>${data.date}</small>
                        </div>
                        <p id="${change.doc.id}-message">
                            ${data.message}
                        </p>
                        <span onclick="deleteMessage('${change.doc.id}')" class="mdi mdi-delete"></span> 
                    </div>
                </li>`;

                    $('.chat').append(html);

                } else {

                    var html = `<li class="clearfix" style="width: 60%;border-radius: 10px 10px 10px 0px;background: #fbfbfc; padding:10px">                    
                    <img src="assets/images/avatar.png" width="12%" alt="User Avatar" class="img-circle pull-right" style="margin-top:13px;margin-right:10px;"/>

                    <div class="chat-body clearfix">
                        <div class="header">
                            <small class=" text-muted">
                                <span class="mdi mdi-clock"></span>${data.date}</small>
                            <strong class="pull-right primary-font">${data.senderName}</strong>
                        </div>
                        <p id="${change.doc.id}-message">
                            ${data.message}
                        </p>
                        <span onclick="deleteMessage('${change.doc.id}')" class="mdi mdi-delete"></span> 
                    </div>
                </li>`;

                    $('.chat').append(html);

                }
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

