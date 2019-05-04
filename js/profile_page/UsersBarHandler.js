function UsersBarHandler(event_handler) {

    var mediator = event_handler;

	this.build_users_bar = function(){
        if (users) {
            var counter = 0;
            users.forEach(function(user){
                if (!friends_IDs.includes(user.user_id)) {
                    create_user_node(user);
                    counter++;
                }
            });
            if (counter==0) {
                announce_all_users_are_friends();
            }
        } else {
            announce_zero_users();
        }
	}

	var create_user_node = function(user){
        var id = 'u'+user.user_id; 
        $("#inventory").append('<div class="element" id="'+id+'"></div>');
        $("#"+id).append('<div class="text">'+user.username+'</div>');
        $("#"+id).append('<button class="btn btn-info" id="'+'add_'+id+
            '">Add to friends</button>');
        $('#add_'+id).click(function(){
            add_user_to_friends(user);
        });
	}

    var add_user_to_friends = function(user){
        $("#u"+user.user_id).remove();
        check_users_bar_state();
        var event = new Event('friends','friend_addition_button_clicked',user);
        mediator.process_event(event);
    }

	var check_users_bar_state = function(){
		var pattern = /id="u[0-9]+"/;
        var content = $('#inventory').html();
        if (!pattern.exec(content)) {
            announce_all_users_are_friends();
        }
	}

	var announce_all_users_are_friends = function(){
		var $text = $('<div style="text-align:center"></div>').
        text(
            'All the users registered in Social Nano Network are your friends.'
        );
        $('#inventory').html($text);
	}

    var announce_zero_users = function(){
        var $text = $('<div style="text-align:center"></div>').
        text(
            'At the moment you are the only user registered in Social Nano Network.'
        );
        $('#inventory').html($text);
    }

}