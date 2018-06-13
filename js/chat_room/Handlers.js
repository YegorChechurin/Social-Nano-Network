function MessagesHandler() {

	this.handle_incoming_messages = function(messages){
		var last_index = messages.length;
        last_rec_mes_id = parseInt(messages[last_index-1].message_id);
        this.register_last_mes_ts();
		messages.forEach(this.display_message);
		messages.forEach(this.register);
		var h = new ChatsBarHandler();
		h.form_chats_bar();
	}

	this.handle_sent_message = function(message){
		this.display_message(message);
        this.register_last_mes_ts();
        var h = new ChatsBarHandler();
		h.form_chats_bar();
	}

	this.display_message = function(message){
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

	this.register_last_mes_ts = function(){
		var date = new Date();
        var last_mes_ts = date.getTime();
        Cookies.set('last_mes_ts', last_mes_ts, {expires:365});
	}

	this.register = function(message){
		if (active_id) {
			if (message.sender_id!=active_id) {
                var id = parseInt(message.sender_id);
				register_unread(id);
			}
		} else {
            var id = parseInt(message.sender_id);
			register_unread(id);
		}
	}

	var register_unread = function(chat_partner_id){
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

} 


function ChatsBarHandler(){

	this.form_chats_bar = function(){
		$.get("http://localhost/SNN/ajax/"+user_id+"/chats", 
            function(data, status){
                if (status=="success") {
                    chats = JSON.parse(data);
                    form_chats_bar();
                }
            }
        );
	}

	var form_chats_bar = function(){
        if (chats) {
            $("#chats_wrapper").html('');
            chats.forEach(form_chat_header);
        }
	}

	var form_chat_header = function(chat){
            var id = 'c'+chat.partner_id; 
            $("#chats_wrapper").append('<div id="'+id+'"></div>');
            $("#"+id).click(function(){
                display_chat(chat.partner_id,chat.partner_name);
            });
            $("#"+id).html('<b>'+chat.partner_name+'</b><br>'+
            chat.last_mes_auth_name+': '+chat.last_mes_text);
            $("#"+id).append('<div id="unread_'+id+'" style="position:absolute; top:0; left:50%; color:red"><b>UNREAD</b></div>');
            var parsed_id = parseInt(chat.partner_id);
            if (chat.partner_id==active_id) {
                $("#"+id).attr('class','active_chat_header text-truncate');
                register_read(parsed_id);
                mark_chat_read(parsed_id);
            } else { 
                $("#"+id).attr('class','chat_header text-truncate');
                ts = Date.parse(chat.last_mes_ts);
                check_chat(parsed_id,ts);
                mark_chat(parsed_id);
            }
	}

	var display_chat = function(partner_id,partner_name){
		n = 0;
        active_id = partner_id;  
        active_name = partner_name;
        $.get("http://localhost/SNN/ajax/"+user_id+"/chat/"+partner_id, 
            function(data, status){
                if (status=="success") {
                    $("#mes").html('')
                    var messages = JSON.parse(data);
                    h = new MessagesHandler();
                    messages.forEach(h.display_message);
                }
            }
        );
        var id = parseInt(active_id);
        register_read(id);
        mark_chat_read(id);
        chats.forEach(function(item){
            if (item.partner_id==active_id) {
                $("#c"+active_id).attr("class","active_chat_header text-truncate");
            } else {
                $("#c"+item.partner_id).attr("class","chat_header text-truncate");
                id = parseInt(item.partner_id);
                mark_chat(id);
            }
        });
	}

	var check_chat = function(chat_partner_id,ts) {
        var saved_ts = Cookies.getJSON('last_mes_ts');
        if (ts>saved_ts) {
            register_unread(chat_partner_id);
            Cookies.set('last_mes_ts', ts, {expires:365});
        }
    }

	var register_read = function(chat_partner_id){
		var unread_chats = Cookies.getJSON('unread_chats');
        if (unread_chats) {
            var index = unread_chats.indexOf(chat_partner_id);
            if (index>-1) {
                unread_chats.splice(index, 1);
                Cookies.set('unread_chats', unread_chats, {expires:365});
            }
        }
	}

	var register_unread = function(chat_partner_id){
		var unread_chats = Cookies.getJSON('unread_chats');
        if (unread_chats) {
            var index = unread_chats.indexOf(chat_partner_id);
            if (index==-1) {
                unread_chats.push(chat_partner_id);
                Cookies.set('unread_chats', unread_chats, {expires:365});
            }
        } else{
        	Cookies.set('unread_chats', [chat_partner_id], {expires:365});
        }
	}

	var mark_chat = function(chat_partner_id){
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

	var mark_chat_read = function(chat_partner_id){
		$("#unread_c"+chat_partner_id).attr('class','invisible');
	} 

	var mark_chat_unread = function(chat_partner_id){
		$("#unread_c"+chat_partner_id).attr('class','visible');
        $("#c"+chat_partner_id).attr('class','chat_header_unread text-truncate');
	}

}