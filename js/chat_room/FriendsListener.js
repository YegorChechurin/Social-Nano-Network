/** 
 * Creates a new FriendsListener.
 * @class 
 *
 * FriendsListener posses methods which allow to listen for
 * new friends, and to fire corresponding 
 * event when user gets new friends.
 */
function FriendsListener(event_handler) {

    var mediator = event_handler;

	/** 
     * Listens for new friends.
     *
     * Sends AJAX request to the server and waits for the 
     * response. If response contains new friends, 
     * event 'new_friends' is fired. New AJAX request 
     * is sent 1 second after response for previous one 
     * arrives. 
     */
	this.listen_new_friends = function(){
        var info = JSON.stringify(friendship_IDs);
		$.get("http://localhost/SNN/ajax/"+user_id+"/friends?IDs="+info,
            function(data, status){
                if (status=='success') {
                    if (data) {
                        var friend_data = JSON.parse(data);
                        var event = new Event('friends','friend_data_received',friend_data);
                        mediator.process_event(event);
                    }
                	var recursion = function(){
                		var l = mediator.create_object(FriendsListener);
                		l.listen_new_friends();
                	}
                    setTimeout(recursion,5000);
                }
            }
        );
	}

}