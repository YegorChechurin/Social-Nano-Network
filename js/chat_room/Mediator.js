function Mediator() {

	var object_pool = [];

	var topic_mediator_pairs = [];

	this.create_object = function(object_constructor){
		if (object_pool.length==0) {
			var new_object = new object_constructor(this); 
			object_pool.push(new_object);
			return new_object;
		} else {
			for (var i = 0; i < object_pool.length; i++) {
				if (object_pool[i] instanceof object_constructor) {
					return object_pool[i];
				} else {
					var new_object = new object_constructor(this);
					object_pool.push(new_object);
					return new_object;
				}
			}
		}
	}

	this.attach_topic_mediator_pair = function(topic,mediator){
		var topic_mediator_pair = {
			topic : topic,
			mediator : mediator
		};
		if (topic_mediator_pairs.length==0) {
			topic_mediator_pairs.push(topic_mediator_pair);
		} else {
			topic_mediator_pairs.forEach(function(pair){
				if (pair.topic==topic_mediator_pair.topic) {
					return;
				}
			});
			topic_mediator_pairs.push(topic_mediator_pair);
		}
	}

	this.process_event = function(event){
		for (var i = 0; i < topic_mediator_pairs.length; i++) {
			if (topic_mediator_pairs[i].topic==event.topic) {
				topic_mediator_pairs[i].mediator.process_event(event);
				break;
			}
		}
	}

}