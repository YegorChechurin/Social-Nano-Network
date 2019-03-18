function MessagesBarHandler() {

	this.display_chat(){
		if (active_id && active_name) {
			number_of_messages_displayed = 0;
	        $.get("http://localhost/SNN/ajax/"+user_id+"/chat/"+active_id, 
	            function(data, status){
	                if (status=="success") {
	                    clear_messages_bar();
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
	                            var last_mes_pos = document.getElementById("m"+number_of_messages_displayed).offsetTop;
	                            if (last_mes_pos > mes_height) {
	                                document.getElementById("mes").scrollTop = last_mes_pos; 
	                            }
	                        }
	                    );
	                }
	            }
	        );
		}
	}

	var clear_messages_bar = function(){
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

}