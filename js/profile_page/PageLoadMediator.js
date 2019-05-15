function PageLoadMediator(global_mediator) {

	var mediator = global_mediator;

	this.process_event = function(event){
		if (event.name=='page_loaded') {
			var friends_handler = mediator.create_object(FriendsHandler);
			friends_handler.fetch_friends();
		} else if (event.name=='friends_fetched') {
			var users_handler = mediator.create_object(UsersHandler);
			var friends_bar_handler = mediator.create_object(FriendsBarHandler);
			users_handler.fetch_users();
			friends_bar_handler.build_friends_bar();
		} else if (event.name=='users_fetched') {
			var users_bar_handler = mediator.create_object(UsersBarHandler);
			var friends_listener = mediator.create_object(FriendsListener);
			var messages_listener = mediator.create_object(MessagesListener);
			//var users_listener = mediator.create_object(UsersListener);
			users_bar_handler.build_users_bar();
			friends_listener.listen_new_friends();
			messages_listener.listen_incoming_messages();
			//users_listener.listen_new_users();
		}
	}

}