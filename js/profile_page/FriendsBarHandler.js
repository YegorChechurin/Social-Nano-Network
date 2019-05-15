function FriendsBarHandler(event_handler) {

    var mediator = event_handler;

	this.build_friends_bar = function(){
		if (friends) {
            friends.forEach(function(friend){
        	    create_friend_node(friend);
            });
        } else {
            announce_zero_friends();
        }          
	}

	var create_friend_node = function(friend){
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

    this.create_friend_node = function(friend){
        var pattern = /id="f[0-9]+"/;
        var content = $('#friends').html();
        if (!pattern.exec(content)) {
            $("#friend_caption").html('<h3 class="text">Your friends</h3>');
        }
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

    this.scroll_bar = function(friend_id){
        var last_mes_pos = document.getElementById("f"+friend_id).offsetTop;
        if (last_mes_pos > friends_bar_height) {
            document.getElementById("friends_bar").scrollTop = last_mes_pos; 
        }
    }

    var remove_friend = function(friend){
        $("#f"+friend.friend_id).remove();
        check_friends_bar_state();
        var event = new Event('friends','friend_removal_button_clicked',friend);
        mediator.process_event(event);
    }

	var check_friends_bar_state = function(){
		var pattern = /id="f[0-9]+"/;
        var content = $('#friends').html();
        if (!pattern.exec(content)) {
            announce_zero_friends();
        }
	}

	var announce_zero_friends = function(){
		var $text = $('<div style="text-align:center" class="text"></div>').
        text(
            'You have no friends. In Social Nano Network you\
             can chat only with friends. Please add some users to your friend list'
        );
        $('#friend_caption').html($text);
	}

    this.handle_lost_friends = function(lost_friends_IDs){
        lost_friends_IDs.forEach(
            function(id){
                $("#f"+id).remove();
                check_friends_bar_state();
            }
        );
    }

    this.handle_new_friends = function(new_friends){
        var pattern = /id="f[0-9]+"/;
        var content = $('#friends').html();
        if (!pattern.exec(content)) {
            $("#friend_caption").html('<h3 class="text">Your friends</h3>');
        }
        new_friends.forEach(
            function(friend){
                create_friend_node(friend);
            }
        );
    }

}