events_map = 
[{
	"event":"incoming_messages",
    "handling":[{
    	"handler":"MessagesHandler()",
    	"method":"handle_incoming_messages(data)"
    },
    {   "handler":"ChatsBarHandler()",
    	"method":"rearrange_chats_bar(data)"
    }]
},
{
	"event":"message_sent",
    "handling":[{
    	"handler":"MessagesHandler()",
    	"method":"handle_sent_message(data)"
    },
    {   "handler":"ChatsBarHandler()",
    	"method":"rearrange_chats_bar(data)"
    }]
},
{
	"event":"chat_room_load",
	"handling":[{}]
}]