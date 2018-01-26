<?php
	class Message extends Action {
		protected $_table;

		public function __construct($table = 'message') {
			$this->_table = $table;
		}
		public function getN($id,$location, $trash = false) {
			if($trash) {
				$sql = "SELECT * FROM message WHERE recipient = ? AND receiver_status = ".Config::get('message/deleted')." OR `from` = ? AND sender_status = ".Config::get('message/deleted');
				if(!$data = DB::getInstance()->query($sql, array($id, $id))) {
					throw new Exception("There was a problem getting data");
				}
				return $data->results();
			}
			$sql = "SELECT * FROM message WHERE recipient =  ? AND receiver_status != ".Config::get('message/deleted');
			if(!$data = DB::getInstance()->query($sql, array($id))) {
				throw new Exception("There was a problem getting data");
			}
			return $data->results();
		}

		//to simplify this method use text as the data type in the DB and store the status field for each user in a JSON string rather than storing each one in a independent row
		public function put() {
			try {
				$user = new User();
				if(!empty(Input::get('location'))) {	//when sending to all in a location

					$staffs = $user->getStaffs(array('location', '=', Input::get('location'), 'id','!=', $user->data()->id),'id');
					if($staffs) {
						foreach ($staffs as $key => $value) {
							$this->create(array(
								'from' => $user->data()->id,
								'recipient' => $value->id,
								'subject' => ucfirst(Input::get('subject')),
								'message' => $_POST['message'],		//to enable tags from textarea pass through
								));
						}
					}
				} else {
					if(Input::get('to') == '*') {		//when sending to all staffs
						$staffs = $user->getStaffs(array('id','!=', $user->data()->id),'id');
						if($staffs) {
							foreach ($staffs as $key => $value) {
								$this->create(array(
									'from' => $user->data()->id,
									'recipient' => $value->id,
									'subject' => Input::get('subject'),
									'message' => $_POST['message'],		//to enable tags from textarea pass through
									));
							}
						}
					} else {		//when sending to one staff
						$this->create(array(
								'from' => $user->data()->id,
								'recipient' => Input::get('to'),
								'subject' => Input::get('subject'),
								'message' => $_POST['message'],		//to enable tags from textarea pass through
								));
					}
				}
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
