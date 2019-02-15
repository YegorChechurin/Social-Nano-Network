/** 
 * "event" - event name.
 * "handler" - name of class to be instantiated in order to
 * handle the event. 
 * "method" - name of "handler" method to be called in order
 * to handle the event.
 */
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
    "event":"friend_data",
    "handling":[{
        "handler":"FriendsHandler",
        "method":"handle_friend_data"
    }]
}]