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
		var $text = $('<div style="text-align:center"></div>').
        text(
            'You have no friends. In Social Nano Network you\
             can chat only with friends.'
        );
        $('#friends').html($text);
	}

}