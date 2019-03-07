<?php 

	namespace Models;

	interface iObservable {

		public function attach_observer(iObserver $observer, $event);

		public function detach_observer(iObserver $observer, $event);

		public function fire_event($event, $data);

	}