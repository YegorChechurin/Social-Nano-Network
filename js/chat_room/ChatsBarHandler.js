/** 
 * Creates a new ChatsBarHandler.
 * @class 
 *
 * ChatsBarHandler posses methods, required for buiding 
 * chats bar, and rearranging it, when user receives or sends
 * a new message.
 */
function ChatsBarHandler(event_handler){

    var mediator = event_handler;

    this.announce_no_chats = function(){
        $("#chats_wrapper").
        html('<div style="text-align:center" class="text">You have no chats yet</div>');
    }

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
        $("#chats_wrapper").html('');
        chats.forEach(function(chat){
            var $chat_header = form_chat_header(chat);
            append_chat_header($chat_header);
        });
        this.mark_chats_bar();
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
        var $chat_header = $('<div id="'+id+'"></div>');
        $chat_header.html('<b class="text">'+chat.partner_name+'</b><br>'+
        chat.last_mes_auth_name+': '+chat.last_mes_text);
        if (chat.blocked=='no') {
            $chat_header.click(function(){
                display_chat(chat);
            });
        } else {
            $chat_header.attr('class','chat_header_blocked text-truncate');
            var $blocked_div = $('<div id="lock'+chat.partner_id+'" class="lock"></div>');
            /*https://github.com/danklammer/bytesize-icons*/
            var $blocked_sign = $('<svg class="lock" viewBox="0 0 32 32" width="32" height="32"><use xlink:href="http://localhost/SNN/images/lock.svg#i-lock"></use></svg>');
            $blocked_div.append($blocked_sign);
            $chat_header.append($blocked_div);
        }
        return $chat_header;
    }

    var append_chat_header = function(chat_header){
        $('#chats_wrapper').append(chat_header);
    }

    var prepend_chat_header = function(chat_header){
        $('#chats_wrapper').prepend(chat_header);
    }

    this.mark_chats_bar = function(){
        chats.forEach(function(chat){
            $chat_header = $('#c'+chat.partner_id);
            if (chat.blocked=='no') {
                var parsed_chat_partner_id = parseInt(chat.partner_id);
                if (chat.partner_id==active_id) {
                    $chat_header.attr('class','active_chat_header text-truncate');
                    mark_chat_read(parsed_chat_partner_id);
                } else { 
                    $chat_header.attr('class','chat_header text-truncate');
                    mark_chat(parsed_chat_partner_id);
                }
            } else {
                var $tooltip = $('<div id="hint'+chat.partner_id+'" class="hint"></div>').
                text('Chat with user '+chat.partner_name+' is locked. In order to unlock it add user '+chat.partner_name+' to your friend list');
                var height = document.getElementById('c'+chat.partner_id).offsetTop;
                var top = 1*height;
                $("#mes").append($tooltip);
                $chat_header.hover(function(){
                    $('#hint'+chat.partner_id).css('top',top);
                    $('#hint'+chat.partner_id).css('display','inline-block');
                }, function(){
                    $('#hint'+chat.partner_id).css('display','none');
                });
            }
        });
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

    var display_chat = function(chat){
        if (active_id) {
            $("#c"+active_id).attr("class","chat_header text-truncate");
        }
        $("#c"+chat.partner_id).attr("class","active_chat_header text-truncate");
        var id = parseInt(chat.partner_id);
        mark_chat_read(id);
        var event = new Event('chats','chat_header_clicked',chat);
        mediator.process_event(event);
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
        console.log(chat_partner_id);
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
        $("#unread_c"+chat_partner_id).remove();
	} 
    
    /** 
     * Marks chat header as unread. 
     *
     * @param {number} chat_partner_id - User id of chat 
     * partner. 
     */
	var mark_chat_unread = function(chat_partner_id){
        $('#c'+chat_partner_id).append('<div id="unread_c'+chat_partner_id+'" style="position:absolute; top:0; left:50%; color:red"><b>UNREAD</b></div>');
        $("#c"+chat_partner_id).attr('class','chat_header_unread text-truncate');
	}

    this.unblock_chat = function(chat){
        $("#lock"+chat.partner_id).remove();
        $("#hint"+chat.partner_id).remove();
        var id = 'c'+chat.partner_id; 
        $("#"+id).off('hover');
        $("#"+id).click(function(){
            display_chat(chat);
        });  
        $("#"+id).attr('class','chat_header text-truncate');
        var parsed_id = parseInt(chat.partner_id);
        mark_chat(parsed_id);
    }

    this.block_chat = function(chat){
        var id = 'c'+chat.partner_id; 
        $("#"+id).off('click');
        $("#unread_"+id).remove();
        $("#"+id).attr('class','chat_header_blocked text-truncate');
        var blocked_div = $('<div id="lock'+chat.partner_id+'" class="lock"></div>');
        /*https://github.com/danklammer/bytesize-icons*/
        var blocked_sign = $('<svg class="lock" viewBox="0 0 32 32" width="32" height="32"><use xlink:href="http://localhost/SNN/images/lock.svg#i-lock"></use></svg>');
        blocked_div.append(blocked_sign);
        $("#"+id).append(blocked_div);
        var tooltip = $('<div id="hint'+chat.partner_id+'" class="hint"></div>').
        text('Chat with user '+chat.partner_name+' is locked. In order to unlock it add user '+chat.partner_name+' to your friend list');
        var height = document.getElementById(id).offsetTop;
        var top = 1*height;
        $("#mes").append(tooltip);
        $("#"+id).hover(function(){
            $('#hint'+chat.partner_id).css('top',top);
            $('#hint'+chat.partner_id).css('display','inline-block');
        }, function(){
            $('#hint'+chat.partner_id).css('display','none');
        });
    }

}