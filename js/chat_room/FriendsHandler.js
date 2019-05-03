function FriendsHandler(event_handler) {

	var mediator = event_handler;

	this.fetch_friends = function(){
		$.get("http://localhost/SNN/ajax/"+user_id+"/all_friends", 
            function(data, status){
                if (status=="success") {
                    friends = JSON.parse(data);
                    var event = new Event('page_load','friends_fetched');
                    mediator.process_event(event);
                }
            }
        );
	}

	this.handle_friend_data = function(friend_data){
		if (friend_data.friends_lost=='yes' && friend_data.friends_obtained=='yes') {
			var lost_friends_handler = mediator.create_object(LostFriendsHandler);
			var new_friends_handler = mediator.create_object(NewFriendsHandler);
			lost_friends_handler.handle_friend_data(friend_data);
			new_friends_handler.handle_friend_data(friend_data);
		} else if (friend_data.friends_lost=='no' && friend_data.friends_obtained=='yes') {
			var new_friends_handler = mediator.create_object(NewFriendsHandler);
			new_friends_handler.handle_friend_data(friend_data);
		} else if (friend_data.friends_lost=='yes' && friend_data.friends_obtained=='no') {
			var lost_friends_handler = mediator.create_object(LostFriendsHandler);
			lost_friends_handler.handle_friend_data(friend_data);
		}
	}

}