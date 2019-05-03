function FriendsBarHandler(event_handler) {

    var mediator = event_handler;

	this.build_friends_bar = function(){
		if (friends) {
            var counter = 0;
            friends.forEach(function(friend){
            	var id = parseInt(friend.friend_id);
        		if (!chat_partner_IDs.includes(id)) {
        			counter++;
            	    create_friend_node(friend);
        		}
            });
            if (counter==0) {
                announce_chatting_with_all_friends();
            }
        } else {
            announce_zero_friends();
        }          
	}

	var create_friend_node = function(friend){
		var id = friend.friend_id;
    	var pattern = /id="f[0-9]+"/;
        var content = $('#friends').html();
        if (!pattern.exec(content)) {
            announce_extra_friends();
        }
        var $block = $('<div id="f'+id+'" class="friend"></div>');
        $block.html('Start chatting with '+'<b>'+friend.friend_name+'</b>');
        $('#friends').prepend($block);
        $block.click(function(){
        	$block.remove();
        	check_friends_bar_state();
            var event = new Event('friends','friend_node_clicked',friend);
            mediator.process_event(event);
        });
	}

	var check_friends_bar_state = function(){
		var pattern = /id="f[0-9]+"/;
        var content = $('#friends').html();
        if (!pattern.exec(content)) {
            announce_chatting_with_all_friends();
        }
	}

	var announce_chatting_with_all_friends = function(){
		var $text = $('<div style="text-align:center"></div>').
        text(
            'You have chats with all of your\
             friends. If you would like to chat with some other\
             Social Nano Network users, you have to add them to your\
             friend list'
        );
        $('#friend_caption').html($text);
	}

	var announce_zero_friends = function(){
		var $text = $('<div style="text-align:center"></div>').
        text(
            'You have no friends. In Social Nano Network you\
             can chat only with friends. Go to your profile page\
             and add some friends'
        );
        $('#friend_caption').html($text);
	}

	var announce_extra_friends = function(){
		var $text = $('<div style="text-align:center"></div>').
        text('Friends you have no chats with:');
        $('#friend_caption').html($text);
	}

	this.handle_incoming_message = function(message){
		var id = message.sender_id;
		$('#f'+id).remove();
        check_friends_bar_state();
	}

	this.handle_sent_message = function(message){
		var id = message.recipient_id;
		$('#f'+id).remove();
        check_friends_bar_state();
	}

	this.handle_lost_friends = function(lost_friends_IDs){
		if (friends) {
			lost_friends_IDs.forEach(function(id){
				$('#f'+id).remove();
				check_friends_bar_state();
			});
		} else {
			$('#friends').html('');
			announce_zero_friends();
		}
	}

	this.handle_new_friends = function(new_friends){
		new_friends.forEach(function(friend){
			var id = parseInt(friend.friend_id);
    		if (!chat_partner_IDs.includes(id)) {
        	    create_friend_node(friend);
    		}
		});
	}

}