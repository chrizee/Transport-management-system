<?php
class Invoice extends Action{
	protected $_table = 'invoice';
	public $total;

	public function generate($price, $noOfItems, $travelId, $userId) {
		$this->total =  $price * count($noOfItems);
		$this->create(array(
			'travel_id' => $travelId,
			'unit_price' => $price,
			'trip_amount' => $this->total,
			'user_id' => $userId,
			));
	}
}