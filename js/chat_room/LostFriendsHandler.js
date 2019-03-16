function LostFriendsHandler() {

	var lost_friends_IDs = [];

	var lost_friends_names = [];

	var blocked_names = [];

	this.handle_friend_data = function(friend_data){
		if (friend_data.friends_lost=='all_lost') {
			this.remove_all_friends();
			this.announce_lost_friends();
			lost_friends_IDs.forEach(this.block_chat);
			this.announce_blocked_chats();
			friendship_IDs = 0;
		} else {
			var lost_friendship_IDs = friend_data.lost_friendship_IDs;
			this.remove_friends(lost_friendship_IDs);
			this.announce_lost_friends();
			lost_friends_IDs.forEach(this.block_chat);
			this.announce_blocked_chats();
			friendship_IDs = friend_data.friendship_IDs;
		}
	}

	this.remove_all_friends = function(){
		friends.forEach(function(friend){
			lost_friends_names.push(friend.friend_name);
			var id = parseInt(friend.friend_id);
			lost_friends_IDs.push(id);
			$('#f'+friend.friend_id).remove();
		});
		var text = $('<div style="text-align:center"></div>').
        text(
            'You have no friends. In Social Nano Network you\
             can chat only with friends. Go to your profile page\
             and add some friends'
        );
        $('#friend_caption').html(text);
        friends = 0;
	}

	this.remove_friends = function(lost_friendship_IDs){
		for (var i = 0; i < friends.length; i++) {
			if (lost_friendship_IDs.includes(friends[i].friendship_id)) {					
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