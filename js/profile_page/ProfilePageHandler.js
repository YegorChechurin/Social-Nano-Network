function ProfilePageHandler() {

	this.build_bars = function(){
		$.get("http://localhost/SNN/ajax/"+user_id+"/all_friends", 
            function(data, status){
                if (status=="success") {
                    friends = JSON.parse(data);
                    build_friends_bar();
                }
                build_non_friends_bar();
            }
        );
	}

	var build_friends_bar = function(){
		if (friends) {
            friends.forEach(form_friend_element);
        }
	}

	var build_non_friends_bar = function(){
		$.get("http://localhost/SNN/ajax/"+user_id+"/all_users", 
            function(data, status){
                if (status=="success") {
                    users = JSON.parse(data);
                    if (users) {
                    	if (friends) {
                    		var friend_IDs = [];
	                    	friends.forEach(
	                    		function(friend){
	                    			friend_IDs.push(friend.friend_id);
	                    		}
	                    	);
	                    	users.forEach(function(user){
	                    		if (!friend_IDs.includes(user.user_id)) {
	                    			form_user_element(user);
	                    		}
	                    	});
                    	} else {
                    		users.forEach(form_user_element);
                    	}
                    }
                }
            }
        );
	}

	var form_friend_element = function(friend) {
		var id = 'f'+friend.friend_id; 
        $("#friends").append('<div id="'+id+'"></div>');
        $("#"+id).append('<div>'+friend.friend_name+'</div>');
        $("#"+id).append('<button id="'+'remove_'+id+'">Remove from friends</button>');
        $('#remove_'+id).click(function(){
        	remove_friend(friend);
        });
        var href = 'http://localhost/SNN/public/'+user_id+'/messenger';
        var query_string = '?id='+friend.friend_id+'&name='+friend.friend_name;
        var url = href + query_string;
        var messenger_button = $('<button></button>').text('Send message');
        messenger_button.click(function(){
        	window.open(url,'_self');
        	//window.open(url,"myWindow","width=500,height=700");
        });
        $("#"+id).append(messenger_button);
	}

	var form_user_element = function(user) {
		var id = 'u'+user.user_id; 
        $("#inventory").append('<div id="'+id+'"></div>');
        $("#"+id).append('<div>'+user.username+'</div>');
        $("#"+id).append('<button id="'+'add_'+id+'">Add to friends</button>');
        $('#add_'+id).click(function(){
        	add_friend(user);
        });
	}

	var remove_friend = function(friend) {
		$.get("http://localhost/SNN/ajax/"+user_id+"/friend_removal/"+friend.friend_id, 
            function(data, status){
                if (status=="success") {
                	$("#f"+friend.friend_id).html('');
                	var user = {
		        		user_id : friend.friend_id,
		        		username : friend.friend_name
		        	};
		        	form_user_element(user);
                }
            }
        );
	}

	var add_friend = function(user) {
		$.get("http://localhost/SNN/ajax/"+user_id+"/friend_addition/"+user.user_id
			+"?name1="+user_name+"&name2="+user.username, 
            function(data, status){
                if (status=="success") {
                	$("#u"+user.user_id).html('');
                	var friend = {
		        		friend_id : user.user_id,
		        		friend_name : user.username
		        	};
		        	form_friend_element(friend);
                }
            }
        );
	}

}