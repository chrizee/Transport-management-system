<?php
	class Action {

		//method to get data from non static classes like drivers etc
		public function get($where =  array(), $fields = '*') {
			if(!$data = DB::getInstance()->get($this->_table, $where, $fields)) {
				throw new Exception("There was a problem getting data");
			}
			return $data->results();
		}

		//method to create record in non static classes like drivers etc
		public function create($fields = array()) {
			if(!DB::getInstance()->insert($this->_table, $fields)) {
				throw new Exception("There was a problem creating an account");
			}
		}

		public function update($id, $fields = array()) {
			if(!DB::getInstance()->update($this->_table, $id, $fields)) {
				throw new Exception("Error updating data");
			}
		}

		public function delete($where = array()) {
			if(!DB::getInstance()->delete($this->_table, $where)) {
				throw new Exception("Error deleting data");
			}
		}

		public function lastId() {
			if(!$lastId = DB::getInstance()->lastInsertId($this->_table)) {
				throw new Exception("Error getting last Id");
			}
			return $lastId;
		}

		//returns the time part of the date in a precise format
		public function date($date) {
			$init = new dateTime($date);
			$now = new dateTime();
			$diff = $now->diff($init);
			if($diff->y != 0 ) {
				return $diff->format("%y Years %m months %d days");
			} elseif($diff->m != 0) {
				return $diff->format("%m months %d days");
			} elseif($diff->d != 0) {
				return $diff->format("%d days");
			} elseif($diff->h != 0) {
				return $diff->format("%h hours");
			} elseif($diff->i != 0) {
				return $diff->format("%i mins");
			} elseif($diff->s != 0) {
				return $diff->format("%s secs");
			} elseif($diff->s == 0) {
				return "now";
			}
		}
	}
