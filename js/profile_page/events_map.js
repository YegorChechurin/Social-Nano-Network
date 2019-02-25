/** 
 * "event" - event name.
 * "handler" - name of class to be instantiated in order to
 * handle the event. 
 * "method" - name of "handler" method to be called in order
 * to handle the event.
 */
events_map = 
[{
    "event":"friend_data",
    "handling":[{
        "handler":"FriendsHandler",
        "method":"handle_friend_data"
    }]
}]