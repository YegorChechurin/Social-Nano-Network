/** 
 * Creates a new FriendsListener.
 * @class 
 *
 * FriendsListener posses methods which allow to listen for
 * new friends, and to fire corresponding 
 * event when user gets new friends.
 */
function FriendsListener() {
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
		$.get("http://localhost/SNN/ajax/"+user_id+"/friends/"+last_friendship_id, 
            function(data, status){
                if (data) {
                    var friend_data = JSON.parse(data);
                    var event = 'friend_data';
                    fire_event(event,friend_data);
                }
                if (status=='success') {
                	var recursion = function(){
                		var l = new FriendsListener();
                		l.listen_new_friends();
                	}
                    setTimeout(recursion,1000);
                }
            }
        );
	}

	/** 
     * Fires event.
     *
     * @param {string} event - Name of the event to be handled.
     * @param {*} data - Data required to handle the event.   
     */
	var fire_event = function(event,data){
		var broker = new Broker();
        broker.invoke_handlers(event,data);
	}

}