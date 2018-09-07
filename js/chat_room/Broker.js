function Broker(){

	this.invoke_handler = function(event,data){
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