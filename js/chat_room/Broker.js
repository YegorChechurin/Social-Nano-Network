/** 
 * Creates a new Broker.
 * @class 
 */
function Broker(){
    /** 
     * Invokes required handlers for the given event.
     * 
     * Reads events_map (located in js/chat_room/events_map.js)
     * array element by element. Each element of events_map
     * array contains event's name and array of handler class 
     * name and method pairs. If event's name contained in
     * the events_map array element is equal to the name of
     * the given event, array of handler class name and method 
     * pairs is read. Each handler class and its method are 
     * instantiated and implemeneted by eval according to the
     * order they are written in the events_map array. 
     *
     *  
     */
	this.invoke_handlers = function(event,data){
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