<?php
	require_once 'includes/content/header.php';
	if(!$user->checkPermission(array('staff', 'manager'))) {    //only staffs and manager can see it
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
        <small>Vehicle Request</small>
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
        <li class="active">Request</li>
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
            	<form role="form" method="post" name="request">
	                <?php
	                	if($parks) {
	                ?>
	                <div class="form-group">
		                <label>Select location to request from</label>
		                <select class="form-control select2" name="request_location" data-placeholder="Select a Park" style="width: 100%;" required>
		                	<option value="">--select--</option>
		                <?php
		                  	foreach ($parks as $value) {
		                  		if($value->id == $user->data()->location) continue; ?>
		                  		<option value="<?php echo $value->id; ?>"><?php echo $value->park; ?></option>
	                  	<?php } ?>
		                </select>
		            	</div>
			            <div class="form-group">
	                  <label for="no_of_vehicles">No of vehicles</label>
	                  <div class="input-group">
		                  <input type="number" class="form-control" id="no_of_vehicles" min="1" name="no_of_vehicles" value="<?php echo escape(Input::get('no_of_vehicles'))?>" required>
		                  <div class="input-group-addon">
				               	<i class="fa fa-database"></i>
				              </div>
				            </div>
				          </div>
		              <div class="form-group">
	                  <label for="note">Addtional note <small>(optional)</small></label>
	                  <div class="input-group">
	                  	<textarea name="note" id="note" cols="43" rows="4"><?php echo escape(Input::get('note'))?></textarea>
				          	</div>
		              </div>
		            		<?php } ?>
	            </div>
            <div class="box-footer">
                <input type="submit" class="btn btn-primary" name="send_request" value="Send Request">
            </div>
            </form>
					</div>
        </section>
        <section class="<?php echo ($user->hasPermission('manager'))? 'col-lg-5' : 'col-lg-8' ?> connectedSortable">
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
        <!-- right col -->
        <?php if($user->hasPermission('manager')) { ?>
        <section class="col-lg-3 connectedSortable right">
        	<div class="box box-success">
						<div class="box-header with-border">
              <h3 class="box-title">Requests</h3>
            	<div class="pull-right box-tools">
	                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
	                  <i class="fa fa-minus"></i></button>
	                <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
	                  <i class="fa fa-times"></i></button>
	            </div>
            </div>
            <div class="box-body">
            <?php $requests = array_reverse($vehicle->requestGet($user));
            	if(count($requests) != 0) { ?>
            	<table class="table table-condensed">
	            			<thead>
	            				<tr>
	            					<th>Park</th>
	            					<th>Request</th>
	            					<th>Action</th>
	            				</tr>
	            			</thead>
	            			<tbody>
            	<?php

            		foreach ($requests as $key => $value) { ?>
            				<tr>
            					<td><?php echo $parkObj->get($value->location_initiated, 'park')->park;?></td>
            					<td><?php $msg = explode($delimiter, $value->message);
            					  $con = "Requested for $msg[0] vehicle(s). ";
            					  if(isset($msg[1])) $con .= $msg[1];
            					  echo $con;
            					   ?></td>
            					<td><?php echo ($value->status == 0) ? "<button data-val={$value->id} class='btn btn-primary btn-sm respond' data-target='#request' data-toggle='modal'>Respond</button>" : "Responded" ?></td>
            				</tr>
            		<?php }?>
	            		</tbody>
	            			</table>
	            <?php } else { ?>
	            	<p class="text text-center">No requests.</p>
	            	<?php }?>
            </div>
					</div>
					<div class="example-modal">
					  <div id="request" class="modal fade" role="dialog">
					    <div class="modal-dialog">
					      <div class="modal-content">
					        <div class="modal-header">
					          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					            <span aria-hidden="true">&times;</span></button>
					          <h4 class="modal-title">Respond</h4>
					          <p class="passengererror text-danger"></p>
					        </div>
					        <div class="modal-body">
					          <form role="form" method="post" name="request" action="_vehiclerequest.php">
					            <input type="hidden" name="request" value="">
					            <div class="form-group">
				                <label>vehicle to send</label>
				                <select class="form-control select2" multiple="multiple" name="vehicle[]" data-placeholder="Select vehicle" style="width: 100%;" required>
				                <?php
				                	$vehiclesIn = $vehicle->get(array('current_location', '=', $user->data()->location, 'status', '=', Config::get('status/good')));
				                  	foreach ($vehiclesIn as $value) { ?>
				                  		<option value="<?php echo $value->id; ?>"><?php echo $value->plate_no; ?></option>
			                  	<?php } ?>
				                </select>
				            	</div>

					            <div class="form-group">
				                <label>Driver to send</label>
				                <select class="form-control select2" multiple="multiple" name="driver[]" data-placeholder="Select driver" style="width: 100%;" required>
				                <?php
				                	$driver = new Driver('drivers');
				                	$drivers = $driver->get(array('current_location', '=', $user->data()->location, 'status', '=', Config::get('status/active')));
				                  	foreach ($drivers as $value) { ?>
				                  		<option value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>
			                  	<?php } ?>
				                </select>
				            	</div>

					            <div class="box-footer">
					              <input type="submit" class="btn btn-primary" name="sendResponse" value="Send">
					            </div>
					          </form>
					        </div>
					      </div>
					      <!-- /.modal-content -->
					    </div>
					    <!-- /.modal-dialog -->
					  </div>
					  <!-- /.modal -->
					</div>
        </section><p class="test"></p>
        <?php } ?>
      </div>
      <!-- /.row (main row) -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<script type="text/javascript">
	$(document).ready(function() {
		var $disabled = (<?php echo $disabled; ?>);
		//disable fields that don't have vehicles in them currently
		$($disabled).each(function() {
			$("select[name=request_location] option[value="+this+"]").attr('disabled', 'disabled');
		});
		$('input[name=send_request]').click(function(e) {
			$('select[name=request_location] , input[name=no_of_vehicles]').each(function() {
				if(this.value == '') {
					//e.preventDefault();
					$(this).css('border', '1px solid red').focus();
				}
			});
		});
		$(document).on('click', "button.respond", function(e) {
			e.preventDefault();
			$("input[name=request]").val($(this).data('val'));
		}).on('click', "input[name=sendResponse]", function(e) {
			/*e.preventDefault();
			var $pass = true;
			$("select").each(function() {
				if($(this).val() == '') {
					$(this).css('border', "1px solid red");
					$pass = false;
				}
			});
			if(!$pass) {
				//return false;
			}
			$.post('_vehiclerequest.php', $("form[name=request]").serialize(), function($result) {
				if($result != 0) {
					$('button.close').click();
					var $val = $('input[name=request]').val();
					//$("button[data-val="+$val+"]").removeClass('btn-primary').addClass('btn-warning').text("Responded").attr('disabled', 'disabled');
					$("button[data-val="+$val+"]").replaceWith("Responded");
					$("select").filter("option:selected").attr('disabled', 'disabled');
					//location.reload(true);
				}else {
					alert("There was a problem responding to that request");
				}
			});*/
		}).one('mousemove', 'section.right', function() {
			$('section.left').removeClass('col-lg-4').addClass('col-lg-3');
			$('section.right').removeClass('col-lg-3').addClass('col-lg-4');
			$('textarea').attr('cols', '30');
			if(!($('body').hasClass('sidebar-collapse'))) {
        $('a.sidebar-toggle').click();                    //close navigation in this page
      }
		});

	})

</script>
<?php
	require_once 'includes/content/footer.php';
?>
