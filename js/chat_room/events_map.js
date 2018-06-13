/*events_map = 
[{
	"event":"incoming_messages",
	"handler":"MessagesHandler()",
	"method":"handle_incoming_messages(data)"
},
{
	"event":"message_sent",
	"handler":"MessagesHandler()",
	"method":"handle_sent_message(data)"
}]*/

events_map = 
[{
	"event":"incoming_messages",
    "handling":[{
    	"handler":"MessagesHandler()",
    	"method":"handle_incoming_messages(data)"
    }]
},
{
	"event":"message_sent",
    "handling":[{
    	"handler":"MessagesHandler()",
    	"method":"handle_sent_message(data)"
    }]
},
{
	"event":"chat_room_load",
	"handling":[{}]
}]