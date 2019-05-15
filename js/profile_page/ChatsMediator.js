function ChatsMediator(global_mediator) {

	var mediator = global_mediator;

	this.process_event = function(event){
		var received_messages = event.data;
		var n = received_messages.length;
		last_rec_mes_id = received_messages[n-1].message_id;
		var sender_names = [];
		for (var i = 0; i < n; i++) {
			if (i==0) {
				sender_names.push(received_messages[i].sender_name);
			} else {
				if (received_messages[i].sender_id!=received_messages[i-1].sender_id) {
					sender_names.push(received_messages[i].sender_name);
				}
			}
		}
		if (sender_names.length==1) {
			alert('You have received new messages from '+sender_names[0]+'. If you would like to send a response, please click "MESSENGER" in the top left corner');
		} else {
			var sender_names_string = sender_names.join(', ');
			alert('You have received new messages from '+sender_names_string+'. If you would like to send a response, please click "MESSENGER" in the top left corner');
		}
    }

}