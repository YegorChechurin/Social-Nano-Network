events_map = 
[{
    "event":"incoming_messages",
    "handling":[{
        "handler":"MessagesHandler",
        "method":"handle_incoming_messages"
    },
    {   "handler":"ChatsBarHandler",
        "method":"rearrange_chats_bar"
    }]
},
{
    "event":"message_sent",
    "handling":[{
        "handler":"MessagesHandler",
        "method":"handle_sent_message"
    },
    {   "handler":"ChatsBarHandler",
        "method":"rearrange_chats_bar"
    }]
},
{
    "event":"chat_room_load",
    "handling":[{}]
}]