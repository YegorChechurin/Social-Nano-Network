function PageLoadMediator(global_mediator) {

	var mediator = global_mediator;

	this.process_event = function(event){
		if (event.name=='page_loaded') {
			var chats_handler = mediator.create_object(ChatsHandler);
            chats_handler.fetch_chats();
		} else if (event.name=='chats_fetched') {
			var chats_bar_handler = mediator.create_object(ChatsBarHandler);
			var messages_bar_handler = mediator.create_object(MessagesBarHandler);
			var friends_handler = mediator.create_object(FriendsHandler);
			if (chats) {
				chats_bar_handler.build_chats_bar();
			} else {
				chats_bar_handler.announce_no_chats();
			}
			messages_bar_handler.display_chat();
			friends_handler.fetch_friends();
		} else if (event.name=='friends_fetched') {
			var friends_bar_handler = mediator.create_object(FriendsBarHandler);
			var messages_listener = mediator.create_object(MessagesListener);
			var friends_listener = mediator.create_object(FriendsListener);
        	friends_bar_handler.build_friends_bar();
        	messages_listener.listen_incoming_messages();
        	friends_listener.listen_new_friends();
        	$("#send_button").click(
                function(){
                    messages_listener.listen_sent_messages();
                }
            );
		}
	}

}