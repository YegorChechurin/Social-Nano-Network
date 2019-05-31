<?php
	
	namespace Controllers\Ajax\LongPolling;
	use Controllers\Ajax\LongPolling\LongPoll;

	class FriendsPoll extends LongPoll {

		public function get_query_parameter() {
			parse_str($this->request->query_string,$query_string);
    		$this->query_parameter = json_decode($query_string['IDs']);
		}

	}