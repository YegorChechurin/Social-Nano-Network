function MessagesHandler() {

	this.handle_incoming_messages = function(messages){
		var last_index = messages.length;
        last_rec_mes_id = parseInt(messages[last_index-1].message_id);
        this.register_last_mes_ts();
		messages.forEach(this.display_message);
		messages.forEach(this.register);
		// var l = messages.length;
  //       for (i = 0; i < l-1; i++) {
  //       	if (messages[i].sender_id==messages[i+1].sender_id) {
  //       		messages.splice(i, 1); 
  //       	}
  //       }
	}

	this.handle_sent_message = function(message){
		this.display_message(message);
        this.register_last_mes_ts();
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
        update_chats(message);
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

	var update_chats = function(message) {
		if (chats) {
        	chats.forEach(
        		function(chat) {
        			if ((message.sender_id==chat.participant1_id || message.recipient_id==chat.participant2_id)||(message.recipient_id==chat.participant1_id || message.recipient_id==chat.participant2_id)) {
        				chat.last_mes_auth_id = user_id;
        				chat.last_mes_auth_name = user_name;
        				chat.last_mes_text = message.message;
        				var date = new Date();
                        var t = date.getTime();
        				chat.last_mes_ts = t;
        			}
        		}
        	);
        }
	}

} 