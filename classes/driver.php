<?php
	class Driver extends Action {
		protected $_table;

		public function __construct($table) {
			$this->_table = $table;
		}

	}