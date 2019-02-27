<?php

	interface iObserver {

		public function process_event($event, $data) {}
		
	}