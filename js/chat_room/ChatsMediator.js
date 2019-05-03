function ChatsMediator(global_mediator) {

	var mediator = global_mediator;

	this.process_event = function(event){
		if (event.name=='chat_header_clicked') {
			var chats_handler = mediator.create_object(ChatsHandler);
			var messages_bar_handler = mediator.create_object(MessagesBarHandler);
			var clicked_chat = event.data;
	        chats_handler.activate_chat(clicked_chat);
	        messages_bar_handler.display_chat();
		} else if (event.name=='chats_rearranged') {
			var chats_bar_handler = mediator.create_object(ChatsBarHandler);
        	chats_bar_handler.build_chats_bar();
		} else if (event.name=='active_chat_blocked') {
			var messages_bar_handler = mediator.create_object(MessagesBarHandler);
			messages_bar_handler.clear_messages_bar();
		} else if (event.name=='messages_received') {
			var messages_bar_handler = mediator.create_object(MessagesBarHandler);
			var chats_handler = mediator.create_object(ChatsHandler);
			var chats_bar_handler = mediator.create_object(ChatsBarHandler);
			var friends_bar_handler = mediator.create_object(FriendsBarHandler);
			var received_messages = event.data;
			received_messages.forEach(messages_bar_handler.display_message);
			messages_bar_handler.scroll_message_bar();
			received_messages.forEach(chats_handler.register);
	        received_messages.forEach(chats_handler.update_chats);
	        chats_handler.rearrange_chats();
	        received_messages.forEach(friends_bar_handler.handle_incoming_message);
	        chats_handler.update_last_received_mes_id(received_messages);
        	chats_handler.register_last_mes_ts();
		} else if (event.name=='message_sent') {
			var messages_bar_handler = mediator.create_object(MessagesBarHandler);
			var chats_handler = mediator.create_object(ChatsHandler);
			var friends_bar_handler = mediator.create_object(FriendsBarHandler);
			var message = event.data;
	        messages_bar_handler.display_message(message);
	        messages_bar_handler.scroll_message_bar();
	        chats_handler.update_chats(message);
	        chats_handler.rearrange_chats();
	        friends_bar_handler.handle_sent_message(message);
	        chats_handler.register_last_mes_ts();
		} 
	} 

}