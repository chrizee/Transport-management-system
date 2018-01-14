<?php
class Park extends Action {
	protected $_table = 'location_of_park', $_db;

	public function __construct() {
		$this->_db = DB::getInstance();
	}
	//get info about park from park table
	public function get($id = null, $fields = "*") {
		if($id) {
			if(!$location = $this->_db->get($this->_table, array('id', '=', $id), $fields)) {
				throw new Exception("There was a problem getting location data");
			}
			return $location->first();
		}

		if(!$location = $this->_db->get($this->_table, array('1', '=', '1'), $fields)) {
			throw new Exception("There was a problem getting location data");
		}
		return $location->results();
	}

	public function lastId() {
		$lastId = $this->_db->lastInsertId($this->_table);
		return $lastId;
	}
}