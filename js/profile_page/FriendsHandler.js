function FriendsHandler() {

	this.handle_friend_data = function(friend_data){
		var type = typeof friend_data;
		if (type==='object') {
			var new_friends = friend_data;
			var last_index = new_friends.length - 1;
	        last_friendship_id = parseInt(new_friends[last_index].friendship_id);
			new_friends.forEach(this.display_new_friend);
			new_friends.forEach(this.update_friends);
		} else {
			last_friendship_id = parseInt(friend_data);
			this.remove_outdated_friends(last_friendship_id);
		}
	}

	this.display_new_friend = function(new_friend){
		alert('Congrats, user '+new_friend.friend_name+' is your new friend!');
		$("#u"+new_friend.friend_id).remove();
		form_friend_element(new_friend);
	}

	this.update_friends = function(new_friend){
		if (friends) {
			friends.push(new_friend);
		} else {
			friends = [new_friend];
		}
	}

	this.remove_outdated_friends = function(last_friendship_id){
		for (var i = 0; i < friends.length; i++) {
			var friendship_id = parseInt(friends[i].friendship_id); 
			if (friendship_id > last_friendship_id) {					
				var id = parseInt(friends[i].friend_id);
				$('#f'+id).remove();
				alert('So sad... User '+friends[i].friend_name+
					' is no longer your friend');
				form_user_element(friends[i]);
				friends.splice(i,1);
			}
		}
	}

	var form_friend_element = function(friend) {
		var id = 'f'+friend.friend_id; 
        $("#friends").append('<div class="element" id="'+id+'"></div>');
        $("#"+id).append('<div class="text">'+friend.friend_name+'</div>');
        $("#"+id).append('<button class="btn btn-info" id="'+'remove_'+id+
        	'">Remove from friends</button>');
        $('#remove_'+id).click(function(){
        	remove_friend(friend);
        });
        var href = 'http://localhost/SNN/public/'+user_id+'/messenger';
        var query_string = '?id='+friend.friend_id+'&name='+friend.friend_name;
        var url = href + query_string;
        var messenger_button = $('<button class="btn btn-info"></button>').text('Send message');
        messenger_button.click(function(){
        	window.open(url,'_self');
        	//window.open(url,"myWindow","width=500,height=700");
        });
        $("#"+id).append(messenger_button);
	}

	var form_user_element = function(friend) {
		var id = 'u'+friend.friend_id; 
        $("#inventory").append('<div class="element" id="'+id+'"></div>');
        $("#"+id).append('<div class="text">'+friend.friend_name+'</div>');
        $("#"+id).append('<button class="btn btn-info" id="'+'add_'+id+
        	'">Add to friends</button>');
        $('#add_'+id).click(function(){
        	add_friend(friend);
        });
	}

	var remove_friend = function(friend) {
		$.get("http://localhost/SNN/ajax/"+user_id+"/friend_removal/"+friend.friend_id, 
            function(data, status){
                if (status=="success") {
                	$("#f"+friend.friend_id).remove();
                	var user = {
		        		user_id : friend.friend_id,
		        		username : friend.friend_name
		        	};
		        	form_user_element(user);
		        	for (var i = 0; i < friends.length; i++) {
                        if (parseInt(friend.friend_id)==parseInt(friends[i].friend_id)) {
                            friends.splice(i,1);
                        }
                    }
                    if (friends.length==0) {
                        last_friendship_id = 0;
                    } else {
                        /*var friendship_IDs = [];
                        for (var i = 0; i < friends.length; i++) {
                            friendship_IDs.push(parseInt(friends[i].friendship_id));
                            friendship_IDs.sort(function(a,b){return a-b});
                            var n = friendship_IDs.length-1;
                            last_friendship_id = friendship_IDs[n];
                        }*/
                        var n = friends.length - 1;
                        last_friendship_id = friends[n].friendship_id;
                    }
                }
            }
        );
	}

	var add_friend = function(friend) {
		$.get("http://localhost/SNN/ajax/"+user_id+"/friend_addition/"+friend.friend_id
			+"?name1="+user_name+"&name2="+friend.friend_name, 
            function(data, status){
                if (status=="success" && data) {
                    last_friendship_id = parseInt(data);
                	$("#u"+friend.friend_id).remove();
                	if (friends) {
                        friends.push(friend);
                    } else {
                        friends = [friend];
                    }
		        	form_friend_element(friend);
                }
            }
        );
	}

}