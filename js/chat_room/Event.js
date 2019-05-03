function Event(event_topic,event_name,event_data) {

	this.topic = event_topic;

	this.name = event_name;

	this.data = event_data;

	this.set_fields = function(topic,name,data){
		this.topic = topic;
		this.name = name;
		this.data = data;
	}

}