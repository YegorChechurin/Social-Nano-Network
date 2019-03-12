function FriendsHandler() {

	var lost_friends_IDs = [];

	var lost_friends_names = [];

	var blocked_names = [];

	var unblocked_names = [];

	this.handle_friend_data = function(friend_data){
		var type = typeof friend_data;
		if (type==='object') {
			var new_friends = friend_data;
			var last_index = new_friends.length - 1;
	        last_friendship_id = parseInt(new_friends[last_index].friendship_id);
			new_friends.forEach(this.display_new_friend);
			new_friends.forEach(this.update_friends);
			this.announce_new_friends(new_friends);
			new_friends.forEach(this.unblock_chat);
			this.announce_unblocked_chats(new_friends);
		} else {
			last_friendship_id = parseInt(friend_data);
			this.remove_friends(last_friendship_id);
			this.announce_lost_friends();
			lost_friends_IDs.forEach(this.block_chat);
			this.announce_blocked_chats();
		}
	}

	this.display_new_friend = function(new_friend){
		var id = parseInt(new_friend.friend_id);
		if (chats) {
			var partner_IDs = [];
			chats.forEach(function(chat){
                partner_IDs.push(chat.partner_id);
            });
            if (!partner_IDs.includes(id)) {
            	create_new_friend_node(new_friend);
            }
		} else {
			create_new_friend_node(new_friend);
		}
	}

	var create_new_friend_node = function(new_friend){
		var id = parseInt(new_friend.friend_id);
		var pattern = /id="f[0-9]+"/;
        var content = $('#friends').html();
        if (!pattern.exec(content)) {
            var text = $('<div style="text-align:center"></div>').
            text('Friends you have no chats with:');
            $('#friend_caption').html(text);
        }
		var block = $('<div id="f'+id+'" class="friend"></div>');
        block.html('Start chatting with '+'<b>'+new_friend.friend_name+'</b>');
        $('#friends').prepend(block);
	}

	this.update_friends = function(new_friend){
		if (friends) {
			friends.push(new_friend);
		} else {
			friends = [new_friend];
		}
	}

	this.announce_new_friends = function(new_friends){
		if (new_friends.length==1) {
			alert('Congrats, user '+new_friends[0].friend_name+' is your new friend!');
		} else if (new_friends.length>1) {
			var new_friends_names = [];
			new_friends.forEach(function(new_friend){
				new_friends_names.push(new_friend.friend_name);
			});
			var new_friends_names_string = new_friends_names.join(', ');
			alert('Congrats, users '+new_friends_names_string+' are your new friends!');
		}
	}

	this.unblock_chat = function(new_friend){
		if (chats) {
			var id = parseInt(new_friend.friend_id);
			if (chat_partner_IDs.includes(id)) {
				for (var i = 0; i < chats.length; i++) {
					if (id==parseInt(chats[i].partner_id)) {
						unblocked_names.push(chats[i].partner_name);
						chats[i].blocked = 'no';
						var h = new ChatsBarHandler();
						h.unblock_chat(chats[i]);
					}
				}
			}
		}
	}

	this.announce_unblocked_chats = function(new_friends){
		if (unblocked_names.length==1) {
			alert('Chat with user '+unblocked_names[0]+' has been unlocked,'+
			' now you can freely chat with user '+unblocked_names[0]+'!:)');
		} else if (unblocked_names.length>1) {
			var unblocked_names_string = unblocked_names.join(', ');
			alert('Chats with users '+unblocked_names_string+' have been unlocked,'+
			' now you can freely chat with users '+unblocked_names_string+'!:)');
		}
	}

	this.remove_friends = function(last_friendship_id){
		for (var i = 0; i < friends.length; i++) {
			var friendship_id = parseInt(friends[i].friendship_id); 
			if (friendship_id > last_friendship_id) {					
				var id = parseInt(friends[i].friend_id);
				lost_friends_IDs.push(id);
				lost_friends_names.push(friends[i].friend_name);
				$('#f'+id).remove();
				friends.splice(i,1);
				i--;
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
	}

	this.announce_lost_friends = function(){
		if (lost_friends_names.length==1) {
			alert('So sad... User '+lost_friends_names[0]+
					' is no longer your friend');
		} else if (lost_friends_names.length>1) {
			var lost_friends_names_string = lost_friends_names.join(', ');
			alert('So sad... Users '+lost_friends_names_string+
					' are no longer your friends');
		}
	}

	this.block_chat = function(lost_friend_id){
		if (chats) {
			var id = parseInt(lost_friend_id);
			if (chat_partner_IDs.includes(id)) {
				for (var i = 0; i < chats.length; i++) {
					if (id==parseInt(chats[i].partner_id)) {
						chats[i].blocked = 'yes';
						blocked_names.push(chats[i].partner_name);
						var h = new ChatsBarHandler();
						h.block_chat(chats[i]);
					}
				}
			}
		}
	}

	this.announce_blocked_chats = function(){
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

}