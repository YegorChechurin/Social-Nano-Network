/** 
 * Creates a new ChatsBarHandler.
 * @class 
 *
 * ChatsBarHandler posses methods, required for buiding 
 * chats bar, and rearranging it, when user receives or sends
 * a new message.
 */
function ChatsBarHandler(){
    /** 
     * Builds chats bar.
     *
     * Sends AJAX get request to the server in order to fetch
     * chats meta data: id and names of participants, id and 
     * name of last message author, text and timestamp of 
     * last message. Server sends reply containing all this
     * information in json format. It is parsed and stored in
     * chats global array. MySQL timestamps are converted 
     * into number of milliseconds since January 1, 1970, 
     * 00:00:00 UTC. Chats are sorted so that chat whose
     * last message timestamp is the largest, has index 0 in
     * chats array. Timestamp of the first element of chats
     * array is stored in cookies. Using information 
     * contained in chats array, chats bar is built in 
     * 'chats_wrapper' element. 
     */
	this.build_chats_bar = function(){
		$.get("http://localhost/SNN/ajax/"+user_id+"/chats", 
            function(data, status){
                if (status=="success") {
                    chats = JSON.parse(data);
                    if (chats) {
                        chats.forEach(convert_chat_last_mes_ts);
                        var ts = chats[0].last_mes_ts;
                        Cookies.set('last_mes_ts', ts, {expires:365});
                        var partner_IDs = [];
                        chats.forEach(function(chat){
                            partner_IDs.push(chat.partner_id);
                        });
                        if (active_id && active_name) {
                            if (!partner_IDs.includes(active_id)) {
                                var chat = {
                                    partner_id : active_id,
                                    partner_name : active_name,
                                    last_mes_auth_id : 0,
                                    last_mes_auth_name : user_name,
                                    last_mes_text : '',
                                    last_mes_ts : 0 
                                };
                                chats.splice(0,0,chat);
                            } else {
                                display_chat(active_id,active_name);
                            }
                        }
                        chats.forEach(form_chat_header);
                    } else {
                        if (active_id && active_name) {
                            var chat = {
                                partner_id : active_id,
                                partner_name : active_name,
                                last_mes_auth_id : 0,
                                last_mes_auth_name : user_name,
                                last_mes_text : '',
                                last_mes_ts : 0 
                            };
                            chats = [chat];
                            chats.forEach(form_chat_header);
                        }
                    }
                }
            }
        );
	}
    
    /** 
     * Rearranges chats bar. 
     *
     * Sorts chats array in descending (reverse) order based 
     * on last message timestamp: chat with largest last
     * message timestamp should be first and chat with the
     * smallest should be the last. Rebuilds chats bar based
     * on the sorted chats array. Updates and stores in 
     * cookies information about timestamp of the very last 
     * message (either sent or received) of all the chats. 
     */
    this.rearrange_chats_bar = function() {
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
        $("#chats_wrapper").html('');
        chats.forEach(form_chat_header);
        var ts = chats[0].last_mes_ts;
        Cookies.set('last_mes_ts', ts, {expires:365});
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
    
    /** 
     * Forms chat header.
     *
     * Appends new chat header to chats bar, fills it with 
     * chat meta data, appends click event listener to it - 
     * display_chat function. Checks whether the chat 
     * represented by newly appended chat header has any
     * unread messages. If it does, chat header is marked
     * accordingly.
     *
     * @param {Object} chat - Chat object containing meta
     * data about a particular chat.
     */
	var form_chat_header = function(chat){
        var id = 'c'+chat.partner_id; 
        $("#chats_wrapper").append('<div id="'+id+'"></div>');
        $("#"+id).click(function(){
            display_chat(chat.partner_id,chat.partner_name);
        });
        $("#"+id).html('<b>'+chat.partner_name+'</b><br>'+
        chat.last_mes_auth_name+': '+chat.last_mes_text);
        $("#"+id).append('<div id="unread_'+id+'" style="position:absolute; top:0; left:50%; color:red"><b>UNREAD</b></div>');
        var parsed_id = parseInt(chat.partner_id);
        if (chat.partner_id==active_id) {
            $("#"+id).attr('class','active_chat_header text-truncate');
            register_read(parsed_id);
            mark_chat_read(parsed_id);
        } else { 
            $("#"+id).attr('class','chat_header text-truncate');
            var ts = chat.last_mes_ts;
            check_chat(parsed_id,ts);
            mark_chat(parsed_id);
        }
	}
    
    /** 
     * Displays chat on user screen.
     *
     * This function is called when user clicks on a chat
     * header. AJAX request containing user id and id of the
     * chat partner is sent to the server. Response to this
     * request contains message objects of all the messages
     * belonging to the chat. These message objects consist
     * of name and id of sender and receiver, message text
     * and timestamp. Each message is displayed in 'mes' 
     * html element according to the order they were sent.
     * If 'mes' element gets full with messages, it is 
     * scrolled so that the user can see the last message.
     * Chat headers are updated.
     *
     * @param {number} partner_id - User id of chat partner.
     * @param {string} partner_name - Name of chat partner.
     */
	var display_chat = function(partner_id,partner_name){
		number_of_messages_displayed = 0;
        active_id = partner_id;  
        active_name = partner_name;
        $.get("http://localhost/SNN/ajax/"+user_id+"/chat/"+partner_id, 
            function(data, status){
                if (status=="success") {
                    $("#mes").html('')
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
        var id = parseInt(active_id);
        register_read(id);
        mark_chat_read(id);
        chats.forEach(function(item){
            if (item.partner_id==active_id) {
                $("#c"+active_id).attr("class","active_chat_header text-truncate");
            } else {
                $("#c"+item.partner_id).attr("class","chat_header text-truncate");
                id = parseInt(item.partner_id);
                mark_chat(id);
            }
        });
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
        var saved_ts = Cookies.getJSON('last_mes_ts');
        if (ts>saved_ts) {
            register_unread(chat_partner_id);
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
    
    /** 
     * Marks chat header as read or unread. 
     *
     * Array unread_chats is stored in cookies. It consists 
     * of user id of those chat partners, chats with whom 
     * contain unread messages. This array is read and if
     * chat_partner_id is not present in the array,
     * corresponding chat header is marked as read. If it is
     * present in the array, corresponding chat header is 
     * marked as unread. 
     *
     * @param {number} chat_partner_id - User id of chat 
     * partner. 
     */
	var mark_chat = function(chat_partner_id){
		var unread_chats = Cookies.getJSON('unread_chats');
        if (unread_chats) {
            var index = unread_chats.indexOf(chat_partner_id);
            if (index>-1) {
                mark_chat_unread(chat_partner_id);
            } else {
                mark_chat_read(chat_partner_id);
            }
        } else {
            mark_chat_read(chat_partner_id);
        }
	}
    
    /** 
     * Marks chat header as read. 
     *
     * @param {number} chat_partner_id - User id of chat 
     * partner. 
     */
	var mark_chat_read = function(chat_partner_id){
		$("#unread_c"+chat_partner_id).attr('class','invisible');
	} 
    
    /** 
     * Marks chat header as unread. 
     *
     * @param {number} chat_partner_id - User id of chat 
     * partner. 
     */
	var mark_chat_unread = function(chat_partner_id){
		$("#unread_c"+chat_partner_id).attr('class','visible');
        $("#c"+chat_partner_id).attr('class','chat_header_unread text-truncate');
	}

}