<?php
	require_once 'includes/content/header.php';
	if(!$user->checkPermission(array('*'))) {    //all can see it
	    Session::flash('home', "You don't have permission to view that page");
	    Redirect::to('dashboard');
	}
	$errors = array();
	$vehicle = new Vehicle('vehicles');
	$vehicleInfo = $vehicle->get(array('1', '=', '1'));
	$parkGet = $parkObj->get();
	$errors = array();
	if(Input::exists()) {
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'model' => array(
				'required' => true,
				'max' => 30,
				),
			'brand' => array(
				'required' => true,
				'max' => 30,
				),
			 'seat_no' => array(
				'required' => true,
				),
			 'plate_no' => array(
				'required' => true,
				'max' => 15,
				),
			 'current_state' => array(
			 	'required' => true,
			 	),
				'location' => array(
					'required' => true,
				)
			));

		if($validation->passed()) {

			try {
				$vehicle->update(Input::get('id'), array(
					'plate_no' => Input::get('plate_no'),
					'no_of_seats' => Input::get('seat_no'),
					'model' => Input::get('model'),
					'ac' => Input::get('ac'),
					'brand' => Input::get('brand'),
					'status' => Input::get('current_state'),
					'current_location' => Input::get('location'),
					));
					Session::flash('home', "Vehicle updated successfully");
					Redirect::to('viewvehicles');
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
        <small>Vehicles</small>
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
    <?php
		if($user->hasPermission('admin') || $user->hasPermission('manager')) {
	?>
      <div style="display:inline-block;">
      	<a href="createvehicle"><button class="btn btn-success pull-right">New <i class="fa fa-plus"></i></button></a>
      </div>
    <?php } ?>
      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <section class="col-lg-12 connectedSortable">
					<div class="box box-success">
						<div class="box-header with-border">
		          <h3 class="box-title">Vehicles</h3>
		        	<div class="pull-right box-tools">
		              <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
		                <i class="fa fa-minus"></i></button>
		              <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
		                <i class="fa fa-times"></i></button>
		          </div>
		        </div>
		        <div class="box-body">
		         	<table class="table table-bordered table-hover table-striped datatable">
		              <thead>
		                <tr>
		                  <th>Plate No</th>
		                  <th>Seat Capacity</th>
		                  <th>AC</th>
		                  <th>Model</th>
		                  <th>Brand</th>
		                  <th>Date Acquired</th>
		                  <th>Current state</th>
		                  <th>Current Location</th>
		                  <?php
								    		if($user->hasPermission('admin') || $user->hasPermission('manager')) {
								    	?>
		                  <th></th>
		                  <?php } ?>
		                </tr>
		              </thead>
		            	<tbody>
		            	<?php
		            		foreach ($vehicleInfo as $key => $value) {
		            	?>
		                <tr>
		                	<?php if($user->hasPermission('admin') || $user->hasPermission('manager')) {?>
		                  <td class="plate_no" title="view complete details and history of maintenance"><a href="vehicle_<?php echo encode($value->id)?>"><?php echo $value->plate_no ?></a></td>
		                  <?php } else {?>
		                  	<td class="plate_no"><?php echo $value->plate_no ?></td>
		                  	<?php } ?>
		                  <td class="seat_no"><?php echo $value->no_of_seats ?></td>
		                  <td class="ac"><?php echo ($value->ac ==0) ? "No" : "Yes" ?></td>
		                  <td class="model"><?php echo $value->model ?></td>
		                  <td class="brand"><?php echo $value->brand ?></td>
		                  <td><?php echo $value->date_created ?></td>
		                  <td class="status"><?php
			                  switch ($value->status) {
			                  	case Config::get('status/out'):
			                  		echo "Out";
			                  		break;
			                  	case Config::get('status/good'):
			                  		echo "Active";
			                  		break;
			                  	case Config::get('status/faulty'):
			                  		echo "Faulty";
			                  		break;
			                  	case Config::get('status/selected'):
			                  		echo "Loading";
			                  		break;
			                  	case Config::get('status/travelling'):
			                  		echo "Travelling";
			                  		break;
			                  	default:
			                  		echo "Undefined";
			                  		break;
			                  }
		                  	?>
		                 	</td>
		                 	<td class="location"><?php echo $parkObj->get($value->current_location, 'park')->park; ?></td>
		                 	<?php
								    		if($user->hasPermission('admin') || $user->hasPermission('manager')) {
								    	?>
		                 	<td><button title="edit mistakes made during creation" class="btn btn-info edit" data-toggle="modal" name="<?php echo $value->id;?>" data-target="#vehicles">Edit</button></td>
		                 	<?php } ?>
		                </tr>
		              <?php } ?>
		            </tbody>
		          </table>
		        </div>
					</div>
					<?php
					 if($user->hasPermission('admin') || $user->hasPermission('manager')) {
				 ?>
					<div class="example-modal">
		        <div id="vehicles" class="modal fade" role="dialog">
		          <div class="modal-dialog">
		            <div class="modal-content">
		              <div class="modal-header">
		                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                  <span aria-hidden="true">&times;</span></button>
		                <h4 class="modal-title">Edit</h4>
		              </div>
		              <form role="form" method="post" id="vehicleedit" action="" name="vehicleedit">
		              	<input type="hidden" name="id" />
		              	<div class="modal-body">
		                	<div class="form-group">
		                  <label for="model">Model</label>
		                  <div class="input-group">
			                  <input type="text" class="form-control" id="model" name="model" required>
			                  <div class="input-group-addon">
				               	<i class="fa fa-bus"></i>
				               </div>
				          		</div>
		                </div>

		                <div class="form-group">
		                  <label for="brand">Brand</label>
		                  <div class="input-group">
			                  <input type="text" class="form-control" id="brand" name="brand" min="4" required>
			                  <div class="input-group-addon">
				               	<i class="fa fa-automobile"></i>
				               </div>
				          		</div>
		                </div>

		                <div class="form-group">
		                  <label for="seat">Number Of Seat</label>
		                  <div class="input-group">
			                  <input type="number" class="form-control" id="seat" name="seat_no" min="4" required>
			                  <div class="input-group-addon">
				               	<i class="fa fa-chain"></i>
				               </div>
				          		</div>
		                </div>

		                <div class="form-group">
		                  <label for="plate_no">Plate No</label>
		                  <div class="input-group">
			                  <input type="text" class="form-control" id="plate_no" name="plate_no" required>
			                  <div class="input-group-addon">
				               	<i class="fa fa-tag"></i>
				               </div>
				          		</div>
		                </div>

		                <div class="form-group">
			                <label>AC &nbsp;</label>
			                <label>
			                	<input type="radio" class="ac_yes" name="ac" value="1"> Yes
			                </label>
			                <label>
			                	<input type="radio" class="ac_no" name="ac" value="0"> No
			                </label>
		                </div>

										<div class="form-group">
											<label>Location</label>
											<select style="text-transform:capitalize;" class="form-control" id="location" name="location" style="width: 100%;">
												<option value="">--select--</option>
												<?php
													foreach ($parkGet as $value) { ?>
														<option value="<?php echo $value->id; ?>"><?php echo $value->park; ?></option>
													<?php } ?>
											</select>
										</div>

		                <div class="form-group">
			                <label>Current State</label>
			                <select style="text-transform:capitalize;" class="form-control" id="current_state" name="current_state" style="width: 100%;" required>
			                  <option value="">--select--</option>
		                		<option value="<?php echo Config::get('status/good'); ?>">Active</option>
		                		<option value="<?php echo Config::get('status/faulty'); ?>">Faulty</option>
		                		<option value="<?php echo Config::get('status/travelling')?>">Travelling</option>
		                		<option value="<?php echo Config::get('status/out'); ?>">Out</option>
			                </select>
			            	</div>

			              <div class="modal-footer">
			                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
			                <input type="submit" class="btn btn-sm btn-primary" name="editvehicle" value="Save changes">
			              </div>
			            </form>
		            </div>
		            <!-- /.modal-content -->
		          </div>
		          <!-- /.modal-dialog -->
		        </div>
		        <!-- /.modal -->
		    	</div>
				<?php } ?>
        </section>
        <!-- /.Left col -->

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
 		$('button.edit').click(function(e) {
 			e.preventDefault();
 			var parent = $(this).parent();
 			$('input[name=model]').val($(parent).siblings('.model').text());
 			$('input[name=brand]').val($(parent).siblings('.brand').text());
 			$('input[name=plate_no]').val($(parent).siblings('.plate_no').text());
 			$('input[name=seat_no]').val($(parent).siblings('.seat_no').text());
 			$('input[name=engine_no]').val($(parent).siblings('.engine_no').text());
 			$('input[name=id]').val($(this).attr('name'));

 			var state = $(parent).siblings('.status').text().trim().toLowerCase();
 			$('select[name=current_state] option').each(function() {
				if($(this).text().trim().toLowerCase() == state) {
					$(this).attr('selected', 'selected');
				}
			});

			var location = $(parent).siblings('.location').text().trim().toLowerCase();
 			$('select[name=location] option').each(function() {
				if($(this).text().trim().toLowerCase() == location) {
					$(this).attr('selected', 'selected');
				}
			});

			var ac = $(parent).siblings('.ac').text().trim().toLowerCase();
			if(ac == 'yes') {
				$('input.ac_yes').click();
			} else {
				$('input.ac_no').click();
			}
 		});
 	})
</script>
<?php }
	require_once 'includes/content/footer.php';
?>
