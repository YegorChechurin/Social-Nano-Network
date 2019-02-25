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
     * the fired event, array of handler class name and method 
     * pairs is read. Each handler class and its method are 
     * instantiated and called dynamically according to the
     * way and order they are written in the events_map array. 
     *
     * @param {string} event - Name of the event to be handled.
     * @param {*} data - Anything which is required to be 
     * passed to the invoked handlers.   
     */
	this.invoke_handlers = function(event,data){
		events_map.forEach(function(item){
			if (event==item.event) {
				item.handling.forEach(
					function(item){
						var handler_name = item.handler;
				        var h = new window[handler_name];
				        var method_name = item.method;
				        h[method_name](data);
					}
				);
			}
		});
	}

}