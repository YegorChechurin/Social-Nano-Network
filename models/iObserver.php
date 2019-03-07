<?php

	namespace Models;

	interface iObserver {

		public function process_event($event, $data);
		
	}