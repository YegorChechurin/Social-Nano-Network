function MessagesBarHandler(event_handler) {

	var mediator = event_handler;

	this.display_chat = function(){
		if (active_id && active_name && chat_partner_IDs.includes(active_id)) {
	        $.get("http://localhost/SNN/ajax/"+user_id+"/chat/"+active_id, 
	            function(data, status){
	                if (status=="success") {
	                    var nodes = $("#mes").children();
				        var pattern = /m[0-9]+/;
				        for (var i = 0; i < nodes.length; i++) {
				            if (pattern.exec(nodes[i].id)) {
				                $("#"+nodes[i].id).remove();
				            } else if (nodes[i].localName=='br') {
				                $(nodes[i]).remove();
				            }
				        }
	                    number_of_messages_displayed = 0;
	                    var messages = JSON.parse(data);
	                    messages.forEach(
	                        function(message) {
	                            number_of_messages_displayed++;
	                            if (message.sender_id == user_id) {
	                                var content = '<div class="message_outlet" id="m'
	                                + number_of_messages_displayed +
	                                '"><b>You:</b> '+message.message+'</div><br>';
	                                $("#mes").append(content);
	                            } else {
	                                var content = '<div class="message_inlet" id="m'
	                                + number_of_messages_displayed +
	                                '"><b>'+message.sender_name+':</b> '+message.message+'</div><br>';
	                                $("#mes").append(content);
	                            }
	                        }
	                    );
	                    if (document.getElementById("m"+number_of_messages_displayed)) {
							var last_mes_pos = document.getElementById("m"+number_of_messages_displayed).offsetTop;
					        if (last_mes_pos > mes_height) {
					            document.getElementById("mes").scrollTop = last_mes_pos; 
					        }
						}
	                }
	            }
	        );
		}
	}

	this.clear_messages_bar = function(){
		var nodes = $("#mes").children();
        var pattern = /m[0-9]+/;
        for (var i = 0; i < nodes.length; i++) {
            if (pattern.exec(nodes[i].id)) {
                $("#"+nodes[i].id).remove();
            } else if (nodes[i].localName=='br') {
                $(nodes[i]).remove();
            }
        }
	}

	/** 
     * Displays message on user screen.
     *
     * If message is send by the user or received from that
     * chat partner with whom user is chatting at the moment,
     * it is displayed in 'mes' element. If 'mes' element is
     * full, it is automatically scrolled. 
     *
     * @param {Object} message - Message to be displayed.
     */
	this.display_message = function(message){
		if (active_id && (message.sender_id==active_id || message.recipient_id==active_id)) {
            number_of_messages_displayed++;
            if (message.sender_id == user_id) {
                var content = '<div class="message_outlet" id="m'
                + number_of_messages_displayed +
                '"><b>You:</b> '+message.message+'</div><br>';
                $("#mes").append(content);
            } else {
                var content = '<div class="message_inlet" id="m'
                + number_of_messages_displayed +
                '"><b>'+message.sender_name+':</b> '+message.message+'</div><br>';
                $("#mes").append(content);
            }
        } 
	}

	this.scroll_message_bar = function(){
		if (document.getElementById("m"+number_of_messages_displayed)) {
			var last_mes_pos = document.getElementById("m"+number_of_messages_displayed).offsetTop;
	        if (last_mes_pos > mes_height) {
	            document.getElementById("mes").scrollTop = last_mes_pos; 
	        }
		}
	}

}