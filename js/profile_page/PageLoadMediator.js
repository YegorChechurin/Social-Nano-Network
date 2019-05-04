function PageLoadMediator(global_mediator) {

	var mediator = global_mediator;

	this.process_event = function(event){
		if (event.name=='page_loaded') {
			var _handler = mediator.create_object(Handler);
		} else if (event.name=='') {
			var _handler = mediator.create_object(Handler);
		} else if (event.name=='') {
			var _handler = mediator.create_object(Handler);
		}
	}

}