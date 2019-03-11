/** 
 * Creates a new MessagesListener.
 * @class 
 *
 * MessagesListener posses methods which allow to listen for
 * incoming and sent messages, and to fire corresponding 
 * events when new incoming messages arrive or when user 
 * sends a new message.
 */
function MessagesListener(){
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
                        var event = 'message_sent';
                        fire_event(event,message);
                    }
                }
            );
        }
	}
    
    /** 
     * Fires event.
     *
     * @param {string} event - Name of the event to be handled.
     * @param {*} data - Data required to handle the event.   
     */
	var fire_event = function(event,data){
		var broker = new Broker();
        broker.invoke_handlers(event,data);
	}

}

