function UsersHandler(event_handler) {

    var mediator = event_handler;

    this.fetch_users = function(){
    	$.get("http://localhost/SNN/ajax/"+user_id+"/all_users", 
            function(data, status){
                if (status=="success") {
                    users = JSON.parse(data);
                    var event = new Event('page_load','users_fetched');
                    mediator.process_event(event);
                }
            }
        );
    }

    this.add_user_to_friends = function(user){}

}