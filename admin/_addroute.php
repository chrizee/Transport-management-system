<?php
	require_once '../core/init.php';
	//updating routes table
	if(!empty(Input::get('id'))) {
		$route = new Route('routes');
		try {
			$route->update(Input::get('id'), array(
				'price' => Input::get('price'),
				'duration' => Input::get('duration'),
				));
			echo "U";
		} catch (Exception $e) {
			print_r($e->getMessage());
		}
	}
	//inserting in routes
	if(empty(Input::get('id'))) {
		if(!empty(Input::get('price'))) {		
			$route = new Route('routes');
				if(!empty(Input::get('duration'))) {
					//echo 'a';
					try {
						$route->create(array(
							'source' => Input::get('source'),
							'destination' => Input::get('destination'),
							'price' => Input::get('price'),
							'duration' => Input::get('duration'),
							));
						echo "A";	//with duration check with javascript
					} catch (Exception $e) {
						print_r($e->getMessage());
					}
				} else { //echo 'b';
					try {
						$route->create(array(
							'source' => Input::get('source'),
							'destination' => Input::get('destination'),
							'price' => Input::get('price'),
							));
						echo "B";	//success without duration
					} catch (Exception $e) {
						print_r($e->getMessage());
					}
				}	
		} else {
			echo "X";	//no price failure
		}
	}
	
?>