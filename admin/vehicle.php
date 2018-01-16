<?php
	require_once 'includes/content/header.php';
	if(!$user->checkPermission(array('admin', 'manager'))) {
	    Session::flash('home', "You don't have permission to view that page");
	  	Redirect::to('viewvehicles.php');
	}
	if(empty(Input::get('vehicle'))) {
		Session::flash('home', "please select a valid vehicle");
		Redirect::to('viewvehicles.php');
	}
	$vehicleId = decode(Input::get('vehicle'));
	$errors = array();
	$park = $parkObj->get();
	$vehicleObj = new Vehicle('vehicles');
	$vehicle = $vehicleObj->get(array('id', '=', $vehicleId))[0];
	$vehicleMaintain = new Vehicle('maintenance');
	$notice = new Notification();
	$history = array_reverse($vehicleMaintain->get(array('vehicle_id', '=', $vehicleId, 'status', '=', Config::get('status/good'))));
	$ongoing = array_reverse($vehicleMaintain->get(array('vehicle_id', '=', $vehicleId, 'status', '=', Config::get('status/faulty'))));
	$errors = array();
	if(Input::exists() && !empty(Input::get('submit_fault'))) {
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'fault' => array(
				'required' => true,
				),
			));

		if($validation->passed()) {
			try {
				$date = (empty(Input::get('date'))) ? "" : Input::get('date');
				if(!empty(Input::get('date'))) {
					$vehicleMaintain->create(array(
						'vehicle_id' => $vehicleId,
						'fault' => Input::get('fault'),
						'date_fault_occured' => $date,
						));
				} else{
					$vehicleMaintain->create(array(
						'vehicle_id' => $vehicleId,
						'fault' => Input::get('fault'),
						));
				}
					$vehicleObj->update($vehicleId, array(
						'status' => Config::get('status/faulty'),
						));

						$notice->create(array(
							'message' => "Vehicle ".$vehicle->plate_no." is out for maintenance",
							'initiated' => $user->data()->id,
							'location_initiated' => $user->data()->location,
							'affected' => Config::get('permissions/all'),
							'location_affected' => Config::get('permissions/all'),
							'category' => Config::get('notification/vehicle_bad'),
							));
					Session::flash('home', "Fault sent");
					Redirect::to("vehicle.php?vehicle=".encode($vehicleId));
			} catch (Exception $e) {
				print_r($e->getMessage());
			}
		} else {
			foreach ($validation->errors() as $error) {
				$errors[] = $error;
			}
		}
	}
	if(Input::exists() && !empty(Input::get('complete'))) {
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'work_done' => array(
				'required' => true,
				),
				'location' => array(
					'required' => true,
				),
				'current_state' => array(
					'required' => true,
				)
			));
		if($validation->passed()) {
			try {
				$date = (empty(Input::get('date'))) ? "" : Input::get('date');
				if(!empty(Input::get('fixed_date'))) {
					$vehicleMaintain->update(Input::get('maintain'),array(
						'work_done' => Input::get('work_done'),
						'technician' => Input::get('technician'),
						'date_fixed' => Input::get('fixed_date'),
						'status' => Config::get('status/good'),
						));
				} else{
					$vehicleMaintain->update(Input::get('maintain'),array(
						'work_done' => Input::get('work_done'),
						'technician' => Input::get('technician'),
						'status' => Config::get('status/good'),
						));
				}
				$vehicleObj->update($vehicleId, array(
					'status' => Input::get('current_state'),
					'current_location' => Input::get('location'),
					));
					if(Input::get('current_state') == Config::get('status/good')) {	//send notification only when vehicle is good again
						$notice->create(array(
							'message' => "Vehicle ".$vehicle->plate_no." is back from maintenance",
							'initiated' => $user->data()->id,
							'location_initiated' => $user->data()->location,
							'affected' => Config::get('permissions/all'),
							'location_affected' => Config::get('permissions/all'),
							'category' => Config::get('notification/vehicle_back'),
							));
					}
				Session::flash('home', "Vehicle's Record Updated.");
				Redirect::to("vehicle.php?vehicle=".encode($vehicleId));
			} catch (Exception $e) {
				print_r($e->getMessage());
			}
		} else {
			foreach ($validation->errors() as $error) {
				$errors[] = $error;
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
        <small>Vehicle's record</small>
      </h1>
      <?php if(Session::exists('home')) {
		        echo "<p class='text text-center'>".Session::flash('home')."</p>";
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
      <div style="display:inline-block; margin-bottom:1em;font-size:1.4em">
				<strong class="text-lg text text-success"><?php echo $vehicle->plate_no; ?></strong>
      </div>
			<div class="row">
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php echo DB::getInstance()->query("SELECT COUNT(*) AS count FROM travels WHERE vehicle_id = {$vehicleId}")->first()->count ?></h3>

              <p>Total trips</p>
            </div>
            <div class="icon">
              <i class="ion ion-paper-airplane"></i>
            </div>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php echo $vehicleMaintain->downTime($vehicleId, $vehicle->date_created) ?><sup style="font-size: 20px">%</sup></h3>

              <p>Down time</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
							<?php $span = $vehicleObj->date($vehicle->date_created);
							if(strlen($span) < 9) {
								echo "<h3>".$span."</h3>";
							}else {
								echo "<h4>".$span."</h4>";
							}
							?>

              <p>Life span</p>
            </div>
            <div class="icon">
              <i class="ion ion-calendar"></i>
            </div>
          </div>
        </div>

        <!-- ./col -->
      </div>
      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <section class="col-lg-8 connectedSortable">
						<div class="nav-tabs-custom">
	            <ul class="nav nav-tabs">
	              <li class="active"><a href="#record" data-toggle="tab">Record</a></li>
	              <li><a href="#new" data-toggle="tab">New fault</a></li>
	            </ul>
	            <div class="tab-content">
	            	<div class="tab-pane active" id="record">
									<?php if($ongoing) {?>
										<h3>Ongoing work</h3>
										<?php foreach ($ongoing as $key => $value) {?>
											<div class="row" style="margin-bottom:0.4em;">
												<div>
													<div class="col-md-2">
														<span class="label label-warning"><?php echo explode(" ",$value->date_fault_occured)[0]; ?></span>
													</div>
													<div class="col-md-8">
														<p class="text fault"><?php echo $value->fault?></p>
													</div>
													<div class="col-md-2">
														<button data-val="<?php echo $value->id; ?>" class="btn btn-sm btn-info pull-right done">Done</button>
													</div>
												</div>
											</div>
									<?php } } if(count($history) != 0) { ?>
										<h3>Maintenance Record</h3>
	            		<table class="table table-hover table-condensed">
			                <thead>
			                	<tr>
			                		<th></th>
			                		<th>Date</th>
			                		<th>Fault</th>
			                		<th>Work Done</th>
			                		<th>Date Fixed</th>
													<th>Technician</th>
			                	</tr>
			                </thead>
			                <tbody>
	            		<?php
	            		foreach ($history as $key => $value) {;
	            			?>
			                	<tr>
			                		<td><?php echo ($key + 1);?></td>
			                		<td><span class="label label-danger"><?php echo explode(" ",$value->date_fault_occured)[0]; ?></span></td>
			                		<td><?php echo $value->fault?></td>
			                		<td><?php echo $value->work_done?></td>
			                		<td><span class="label label-success"><?php echo explode(' ',$value->date_fixed)[0]; ?></span></td>
													<td><?php echo $value->technician ?></td>
			                	</tr>
	              		<?php } ?>
	              			</tbody>
		                </table>
									<?php }
									if(count($ongoing) == 0 && count($history) == 0) {?>
										<p>No Maintenance record for this Vehicle</p>
									<?php } ?>
	              	</div>
	              	<div class="tab-pane" id="new">
	              		<form role="form" method="post" class="form-horizontal">
											<div class="form-group">
		                    <label for="fault" class="col-sm-2 control-label">Fault</label>
		                    <div class="col-sm-10">
		                      <textarea rows="3" class="form-control" id="fault" name="fault" placeholder="Brief description of fault" required autofocus></textarea>
		                    </div>
		                  </div>
											<div class="form-group">
		                    <label for="date" class="col-sm-2 control-label">Date fault occured</label>
		                    <div class="col-sm-10">
													<input class="form-control" type="date" name="date" id="date">
		                    </div>
		                  </div>
											<div class="form-group" style="margin-right:0px">
												<input class="btn btn-sm btn-primary pull-right" type="submit" name="submit_fault" value="Add"/>
											</div>
										</form>
	              	</div>

	            </div>
	          </div>
        </section>
        <!-- /.Left col -->
				<section class="col-lg-4 connectedSortable right">
					<div class="box">
						<div class="box-body">
							<h3>Job Complete</h3>
							<form role="form" method="post" name="complete">
								<p class="fault_desc"></p>
								<input type="hidden" name="maintain" value="">
									<div class="form-group">
										<label for="work">Work Done</label>
										<div class="col-md-10 input-group">
											<textarea rows="3" class="form-control" id="work" name="work_done" placeholder="Brief description of work done on vehicle" required autofocus></textarea>
										</div>
									</div>

									<div class="form-group">
										<label for="technician">Technician's Details (optional)</label>
										<div class="col-md-10 input-group">
											<textarea rows="3" class="form-control" id="technician" name="technician" placeholder="Brief info about who and where the work was done such as mechanic's contact details." ></textarea>
										</div>
									</div>

									<div class="form-group">
										<label for="date_fixed">Date Fixed (yyyy-mm-dd)</label>
										<div class="col-md-10 input-group">
											<input class="form-control" type="date" name="date_fixed" id="date_fixed">
										</div>
									</div>

									<div class="form-group">
										<label>Current State</label>
										<div class="col-md-10 input-group">
											<select style="text-transform:capitalize;" class="form-control" id="current_state" name="current_state" style="width: 100%;" required>
												<option value="">--select--</option>
												<option value="<?php echo Config::get('status/good'); ?>" selected>Active</option>
												<option value="<?php echo Config::get('status/faulty'); ?>">Still Faulty</option>
												<option value="<?php echo Config::get('status/out'); ?>">Out</option>
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="location">Current Location</label>
										<div class="col-md-10 input-group">
										<select style="text-transform:capitalize;" id="location" class="form-control select2" name="location" style="width: 100%;" required>
											<option value="">--select--</option>
											<?php
												foreach ($park as $value) { ?>
													<option value="<?php echo $value->id; ?>" <?php if($value->id == $user->data()->location) echo "selected=selected"?>><?php echo $value->park; ?></option>
												<?php } ?>
										</select>
									</div>
									</div>

									<input type="submit" class="btn btn-sm pull-right" name="complete" value="Send" />
								</form>
						</div>
					</div>
				</section>
      </div>
      <!-- /.row (main row) -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<?php
	if($user->hasPermission('admin') || $user->hasPermission('manager')) {
?>
<script type="text/javascript">
 	$(document).ready(function() {
		$('section.right').hide();
		$('section').on('click', 'button.done', function() {
			$('section.right').fadeIn('slow');
			$('p.fault_desc').text($(this).parent().siblings('.col-md-8').find('p.fault').text());
			$('input[name=maintain]').val($(this).data('val'));
		}).on('click', 'input[name=complete]', function(e) {
			var pass = true;
			$("textarea[name=work_done], select[name=location]").each(function(index, val) {
				if($(this).val() == '') {
					$(this).focus();
					$(this).parent().css('border', '1px solid #ff0011');
					e.preventDefault();
					pass = false;
				}
			})
			if(pass) {
				var $confirm = confirm("By clicking Send you are changing the status of the vehicle to active in the current location");
				if(!$confirm) {
					e.preventDefault();
				}
			}
		});


 	})
</script>
<?php }
	require_once 'includes/content/footer.php';
?>
