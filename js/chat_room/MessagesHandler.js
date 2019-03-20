/** 
 * Creates a new MessagesHandler.
 * @class 
 *
 * MessagesHandler posses methods which are required for 
 * displaying new received and sent messages, and keeping
 * chats meta data up-to-date.
 */
function MessagesHandler() {
    
    /** 
     * Handles new incoming messages. 
     *
     * Updates information about last received message id.
     * Registers the moment when the last message was 
     * received using cookies. Displays each message. If 
     * message sender is not the one with whom user is 
     * chatting at the moment, message is marked as unread 
     * on the user screen. Updates 'chats' array which 
     * contains meta data for all the chats of the user.  
     *
     * @param {Object[]} messages - Newly received messages.
     */
	this.handle_incoming_messages = function(messages){
		this.update_last_received_mes_id(messages);
        this.register_last_mes_ts();
        var h1 = new MessagesBarHandler();
		messages.forEach(h1.display_message);
        h2 = new ChatsHandler;
		messages.forEach(h2.register);
        messages.forEach(h2.update_chats);
        h3 = new ChatsBarHandler();
        messages.forEach(h3.handle_incomig_message);
        h4 = new FriendsBarHandler();
        messages.forEach(h4.update_friends_bar);
	}
    
    /** 
     * Handles sent message. 
     *
     * Displays message sent by user on the user screen. 
     * Registers the moment when the message was sent using 
     * cookies. Updates 'chats' array which contains meta 
     * data for all the chats of the user.
     *
     * @param {Object} message - Sent message.
     */
	this.handle_sent_message = function(message){
        this.register_last_mes_ts();
        var h1 = new MessagesBarHandler();
        h1.display_message(message);
        h2 = new ChatsHandler;
        h2.update_chats(message);
        h3 = new ChatsBarHandler();
        h3.handle_sent_message(message);
	}
    
    this.update_last_received_mes_id = function(messages){
        var last_index = messages.length - 1;
        last_rec_mes_id = parseInt(messages[last_index].message_id);
    }
    
    /** 
     * Registers timestamp of the last message being sent or received.
     *
     * Registers the time moment when the user sent or 
     * received their last messages. This time moment is 
     * stored in cookies. 
     */
	this.register_last_mes_ts = function(){
		var date = new Date();
        var last_mes_ts = date.getTime();
        Cookies.set('last_mes_ts', last_mes_ts, {expires:365});
	}

    var update_friends = function(id) {
        $('#f'+id).remove();
        var pattern = /id="f[0-9]+"/;
        var content = $('#friends').html();
        if (!pattern.exec(content)) {
            var text = $('<div style="text-align:center"></div>').
            text(
                'You have chats with all of your\
                 friends. If you would like to chat with some other\
                 Social Nano Network users, you have to add them to your\
                 friend list'
            );
            $('#friend_caption').html(text);
        }
    }

} 