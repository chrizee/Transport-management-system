<?php 
	require_once 'includes/content/header.php';	
	if(!$user->checkPermission(array('staff'))) {    //only staff can see it
	    Session::flash('home', "You don't have permission to view that page");
	    Redirect::to('dashboard');
	}
	$errors = array();
	if(!Session::exists(Config::get('session/load')) || !Input::exists() || !Cookie::exists(Config::get('cookie/load')) || (Session::get(Config::get('session/load')) != Cookie::get(Config::get('cookie/load'))) ) {
		Session::flash('home', "Can't load that page without a valid trip");
		Redirect::to('load');
	}
	$session = Session::get(Config::get('session/load'));
	$destination = Input::get('destination');
	$source = $user->data()->location;

	$passengerObj = new Passenger('temp_passengers');
	$routeObj = new Route('routes');
	$vehicleObj = new Vehicle('vehicles');	
	$driverObj = new Driver('drivers');
	$travelObj = new Travels('travels');
	$waybillObj = new Waybill('waybill');
	$invoiceObj = new Invoice();
	
	try {
		$passengers = $passengerObj->get(array('hash', '=', $session, 'user_id', '=', $user->data()->id));
		$route = $routeObj->get(array('source', '=', $source, 'destination', '=', $destination));
		$vehicle = $vehicleObj->get(array('id', '=', Input::get('vehicle')), 'id,plate_no,no_of_seats,ac')[0];
		$driver = $driverObj->get(array('id', '=', Input::get('driver')))[0];
	} catch (Exception $e) {
		die($e->getMessage());
	}
	$price = (!empty($route)) ? $route[0]->price: Input::get('priceF');
	try {
		if(!empty($route)) {
			$travelObj->create(array(
				'vehicle_id' => $vehicle->id,
				'route_id' => $route[0]->id,
				'source' => $source,
				'destination' => $destination,
				'driver_id' => $driver->id,
				'user_id' => $user->data()->id,
				));
		} else {
			$travelObj->create(array(
				'vehicle_id' => $vehicle->id,
				'driver_id' => $driver->id,
				'source' => $source,
				'destination' => $destination,
				'user_id' => $user->data()->id,
				));
		}
		$travelId = $travelObj->lastId();
		$passengerObj2 = new Passenger('passengers');
		$passengerObj2->moveToPerm($passengers, $travelId);
		$invoiceObj->generate($price, $passengers, $travelId, $user->data()->id);
		$invoiceNo = $invoiceObj->lastId();
	} catch (Exception $e) {
		die($e->getMessage());
	}
	if(!empty(Input::get('waybill'))) {
		try {
			$waybillObj->changeStatus(Input::get('waybill'), 'travelling', $travelId);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	Cookie::delete(Config::get('cookie/load'));
	$duration = (!empty($route)) ? $route[0]->duration: "Not set";
?>
	
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?php echo $parkObj->get($user->data()->location, 'park')->park; ?>
        <small>Trip</small>
      </h1>
      <?php if(Session::exists('home')) {
		        echo "<p class='created text text-center text-danger'>".Session::flash('home')."</p>";
		    }
      ?>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Confirm</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="invoice">
      <!-- title row -->
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
            <i class="fa fa-globe"></i> <?php echo str_replace('|', '', $init->title) ;?>.
            <small class="pull-right">Date: <?php echo date('d/m/Y');?></small>
          </h2>
        </div>
        <!-- /.col -->
      </div>
      <!-- info row -->
      <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
	        <aside>
				<strong>Source:</strong> <span><?php echo $parkObj->get($source, "park")->park;?></span><br />
				<strong>Destination:</strong> <span><?php echo $parkObj->get($destination, "park")->park;?></span><br />
				<strong>Duration:</strong> <span><?php echo $duration; ?></span><br />
			</aside>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          	<aside>
				<strong>Driver:</strong> <span><?php echo $driver->name; ?></span><br />
				<strong>Vehicle:</strong> <span><?php echo $vehicle->plate_no; ?></span><br />
				<strong>Seats in vehicle:</strong> <span><?php echo $vehicle->no_of_seats ; ?></span><br />
				<strong>No of Passengers:</strong> <span><?php echo Count($passengers); ?></span><br />
				<strong>AC:</strong> <span><?php echo ($vehicle->ac == 1) ? "Yes" : "No"; ?></span><br />
			</aside>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          <b>Invoice #<?php echo $invoiceNo; ?></b><br>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <!-- Table row -->
      <div class="row invoice-table">
        <div class="col-xs-6 table-responsive">
          	<table class="table table-striped">
          		<caption>Passengers</caption>
	    		<thead>
	    			<tr>
	    				<th>S/n</th>
	    				<th>Name</th>
	    				<th>Phone</th>
	    				<th>Ticket</th>
	    			</tr>
	    		</thead>
	    		<tbody>
	        	<?php
	        		foreach ($passengers as $key => $value) {?>
	        			<tr>
	        				<td><?php echo $key + 1 ?></td>
	        				<td><?php echo $value->name; ?></td>
	        				<td><?php echo $value->phone?></td>
	        				<td><?php echo $value->ticket?></td>
	        			</tr>
	        	<?php }
	        	?>
				</tbody>
			</table>
        </div>
        <div class="col-xs-6 table-responsive">
          	<table class="table table-striped">
          		<caption>Waybill</caption>
          		<?php
          			if(!empty(Input::get('waybill'))){
          		?>
	    		<thead>
	    			<tr>
	    				<th>S/n</th>
	    				<th>item</th>
	    				<th>Weight</th>
	    			</tr>
	    		</thead>
	    		<tbody>
	        	<?php
	        		foreach (Input::get('waybill') as $key => $value) {
	        			$waybill = $waybillObj->get(array('id', '=', $value), 'item, weight')[0] ?>
	        			<tr>
	        				<td><?php echo $key + 1 ?></td>
	        				<td><?php echo $waybill->item ?></td>
	        				<td><?php echo $waybill->weight ?></td>
	        			</tr>
	        	<?php }
	        	?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="2">Total weight:</th>
						<th><?php echo Input::get('total_weight'); ?></th>
					</tr>
				</tfoot>
				<?php } else { ?>
					<tr><td>No waybill</td></tr>
				<?php } ?>
			</table>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
      <div class="row fuel">
      	<h3 class="text-center">Process Fuel collection here</h3>
      	<p class="text-center">Go collect fuel from filling station. get quantity of fuel from trip table.this section should be detached and collected by the filling station. </p>
      </div>

      <div class="row staff">
        <div class="col-sm-4 invoice-col">
	        <aside>
				<strong>Source:</strong> <span><?php echo $parkObj->get($source, "park")->park;?></span><br />
				<strong>Destination:</strong> <span><?php echo $parkObj->get($destination, "park")->park;?></span><br />
				<strong>Duration:</strong> <span><?php echo $duration; ?></span><br />
			</aside>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          	<aside>
				<strong>Driver:</strong> <span><?php echo $driver->name; ?></span><br />
				<strong>Vehicle:</strong> <span><?php echo $vehicle->plate_no; ?></span><br />
				<strong>No of Passengers:</strong> <span><?php echo Count($passengers); ?></span><br />
			</aside>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          <b>Invoice #<?php echo $invoiceNo; ?></b><br>
          <br>
          <b>Total:</b> <?php echo $invoiceObj->total ?><br>
        </div>
      </div>
      <!-- /.row -->

      <!-- this row will not appear when printing -->
      <div class="row no-print">
        <div class="col-xs-12">
          <a href="invoice-print_<?php echo encode($invoiceNo) ; ?>" target="_blank" class="btn btn-default pull-right"><i class="fa fa-print"></i> Print</a>
        </div>
      </div>
    </section>
  </div>
  <!-- /.content-wrapper -->
<script type="text/javascript">

</script> 
<?php 
	require_once 'includes/content/footer.php';
?>