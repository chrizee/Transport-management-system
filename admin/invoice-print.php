<?php
	require_once '../core/init.php';
	if(!$user->checkPermission(array('staff'))) {    //only staff can see it
	    Session::flash('home', "You don't have permission to view that page");
	    Redirect::to('dashboard.php');
	}
	if(!Session::exists(Config::get('session/load'))) {
		Session::flash('home', "Can't load that page without a valid trip");
		Redirect::to('load.php');
	}
	try {
		$init = Info::get();
	} catch (Exception $e) {
		print_r($e->getMessage());
	}
	
	$invoiceNo = decode(Input::get('invoice_no'));
	$invoiceObj = new Invoice();
	$invoice = $invoiceObj->get(array('id', '=', $invoiceNo));

	$travelId = $invoice[0]->travel_id;
	$travelObj = new Travels('travels');
	$travel = $travelObj->get(array('id', '=', $travelId));
	

	$destination = $travel[0]->destination;
	$source = $travel[0]->source;

	$passengerObj = new Passenger('passengers');
	$routeObj = new Route('routes');
	$vehicleObj = new Vehicle('vehicles');	
	$driverObj = new Driver('drivers');
	$travelObj = new Travels('travels');
	$waybillObj = new Waybill('waybill');
	
	try {
		$passengers = $passengerObj->get(array('travel_id', '=', $travelId));
		$route = $routeObj->get(array('source', '=', $source, 'destination', '=', $destination));
		$vehicle = $vehicleObj->get(array('id', '=', $travel[0]->vehicle_id), 'id,plate_no,no_of_seats,ac')[0];
		$driver = $driverObj->get(array('id', '=', $travel[0]->driver_id))[0];
		$waybill = $waybillObj->get(array('travel_id', '=', $travelId));
		$passengerObj2 = new Passenger('temp_passengers');
		$passengerObj2->delete(array('hash', '=', Session::get(Config::get('session/load')),'user_id', '=', $invoice[0]->user_id));
		$driverObj->update($travel[0]->driver_id, array('status' => Config::get('status/travelling')));
		$vehicleObj->update($travel[0]->vehicle_id, array('status' => Config::get('status/travelling')));
		Cookie::delete(Config::get('cookie/load'));
		Session::delete(Config::get('session/load'));
	} catch (Exception $e) {
		die($e->getMessage());
	}
	$duration = (!empty($route)) ? $route[0]->duration: "Not set";
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Invoice</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="../css/main.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body onload="window.print();">
<div class="wrapper">
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
          			if(!empty($waybill)){
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
	        		$total = 0;
	        		foreach ($waybill as $key => $value) {
	        			$total += $value->weight; ?>
	        			<tr>
	        				<td><?php echo $key + 1 ?></td>
	        				<td><?php echo $value->item ?></td>
	        				<td><?php echo $value->weight ?></td>
	        			</tr>
	        	<?php }
	        	?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="2">Total weight:</th>
						<th><?php echo $total; ?></th>
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
          <b>Total:</b> <?php echo $invoice[0]->trip_amount ?><br>
        </div>
      </div>
      <!-- /.row -->
    </section>
  <!-- /.content -->
</div>
<!-- ./wrapper -->
</body>
</html>
