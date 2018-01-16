<?php
	require_once '../core/init.php';
	//updating routes table
	$parkObj = new Park();
	$notice = new Notification();
	$user = new User();
	if(!empty(Input::get('id'))) {
		$route = new Route('routes');
		try {
			$route->update(Input::get('id'), array(
				'price' => Input::get('price'),
				'duration' => Input::get('duration'),
				));
				$path = $route->get(array('id', '=', Input::get('id')), 'source,destination')[0];
				$source = $parkObj->get($path->source, 'park')->park;
				$destination = $parkObj->get($path->destination, 'park')->park;
				$notice->create(array(
					'message' => $source. " to ". $destination ." is now ". Input::get('price'),
					'initiated' => $user->data()->id,
					'location_initiated' => $user->data()->location,
					'affected' => Config::get('permissions/all'),
					'location_affected' => Config::get('permissions/all'),
					'category' => Config::get('notification/price_update'),
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
							$source = $parkObj->get(Input::get('source'), 'park')->park;
							$destination = $parkObj->get(Input::get('destination'), 'park')->park;
							$notice->create(array(
								'message' => $source. " to ". $destination ." is now fixed for ". Input::get('price'),
								'initiated' => $user->data()->id,
								'location_initiated' => $user->data()->location,
								'affected' => Config::get('permissions/all'),
								'location_affected' => Config::get('permissions/all'),
								'category' => Config::get('notification/price_update'),
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
							$source = $parkObj->get(Input::get('source'), 'park')->park;
							$destination = $parkObj->get(Input::get('destination'), 'park')->park;
							$notice->create(array(
								'message' => $source. " to ". $destination ." is now fixed for ". Input::get('price'),
								'initiated' => $user->data()->id,
								'location_initiated' => $user->data()->location,
								'affected' => Config::get('permissions/all'),
								'location_affected' => Config::get('permissions/all'),
								'category' => Config::get('notification/price_update'),
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
