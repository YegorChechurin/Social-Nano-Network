function FriendsMediator(global_mediator) {

	var mediator = global_mediator;

	this.process_event = function(event){
		if (event.name=='friend_node_clicked') {
			var messages_bar_handler = mediator.create_object(MessagesBarHandler);
			var chats_handler = mediator.create_object(ChatsHandler);
			var chats_bar_handler = mediator.create_object(ChatsBarHandler);
            messages_bar_handler.clear_messages_bar();
            var clicked_friend = event.data;
            var new_chat = chats_handler.start_new_chat_with_friend(clicked_friend);
            chats_handler.activate_chat(new_chat);
            chats_bar_handler.build_chats_bar();
		} else if (event.name=='friend_data_received') {
			var friends_handler = mediator.create_object(FriendsHandler);
			var friend_data = event.data;
			friends_handler.handle_friend_data(friend_data);
		} else if (event.name=='lost_friends_processed') {
			var friends_bar_handler = mediator.create_object(FriendsBarHandler);
			var chats_handler = mediator.create_object(ChatsHandler);
			var chats_bar_handler = mediator.create_object(ChatsBarHandler);
			var lost_friends_IDs = event.data;
        	friends_bar_handler.handle_lost_friends(lost_friends_IDs);
			var blocked_chats = chats_handler.handle_lost_friends(lost_friends_IDs);
			if (blocked_chats) {
				blocked_chats.forEach(chats_bar_handler.block_chat);
			}
		} else if (event.name=='new_friends_processed') {
			var friends_bar_handler = mediator.create_object(FriendsBarHandler);
			var chats_handler = mediator.create_object(ChatsHandler);
			var chats_bar_handler = mediator.create_object(ChatsBarHandler);
			var new_friends = event.data;
			friends_bar_handler.handle_new_friends(new_friends);
			var unblocked_chats = chats_handler.handle_new_friends(new_friends);
			if (unblocked_chats) {
				unblocked_chats.forEach(chats_bar_handler.unblock_chat);
			}
		}
	} 

}