function ChatsHandler(event_handler) {

    var mediator = event_handler;

    /**
     * Fetches all chats that a specific user has.
     *
     * Sends AJAX get request to the server in order to fetch
     * chats meta data: id and names of participants, id and 
     * name of last message author, text and timestamp of 
     * last message. Server sends reply containing all this
     * information in json format. It is parsed and stored in
     * chats global array. MySQL timestamps are converted 
     * into number of milliseconds since January 1, 1970, 
     * 00:00:00 UTC. Checks whether user has received any new
     * messages since the last time when he opened his messenger
     * page. If upon request there was passed user id and user name 
     * of chat partner with whom user would like to chat, and it
     * turns out that user has not had any chats with this chat
     * partner before, new empty chat is created and added to 
     * the chats global array as first element with index 0. 
     */
	this.fetch_chats = function(){
		$.get("http://localhost/SNN/ajax/"+user_id+"/chats", 
            function(data, status){
                if (status=="success") {
                    chats = JSON.parse(data);
                    if (chats) {
                        chats.forEach(function(chat){
                            chat_partner_IDs.push(chat.partner_id);
                            convert_chat_last_mes_ts(chat);
                            check_chat(chat.partner_id,chat.last_mes_ts);
                        });
                    }
                    if (active_id && active_name && !chat_partner_IDs.includes(active_id)) {
                        start_new_empty_chat();
                    }
                    var event = new Event('page_load','chats_fetched');
                    mediator.process_event(event);
                }
            }
        );
	}

	/** 
     * Converts timestamp of chat last message from MySQL
     * string format into number of milliseconds since 
     * January 1, 1970, 00:00:00 UTC. 
     *
     * @param {Object} chat - Chat object containing meta
     * data about a particular chat.
     */
    var convert_chat_last_mes_ts = function(chat) {
        var t = Date.parse(chat.last_mes_ts);
        chat.last_mes_ts = t;
    }

	var start_new_empty_chat = function(){
		var new_chat = {
            partner_id : active_id,
            partner_name : active_name,
            last_mes_auth_id : 0,
            last_mes_auth_name : user_name,
            last_mes_text : '',
            last_mes_ts : 0,
            blocked : 'no' 
        };
        if (chats) {
            chats.splice(0,0,new_chat);
        } else {
            chats = [new_chat];
        }
        chat_partner_IDs.push(active_id);
	}

    this.start_new_chat = function(message){
        var new_chat = {
            partner_id : parseInt(message.sender_id),
            partner_name : message.sender_name,
            last_mes_auth_id : message.sender_id,
            last_mes_auth_name : message.sender_name,
            last_mes_text : message.message,
            last_mes_ts : Date.parse(message.ts),
            blocked : 'no'
        };
        if (chats) {
            chats.splice(0,0,new_chat);
        } else {
            chats = [new_chat];
        }
        chat_partner_IDs.push(new_chat.partner_id);
    }

    this.start_new_chat_with_friend = function(friend){
        var new_chat = {
            partner_id : parseInt(friend.friend_id),
            partner_name : friend.friend_name,
            last_mes_auth_id : 0,
            last_mes_auth_name : user_name,
            last_mes_text : '',
            last_mes_ts : 0,
            blocked : 'no' 
        };
        if (chats) {
            chats.splice(0,0,new_chat);
        } else {
            chats = [new_chat];
        }
        chat_partner_IDs.push(new_chat.partner_id);
        return new_chat;
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
    this.update_chats = function(message) {
        if (chats) {
            var counter = 0;
            for (var i = 0; i < chats.length; i++) {
                if (message.sender_id==chats[i].partner_id || message.recipient_id==chats[i].partner_id) {
                    chats[i].last_mes_auth_id = message.sender_id;
                    chats[i].last_mes_auth_name = message.sender_name;
                    chats[i].last_mes_text = message.message;
                    var date = new Date();
                    var t = date.getTime();
                    chats[i].last_mes_ts = t;
                    counter = counter + 1;
                    break;
                } 
            }
            if (counter==0) {
                this.start_new_chat(message);
            }
        } else {
            this.start_new_chat(message);
        }
    }

	/** 
     * Rearranges chats array. 
     *
     * Sorts chats array in descending (reverse) order based 
     * on last message timestamp: chat with largest last
     * message timestamp should be first and chat with the
     * smallest should be the last. Updates and stores in 
     * cookies information about timestamp of the very last 
     * message (either sent or received) of all the chats. 
     */
    this.rearrange_chats = function() {
        var l = chats.length;
        var stamp;
        for (var i = 0; i < l; i++) {
            for (var j = 1; j < (l-i); j++) {
                if (chats[j-1].last_mes_ts<chats[j].last_mes_ts) {
                    stamp = chats[j-1];
                    chats[j-1] = chats[j];
                    chats[j] = stamp;
                }
            }
        }
        var ts = chats[0].last_mes_ts;
        Cookies.set('last_mes_ts', ts, {expires:365});
        var event = new Event('chats','chats_rearranged');
        mediator.process_event(event);
    }

    this.activate_chat = function(chat){
        active_id = chat.partner_id;  
        active_name = chat.partner_name;
        var id = parseInt(active_id);
        register_read(id);
    }

    this.handle_new_friends = function(new_friends){
        var unblocked_chats = [];
        var unblocked_names = [];
        new_friends.forEach(function(new_friend){
            if (chats) {
                var id = parseInt(new_friend.friend_id);
                if (chat_partner_IDs.includes(id)) {
                    for (var i = 0; i < chats.length; i++) {
                        if (id==parseInt(chats[i].partner_id)) {
                            unblocked_chats.push(chats[i]);
                            unblocked_names.push(chats[i].partner_name);
                            unblock_chat(chats[i]);
                        }
                    }
                }
            }
        });
        this.announce_unblocked_chats(unblocked_names);
        return unblocked_chats;
    }

    this.announce_unblocked_chats = function(unblocked_names){
        if (unblocked_names.length==1) {
            alert('Chat with user '+unblocked_names[0]+' has been unlocked,'+
            ' now you can freely chat with user '+unblocked_names[0]+'!:)');
        } else if (unblocked_names.length>1) {
            var unblocked_names_string = unblocked_names.join(', ');
            alert('Chats with users '+unblocked_names_string+' have been unlocked,'+
            ' now you can freely chat with users '+unblocked_names_string+'!:)');
        }
    }

    this.handle_lost_friends = function(lost_friend_IDs){
        var blocked_chats = [];
        var blocked_names = [];
        lost_friend_IDs.forEach(function(lost_friend_id){
            if (chats) {
                var id = parseInt(lost_friend_id);
                if (chat_partner_IDs.includes(id)) {
                    for (var i = 0; i < chats.length; i++) {
                        if (id==parseInt(chats[i].partner_id)) {
                            blocked_chats.push(chats[i]);
                            blocked_names.push(chats[i].partner_name);
                            block_chat(chats[i]);
                        }
                    }
                }
            }
        });
        this.announce_blocked_chats(blocked_names);
        return blocked_chats;
    }

    this.announce_blocked_chats = function(blocked_names){
        if (blocked_names.length==1) {
            alert('Chat with user '+blocked_names[0]+' is locked. In order to '+
             'unlock this chat, you should add user '+blocked_names[0]+
             ' to your friend list');
        } else if (blocked_names.length>1) {
            var blocked_names_string = blocked_names.join(', ');
            alert('Chats with users '+blocked_names_string+' are locked. In order '+
             'to unlock these chats, you should add users '+blocked_names_string+
             ' to your friend list');
        }
    }

    /** 
     * Checks whether chat has any unread messages.
     *
     * Compares timestamp of last message in the chat to the
     * timestamp stored in cookies. If timestamp of the last
     * message in the chat exceeds the one stored in cookies,
     * it means that this particular chat contains unread 
     * messages.
     *
     * @param {number} chat_partner_id - User id of chat 
     * partner. 
     * @param {number} ts - Timestamp of last message in 
     * chat in format of number of milliseconds since 
     * January 1, 1970, 00:00:00 UTC.
     */
	var check_chat = function(chat_partner_id,ts) {
        if (chat_partner_id==active_id) {
            register_read(chat_partner_id);
        } else {
            var saved_ts = Cookies.getJSON('last_mes_ts');
            if (ts>saved_ts) {
                register_unread(chat_partner_id);
                Cookies.set('last_mes_ts', ts, {expires:365});
            }
        }
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
     * Registers chat as the one which does NOT contain any
     * unread messages. 
     *
     * Array unread_chats is stored in cookies. It consists 
     * of user id of those chat partners, chats with whom 
     * contain unread messages. This array is read and if
     * chat_partner_id is present in the array, it is removed
     * from the array. 
     *
     * @param {number} chat_partner_id - User id of chat 
     * partner. 
     */
	var register_read = function(chat_partner_id) {
		var unread_chats = Cookies.getJSON('unread_chats');
        if (unread_chats) {
            var index = unread_chats.indexOf(chat_partner_id);
            if (index>-1) {
                unread_chats.splice(index, 1);
                Cookies.set('unread_chats', unread_chats, {expires:365});
            }
        }
	}
    
    /** 
     * Registers chat as the one which does contain unread 
     * messages. 
     *
     * Array unread_chats is stored in cookies. It consists 
     * of user id of those chat partners, chats with whom 
     * contain unread messages. This array is read and if
     * chat_partner_id is not present in the array, it is 
     * added to the array. 
     *
     * @param {number} chat_partner_id - User id of chat 
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
        } else{
        	Cookies.set('unread_chats', [chat_partner_id], {expires:365});
        }
	}

    var unblock_chat = function(chat){
        chat.blocked = 'no';
        var parsed_id = parseInt(chat.partner_id);   
        var ts = chat.last_mes_ts;
        check_chat(parsed_id,ts);
    }

    var block_chat = function(chat){
        chat.blocked = 'yes';
        if (chat.partner_id==active_id) {
            active_id = 0;
            active_name = '';
            var event = new Event('chats','active_chat_blocked');
            mediator.process_event(event);
        }
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

}