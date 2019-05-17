<?php
	
	namespace Controllers\Ajax\LongPolling;
	use Skeleton\RequestHandling\Request;
	use Models\iChangeFetcher;

	abstract class LongPoll {

		protected $request;

		protected $service;

		protected $method;

		protected $user_id;

		protected $query_parameter;

		public function __construct(Request $request, iChangeFetcher $service) {
			$this->request = $request;
			$this->service = $service;
			$this->user_id = $this->request->uri[2];
		}

		abstract public function get_query_parameter();

		public function poll($start,$finish,$sleeping_interval) {
			$data = $this->service->fetch_changes($this->user_id,$this->query_parameter);
	        if ($data) {
	            echo $data;
	        } else {
	            sleep($sleeping_interval);
	            $current = time();
	            if ($current > $finish) {
	                exit;
	            } else {
	                $this->poll($start,$finish,$sleeping_interval);
	            }
	        }
		}

	}