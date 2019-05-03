function LostFriendsHandler(event_handler) {

	var mediator = event_handler;

	var lost_friends_IDs = [];

	var lost_friends_names = [];

	this.handle_friend_data = function(friend_data){
		if (friend_data.friends_lost=='all_lost') {
			this.remove_all_friends();
			friendship_IDs = 0;
		} else {
			var lost_friendship_IDs = friend_data.lost_friendship_IDs;
			this.remove_friends(lost_friendship_IDs);
			friendship_IDs = friend_data.friendship_IDs;
		}
		this.announce_lost_friends();
		var event = new Event('friends','lost_friends_processed',lost_friends_IDs);
        mediator.process_event(event);
	}

	this.remove_all_friends = function(){
		friends.forEach(function(friend){
			lost_friends_names.push(friend.friend_name);
			var id = parseInt(friend.friend_id);
			lost_friends_IDs.push(id);
		});
        friends = 0;
	}

	this.remove_friends = function(lost_friendship_IDs){
		for (var i = 0; i < friends.length; i++) {
			if (lost_friendship_IDs.includes(friends[i].friendship_id)) {					
				var id = parseInt(friends[i].friend_id);
				lost_friends_IDs.push(id);
				lost_friends_names.push(friends[i].friend_name);
				friends.splice(i,1);
				i--;
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

}