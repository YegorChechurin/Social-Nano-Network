<?php
	
	namespace Controllers\Ajax\LongPolling;
	use Controllers\Ajax\LongPolling\LongPoll;

	class MessagesPoll extends LongPoll {

		public function get_query_parameter() {
    		$this->query_parameter = $this->request->uri[4];
		}

	}