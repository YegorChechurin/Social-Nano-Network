function MessagesListener(){

	this.listen_incoming_messages = function(){
		$.get("http://localhost/SNN/ajax/"+user_id+"/messages/"+last_rec_mes_id, 
            function(data, status){
                if (data) {
                    var messages = JSON.parse(data);
                    var event = 'incoming_messages';
                    fire_event(event,messages);
                }
                if (status=='success') {
                	var recursion = function(){
                		var l = new MessagesListener();
                		l.listen_incoming_messages();
                	}
                    setTimeout(recursion,1000);
                }
            }
        );
	}

	this.listen_sent_messages = function(){
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
                    var event = 'message_sent';
                    fire_event(event,message);
                }
            }
        );
	}

	var fire_event = function(event,data){
		var gl_h = new GlobalHandler();
        gl_h.handle(event,data);
	}

}

