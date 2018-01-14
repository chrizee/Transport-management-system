<?php
	class Message extends Action {
		protected $_table;

		public function __construct($table = 'message') {
			$this->_table = $table;
		}

		public function put() {
			try {
				$user = new User();
				$this->create(array(
					'from' => $user->data()->id,
					'to' => Input::get('to'),
					'subject' => Input::get('subject'),
					'message' => $_POST['message'],		//to enable tags from textarea pass through
					));
			} catch (Exception $e) {
				die($e->getMessage());
			}	
		}

		public function getIds() {
			for($i = 0; $i < $this->noOfMembers; $i++) {
				$this->_ids[] = $this->_members[$i]->id; 
			}
			return $this->_ids;
		}	
	}
?>