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
		var last_index = messages.length - 1;
        last_rec_mes_id = parseInt(messages[last_index].message_id);
        this.register_last_mes_ts();
		messages.forEach(this.display_message);
		messages.forEach(this.register);
        messages.forEach(update_chats);
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
		this.display_message(message);
        this.register_last_mes_ts();
        update_chats(message);
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
        } 
	}

    /** 
     * Registers timestamp of the last messages.
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
    
    /** 
     * Defines chat status.
     *
     * Defines status of a certain chat when user receives a    
     * new message from participant of this chat. If the 
     * chat is not opened on the user screen at that moment
     * when they receive a message from participant of this
     * chat, this chat is registered as the one containing
     * unread messages. 
     *
     * @param {Object} message - New incoming message.
     */
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
    
    /** 
     * Registers chat as the one containing unread messages.
     *
     * Array unread_chats consists of list of user id of 
     * those chat partners, chats with whom contain messages 
     * which user have not read yet. This array is stored
     * in cookies.
     *
     * @param {number} chat_partner_id - User id of the chat
     * partner. 
     */
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
    
    /** 
     * Updates chats.
     *
     * Updates meta data of a particular chat according to
     * last message of this chat.
     *
     * @param {Object} message - New message, either sent or
     * received.
     */
	var update_chats = function(message) {
		if (chats) {
        	chats.forEach(
        		function(chat) {
        			if (message.sender_id==chat.partner_id || message.recipient_id==chat.partner_id) {
        				chat.last_mes_auth_id = message.sender_id;
        				chat.last_mes_auth_name = message.sender_name;
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