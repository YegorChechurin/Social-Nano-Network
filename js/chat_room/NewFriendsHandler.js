function NewFriendsHandler() {

	var unblocked_names = [];

	this.handle_friend_data = function(friend_data){
		if (friend_data.friends_obtained=='all_new') {
			var text = $('<div style="text-align:center"></div>').
            text(
                'You have chats with all of your\
                 friends. If you would like to chat with some other\
                 Social Nano Network users, you have to add them to your\
                 friend list'
            );
            $('#friend_caption').html(text);
			var new_friends = friend_data.new_friends;
			friends = new_friends;
			new_friends.forEach(this.display_new_friend);
			this.announce_new_friends(new_friends);
			new_friends.forEach(this.unblock_chat);
			this.announce_unblocked_chats();
			friendship_IDs = friend_data.friendship_IDs;
		} else {
			var new_friends = friend_data.new_friends;
			new_friends.forEach(this.display_new_friend);
			this.announce_new_friends(new_friends);
			new_friends.forEach(this.unblock_chat);
			this.announce_unblocked_chats();
			new_friends.forEach(this.update_friends);
			friendship_IDs = friend_data.friendship_IDs;
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

	this.announce_unblocked_chats = function(){
		if (unblocked_names.length==1) {
			alert('Chat with user '+unblocked_names[0]+' has been unlocked,'+
			' now you can freely chat with user '+unblocked_names[0]+'!:)');
		} else if (unblocked_names.length>1) {
			var unblocked_names_string = unblocked_names.join(', ');
			alert('Chats with users '+unblocked_names_string+' have been unlocked,'+
			' now you can freely chat with users '+unblocked_names_string+'!:)');
		}
	}

}