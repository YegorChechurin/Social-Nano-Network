function FriendsHandler(event_handler) {

    var mediator = event_handler;

    this.fetch_friends = function(){
    	$.get("http://localhost/SNN/ajax/"+user_id+"/all_friends", 
            function(data, status){
                if (status=="success") {
                    friends = JSON.parse(data);
                    if (friends) {
                        friends.forEach(function(friend){
                            friends_IDs.push(JSON.parse(friend.friend_id));
                        });
                    }
                    var event = new Event('page_load','friends_fetched');
                    mediator.process_event(event);
                }
            }
        );
    }

    this.remove_friend = function(friend){
        $.get("http://localhost/SNN/ajax/"+user_id+"/friend_removal/"+friend.friend_id, 
            function(data, status){
                if (status=="success") {
                    for (var i = 0; i < friends.length; i++) {
                        if (parseInt(friend.friend_id)==parseInt(friends[i].friend_id)) {
                            friends.splice(i,1);
                        } 
                    }
                    for (var i = 0; i < friendship_IDs.length; i++) {
                        if (friend.friendship_id==friendship_IDs[i]) {
                            friendship_IDs.splice(i,1);
                        }
                    }
                    var event = new Event('friends','friend_removed',friend);
                    mediator.process_event(event);
                }
            }
        );
    }

    this.add_to_friends = function(user) {
        $.get("http://localhost/SNN/ajax/"+user_id+"/friend_addition/"+user.user_id
            +"?name1="+user_name+"&name2="+user.username, 
            function(data, status){
                if (status=="success" && data) {
                    var created_friendship_id = data;
                    var friend = {
                        friendship_id : created_friendship_id,
                        friend_id : user.user_id,
                        friend_name : user.username
                    };
                    if (friends) {
                        friends.push(friend);
                        friendship_IDs.push(created_friendship_id);
                    } else {
                        friends = [friend];
                        friendship_IDs = [created_friendship_id];
                    }
                    var event = new Event('friends','friend_added',friend);
                    mediator.process_event(event);
                }
            }
        );
    }

    this.handle_friend_data = function(friend_data){
        if (JSON.stringify(friendship_IDs)!=JSON.stringify(friend_data.friendship_IDs)) {
            if (
                (friend_data.friends_lost=='yes' || friend_data.friends_lost=='all_lost') 
                && 
                (friend_data.friends_obtained=='yes' || friend_data.friends_obtained=='all_new')
                ) 
            {
                var lost_friends_handler = mediator.create_object(LostFriendsHandler);
                var new_friends_handler = mediator.create_object(NewFriendsHandler);
                lost_friends_handler.handle_friend_data(friend_data);
                new_friends_handler.handle_friend_data(friend_data);
            } else if (friend_data.friends_lost=='no' 
                && (friend_data.friends_obtained=='yes' || friend_data.friends_obtained=='all_new')
                ) 
            {
                var new_friends_handler = mediator.create_object(NewFriendsHandler);
                new_friends_handler.handle_friend_data(friend_data);
            } else if (
                (friend_data.friends_lost=='yes' || friend_data.friends_lost=='all_lost') 
                && friend_data.friends_obtained=='no'
                ) 
            {
                var lost_friends_handler = mediator.create_object(LostFriendsHandler);
                lost_friends_handler.handle_friend_data(friend_data);
            }
        }
    }

}