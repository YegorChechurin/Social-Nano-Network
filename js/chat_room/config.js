function MessagesListener(){

	this.listen_incoming_messages = function(){
		$.get("http://localhost/SNN/ajax/"+user_id+"/messages/"+last_rec_mes_id, 
            function(data, status){
                if (data) {
                    var messages = JSON.parse(data);
                    var event = 'incoming_messages';
                    fire_event(event,messages);
                }
                if (status=='success') {
                	var recursion = function(){
                		var l = new MessagesListener();
                		l.listen_incoming_messages();
                	}
                    setTimeout(recursion,1000);
                }
            }
        );
	}

	this.listen_sent_messages = function(){
		var text = $("#text").val();
        $.post("http://localhost/SNN/ajax/"+user_id+"/messages",
            {user_name:user_name, partner_id:active_id, partner_name:active_name, message:text}, 
            function(data, status){
                if (status=="success") {
                    $("#text").val('');
                    var message = {
                        "sender_id":user_id,
                        "sender_name":user_name,
                        "recipient_id":active_id,
                        "message":text
                    };
                    var event = 'message_sent';
                    fire_event(event,message);
                }
            }
        );
	}

	var fire_event = function(event,data){
		var gl_h = new GlobalHandler();
        gl_h.handle(event,data);
	}

}

function GlobalHandler(){

	this.test = function(){
		alert(events_map[0].event);
	}

	this.get_this = function(){
		return this.constructor.name;
	}

    this.register_event = function(event){
    	this.events.push(event);
    }

	/*this.handle = function(event,data){
		events_map.forEach(function(item){
			if (event==item.event) {
				var h = eval('new '+item.handler);
				eval("h."+item.method);
			}
		});
	}*/

	this.handle = function(event,data){
		events_map.forEach(function(item){
			if (event==item.event) {
				item.handling.forEach(
					function(item){
						var h = eval('new '+item.handler);
				        eval("h."+item.method);
					}
				);
			}
		});
	}

}

/*events_map = 
[{
	"event":"incoming_messages",
	"handler":"MessagesHandler()",
	"method":"handle_incoming_messages(data)"
}]*/

/*function eat(food1, food2)
{
    alert("I like to eat " + food1 + " and " + food2 );
}
function myFunc(callback, args)
{
    //do stuff
    //...
    //execute callback when finished
    callback.apply(this, args);
}

//alerts "I like to eat pickles and peanut butter"
myFunc(eat, ["pickles", "peanut butter"]); */