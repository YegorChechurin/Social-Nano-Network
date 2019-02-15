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
		alert('Congrats, new friend! User '+new_friend.friend_name+' has just added you to friend list');
		var id = parseInt(new_friend.friend_id);
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
		if (friends) {
			for (var i = 0; i < friends.length; i++) {
				var friendship_id = parseInt(friends[i].friendship_id); 
				if (friendship_id > last_friendship_id) {
					alert('So sad... User '+friends[i].friend_name+' is no longer your friend');
					var id = parseInt(friends[i].friend_id);
					$('#f'+id).remove();
					friends.splice(i,1);
				}
			}
		} else {}
	}

}