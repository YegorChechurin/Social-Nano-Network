function NewFriendsHandler(event_handler) {

	var mediator = event_handler;

	this.handle_friend_data = function(friend_data){
		var new_friends = friend_data.new_friends;
		friendship_IDs = friend_data.friendship_IDs;
		if (friend_data.friends_obtained=='all_new') {
			friends = new_friends;
		} else {
			new_friends.forEach(this.update_friends);
		}
		this.announce_new_friends(new_friends);
		var event = new Event('friends','new_friends_processed',new_friends);
        mediator.process_event(event);
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

}