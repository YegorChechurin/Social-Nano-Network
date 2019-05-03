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
                    setTimeout(recursion,1000);
                }
            }
        );
	}
    
    /** 
     * Listens for sent messages.
     *
     * When user types their message into 'text' element and
     * hits 'Send' button in order to send the message, 
     * contents of 'text' element is grabbed. Then this 
     * contents together with all the necessary data is sent 
     * to the server by post AJAX request. If request 
     * succeeds, 'text' element is cleared, message object is 
     * formed and 'message_sent' event is fired.
     */
	this.listen_sent_messages = function(){
        if (active_id) {
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
                        var event = new Event('chats','message_sent',message);
                        mediator.process_event(event);
                    }
                }
            );
        }
	}
    
}

