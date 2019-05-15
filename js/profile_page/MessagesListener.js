/** 
 * Creates a new MessagesListener.
 * @class 
 *
 * MessagesListener posses methods which allow to listen for
 * incoming and sent messages, and to fire corresponding 
 * events when new incoming messages arrive or when user 
 * sends a new message.
 */
function MessagesListener(event_handler){

    var mediator = event_handler;

    /** 
     * Listens for the incoming messages.
     *
     * Sends AJAX request to the server and waits for the 
     * response. If response contains new incoming messages, 
     * event 'incoming_messages' is fired. New AJAX request 
     * is sent 1 second after response for previous one 
     * arrives. 
     */
	this.listen_incoming_messages = function(){
		$.get("http://localhost/SNN/ajax/"+user_id+"/messages/"+last_rec_mes_id, 
            function(data, status){
                if (status=='success') {
                    if (data) {
                        var messages = JSON.parse(data);
                        var event = new Event('chats','messages_received',messages);
                        mediator.process_event(event);
                    }
                	var recursion = function(){
                		var l = mediator.create_object(MessagesListener);
                		l.listen_incoming_messages();
                	}
                    setTimeout(recursion,3000);
                }
            }
        );
	}
    
}