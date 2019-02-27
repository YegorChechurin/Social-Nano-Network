function FriendsHandler() {

	this.handle_friend_data = function(friend_data){
		var type = typeof friend_data;
		if (type==='object') {
			var new_friends = friend_data;
			var last_index = new_friends.length - 1;
	        last_friendship_id = parseInt(new_friends[last_index].friendship_id);
			new_friends.forEach(this.display_new_friend);
			new_friends.forEach(this.update_friends);
		} else {
			last_friendship_id = parseInt(friend_data);
			this.remove_friends(last_friendship_id);
		}
	}

	this.display_new_friend = function(new_friend){
		alert('Congrats, user '+new_friend.friend_name+' is your new friend!');
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

	this.remove_friends = function(last_friendship_id){
		for (var i = 0; i < friends.length; i++) {
			var friendship_id = parseInt(friends[i].friendship_id); 
			if (friendship_id > last_friendship_id) {					
				var id = parseInt(friends[i].friend_id);
				$('#f'+id).remove();
				alert('So sad... User '+friends[i].friend_name+
					' is no longer your friend');
				friends.splice(i,1);
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

}