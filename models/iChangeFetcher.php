<?php

	namespace Models;
	
	interface iChangeFetcher {

		public function fetch_changes($user_id,$query_parameter);

	}