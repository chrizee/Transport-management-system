<?php 
	require_once 'includes/content/header.php';
	if(!$user->checkPermission(array('staff', 'manager'))) {    //only ceo and manager can see it
	    Session::flash('home', "You don't have permission to view that page");
	    Redirect::to('dashboard.php');
	}
	$delimiter = "::";
	$vehicle = new Vehicle('vehicles');
	$parks = $parkObj->get(null, 'id,park');
	$success = true;
	$errors = array();
	if(Input::exists() && !empty(Input::get('send_request'))) {
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'no_of_vehicles' => array(
				'required' => true,
				'min' => 1,
				),
			'request_location' => array(
				'required' => true,
				),
			));
		
		if($validation->passed()) {
			if(!$vehicle->requestCheck(Input::get('request_location'), Input::get('no_of_vehicles'))) {
				$success = false;
				Session::flash('home', "The amount of vehicles requested is not in the target park.Select another park to request from or reduce the number of vehicles in the request");
			}
			if($success){
				try {
					$request = new Notification();
					$request->create(array(
						'message' => Input::get('no_of_vehicles').$delimiter. Input::get('note'),
						'initiated' => $user->data()->id,
						'location_initiated' => $user->data()->location,
						'affected' => Config::get('permissions/manager'),
						'location_affected' => Input::get('request_location'),
						'category' => Config::get('notification/request_vehicle'),
						));
						Session::flash('home', "Request sent successfully");
						Redirect::to($_SERVER['PHP_SELF']);
				} catch (Exception $e) {
					print_r($e->getMessage());
				}
			}
		} else {
			foreach ($validation->errors() as $error) {
				$errors[] = str_replace('_', ' ', $error);
			}
		}	
	}
?>
	
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?php echo $parkObj->get($user->data()->location, 'park')->park; ?>
        <small>Accounts</small>
      </h1>
      <?php if(Session::exists('home')) {
		        echo "<p class='created text text-center text-danger'>".Session::flash('home')."</p>";
		    }
		    if($errors) {
    			foreach ($errors as  $value) {
    				echo "<p class='text text-center'>$value</p>";
    			}
    		}
      ?>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">View</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">    
      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <section class="col-lg-4 connectedSortable left">
					<div class="box box-success">
						<div class="box-header with-border">
              <h3 class="box-title">New Request</h3>
            	<div class="pull-right box-tools">
	                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
	                  <i class="fa fa-minus"></i></button>
	                <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
	                  <i class="fa fa-times"></i></button>
	            </div>
            </div>
            <div class="box-body">
            	
	          </div>
            <div class="box-footer">
            	
            </div>
					</div> 
        </section>
        <section class="col-lg-8 connectedSortable">
        	<div class="box box-success">
						<div class="box-header with-border">
              <h3 class="box-title">Vehicle stats in parks(only good vehicles are shown here)</h3>
              <?php if(!$user->hasPermission('manager')) { ?>
            	<div class="pull-right box-tools">
	                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
	                  <i class="fa fa-minus"></i></button>
	                <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
	                  <i class="fa fa-times"></i></button>
	            </div>
	            <?php } ?>
            </div>
            <div class="box-body">
            	<?php
            		$disabled = array();
            		
            		foreach ($parks as $key => $value) {
            			//if($value->id == $user->data()->location) continue;
            			$vehicles = $vehicle->get(array('current_location', '=', $value->id, 'status', '=', Config::get('status/good')));
            			?>
            		<h3><?php echo $value->park; ?></h3>
            		<?php if(!empty($vehicles)) {?>
            			<table class="table table-condensed">
	            			<thead>
	            				<tr>
	            					<th>Vehicle</th>
	            					<th>seats</th>
	            					<th>AC</th>
	            				</tr>
	            			</thead>
	            			<tbody>
            				<?php foreach ($vehicles as $key => $value) {?>
            				<tr>
            					<td><?php echo $value->plate_no?></td>
            					<td><?php echo $value->no_of_seats?></td>
            					<td><?php echo ($value->ac == 1) ? 'Yes' : "No" ?></td>
            				</tr>
            			
            		<?php }?>
	            		</tbody>
	            			</table> 
	            	<?php }else{
	            		$disabled[] = $value->id;
	            		?>
            			<p>No Vehicle in <?php echo $value->park; ?></p> 
            		<?php } }  $disabled = json_encode($disabled);
            	?>
            </div>
					</div>
        </section>
        
      </div>
      <!-- /.row (main row) -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<script type="text/javascript">
	$(document).ready(function() {

	})

</script> 
<?php 
	require_once 'includes/content/footer.php';
?>