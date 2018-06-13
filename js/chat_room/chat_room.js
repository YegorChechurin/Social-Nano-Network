function form_chats_bar() {
        $.get("http://localhost/SNN/ajax/"+user_id+"/chats", 
            function(data, status){
                if (status=="success") {
                    chats = JSON.parse(data);
                    $("#chats_wrapper").html('');
                    chats.forEach(form_chat_header);
                }
            }
        );
    }

function form_chat_header(chat) {
        if (chat.partner_id==active_id) {
            var id = 'c'+chat.partner_id; 
            $("#chats_wrapper").append('<div id="'+id+'"></div>');
            $("#"+id).attr('class','active_chat_header text-truncate');
            //$("#"+id).attr('onclick','display_chat('+chat.partner_id+','+chat.partner_name+')');
            $("#"+id).click(function(){
                display_chat(chat.partner_id,chat.partner_name);
            });
            $("#"+id).html('<b>'+chat.partner_name+'</b><br>'+
            chat.last_mes_auth_name+': '+chat.last_mes_text);
            $("#"+id).append('<div id="unread_'+id+'" style="position:absolute; top:0; left:50%; color:red"><b>UNREAD</b></div>');
            var parsed_id = parseInt(active_id);
            register_chat_read(parsed_id);
            mark_chat_read(parsed_id);
        } else {
            var id = 'c'+chat.partner_id; 
            $("#chats_wrapper").append('<div id="'+id+'"></div>');
            $("#"+id).attr('class','chat_header text-truncate');
            //$("#"+id).attr('onclick','display_chat('+chat.partner_id+','+chat.partner_name+')');
            $("#"+id).click(function(){
                display_chat(chat.partner_id,chat.partner_name);
            });
            $("#"+id).html('<b>'+chat.partner_name+'</b><br>'+
            chat.last_mes_auth_name+': '+chat.last_mes_text);
            $("#"+id).append('<div id="unread_'+id+'" style="position:absolute; top:0; left:50%; color:red"><b>UNREAD</b></div>');
            var parsed_id = parseInt(chat.partner_id);
            ts = Date.parse(chat.last_mes_ts);
            check_chat(parsed_id,ts);
            mark_chat(parsed_id);
        }
    }

function display_chat(partner_id,partner_name) { 
        n = 0;
        active_id = partner_id;  
        active_name = partner_name;
        $.get("http://localhost/SNN/ajax/"+user_id+"/chat/"+partner_id, 
            function(data, status){
                if (status=="success") {
                    $("#mes").html('')
                    var messages = JSON.parse(data);
                    messages.forEach(display_message);
                }
            }
        );
        var id = parseInt(active_id);
        register_chat_read(id);
        mark_chat_read(id);
        chats.forEach(function(item){
            if (item.partner_id==active_id) {
                $("#c"+active_id).attr("class","active_chat_header text-truncate");
            } else {
                $("#c"+item.partner_id).attr("class","chat_header text-truncate");
                mark_chat(item.partner_id);
            }
        });
    }

function display_message(message) {
        if (active_id && (message.sender_id==active_id || message.recipient_id==active_id)) {
            n++;
            if (message.sender_id == user_id) {
                var content = '<div class="message_outlet" id="m'+n+'"><b>You:</b> '+message.message+'</div><br>';
                $("#mes").append(content);
            } else {
                var content = '<div class="message_inlet" id="m'+n+'"><b>'+message.sender_name+':</b> '+message.message+'</div><br>';
                $("#mes").append(content);
            }
            var last_mes_pos = document.getElementById("m"+n).offsetTop;
            if (last_mes_pos > mes_height) {
                document.getElementById("mes").scrollTop = last_mes_pos; 
            }
        } else {
            alert("You received new message from "+message.sender_name);
        }
    }

function send_message() {
        var text = $("#text").val();
        $.post("http://localhost/SNN/ajax/"+user_id+"/messages",
            {user_name:user_name, partner_id:active_id, partner_name:active_name, message:text}, 
            function(data, status){
                if (status=="success") {
                    $("#text").val('');
                    var message = {
                        "sender_id":user_id,
                        "sender_name":user_name,
                        "recipient_id":active_id,
                        "message":text
                    };
                    display_message(message);
                    form_chats_bar();
                    register_last_mes_ts();
                }
            }
        );
    }

// cookies https://github.com/js-cookie/js-cookie

function register_chat_unread(chat_partner_id) {
    var unread_chats = Cookies.getJSON('unread_chats');
    if (unread_chats) {
        var index = unread_chats.indexOf(chat_partner_id);
        if (index==-1) {
            unread_chats.push(chat_partner_id);
            Cookies.set('unread_chats', unread_chats, {expires:365});
        }
    } else {
        Cookies.set('unread_chats', [chat_partner_id], {expires:365});
    }
}

function register_chat_read(chat_partner_id) {
    var unread_chats = Cookies.getJSON('unread_chats');
    if (unread_chats) {
        var index = unread_chats.indexOf(chat_partner_id);
        if (index>-1) {
            unread_chats.splice(index, 1);
            Cookies.set('unread_chats', unread_chats, {expires:365});
        }
    }
}

function register_last_mes_ts() {
    var date = new Date();
    var last_mes_ts = date.getTime();
    Cookies.set('last_mes_ts', last_mes_ts, {expires:365});
}

function check_chat(chat_partner_id,ts) {
    var saved_ts = Cookies.getJSON('last_mes_ts');
    if (ts>saved_ts) {
        register_chat_unread(chat_partner_id);
        Cookies.set('last_mes_ts', ts, {expires:365});
    }
}

function mark_chat_read(chat_partner_id) {
    $("#unread_c"+chat_partner_id).attr('class','invisible');
}

function mark_chat_unread(chat_partner_id) {
    $("#unread_c"+chat_partner_id).attr('class','visible');
    $("#c"+chat_partner_id).attr('class','chat_header_unread text-truncate');
}

function mark_chat(chat_partner_id) {
    var unread_chats = Cookies.getJSON('unread_chats');
    if (unread_chats) {
        var index = unread_chats.indexOf(chat_partner_id);
        if (index>-1) {
            mark_chat_unread(chat_partner_id);
        } else {
            mark_chat_read(chat_partner_id);
        }
    } else {
        mark_chat_read(chat_partner_id);
    }
}

