function FriendsMediator(global_mediator) {

	var mediator = global_mediator;

	this.process_event = function(event){
		if (event.name=='friend_removal_button_clicked') {
			var friends_handler = mediator.create_object(FriendsHandler);
			var friend_to_be_removed = event.data;
			friends_handler.remove_friend(friend_to_be_removed);
		} if (event.name=='friend_removed') {
			var users_bar_handler = mediator.create_object(UsersBarHandler);
			var removed_friend = event.data;
			var user = {
                        user_id : removed_friend.friend_id,
                        username : removed_friend.friend_name
                    };
            users_bar_handler.create_user_node(user);
		} else if (event.name=='friend_addition_button_clicked') {
			var friends_handler = mediator.create_object(FriendsHandler);
			var user_to_be_added_to_friends = event.data;
			friends_handler.add_to_friends(user_to_be_added_to_friends);
		} else if (event.name=='friend_added') {
			var friends_bar_handler = mediator.create_object(FriendsBarHandler);
			var friend = event.data;
			friends_bar_handler.create_friend_node(friend);
			friends_bar_handler.scroll_bar(friend.friend_id);
		} else if (event.name=='friend_data_received') {
			var friends_handler = mediator.create_object(FriendsHandler);
			var friend_data = event.data;
			friends_handler.handle_friend_data(friend_data);
		} else if (event.name=='lost_friends_processed') {
			var friends_bar_handler = mediator.create_object(FriendsBarHandler);
			var users_bar_handler = mediator.create_object(UsersBarHandler);
			var lost_friends_IDs = event.data;
        	friends_bar_handler.handle_lost_friends(lost_friends_IDs);
			users_bar_handler.handle_lost_friends(lost_friends_IDs);
		} else if (event.name=='new_friends_processed') {
			var friends_bar_handler = mediator.create_object(FriendsBarHandler);
			var users_bar_handler = mediator.create_object(UsersBarHandler);
			var new_friends = event.data;
			friends_bar_handler.handle_new_friends(new_friends);
			users_bar_handler.handle_new_friends(new_friends);
		}
	} 

}