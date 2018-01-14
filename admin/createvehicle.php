<?php 
	require_once 'includes/content/header.php';
	if(!$user->checkPermission(array('manager', 'admin'))) {    //only ceo and manager can see it
	    Session::flash('home', "You don't have permission to view that page");
	    Redirect::to('dashboard.php');
	}
	$park = $parkObj->get();
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
				'numeric' => 4,
				),
			 'plate_no' => array(
				'required' => true,
				'max' => 15,
				),
			 'engine_no' => array(
				'required' => true,
				'max' => 30,
				),
			 'location' => array(
			 	'required' => true,
			 	)
			));

		if($validation->passed()) {
			try {
				$vehicle = new Vehicle('vehicles');
				$vehicle->create(array(
					'plate_no' => Input::get('plate_no'),
					'no_of_seats' => Input::get('seat_no'),
					'engine_no' => Input::get('engine_no'),
					'model' => Input::get('model'),
					'ac' => Input::get('ac'),
					'brand' => Input::get('brand'),
					'current_location' => Input::get('location'),
					));
					Session::flash('home', "Vehicle added successfully");
					Redirect::to($_SERVER['PHP_SELF']);	
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
        <small>New Vehicles</small>
      </h1>
      <?php if(Session::exists('home')) {
		        echo "<p class='text text-center text-danger'>".Session::flash('home')."</p>";
		    }
		    if($errors) {
    			foreach ($errors as  $value) {
    				echo "<p class='text text-center'>$value</p>";
    			}
    		}
      ?>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Create</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      
      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <section class="col-lg-5 connectedSortable">
			<div class="box box-success">
				<div class="box-header with-border">
	              <h3 class="box-title">Enter Vehicle Info</h3>
	            	<div class="pull-right box-tools">
		                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
		                  <i class="fa fa-minus"></i></button>
		                <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
		                  <i class="fa fa-times"></i></button>
		            </div>
	            </div>
	            <div class="box-body">
	            	<form role="form" method="post" name="vehicle">
		                <div class="form-group">
		                  <label for="model">Model</label>
		                  <div class="input-group">
			                  <input type="text" class="form-control" id="model" name="model" value="<?php echo escape(Input::get('model'))?>" required>
			                  <div class="input-group-addon">
				               	<i class="fa fa-database"></i>
				               </div>
				          </div>
		                </div>

		                <div class="form-group">
		                  <label for="brand">Brand</label>
		                  <div class="input-group">
			                  <input type="text" class="form-control" id="brand" name="brand" value="<?php echo escape(Input::get('brand'))?>" required>
			                  <div class="input-group-addon">
				               	<i class="fa fa-database"></i>
				               </div>
				          </div>
		                </div>

		                <div class="form-group">
		                  <label for="seat">Number Of Seat</label>
		                  <div class="input-group">
			                  <input type="number" class="form-control" id="seat" name="seat_no" min="4" value="<?php echo escape(Input::get('seat_no'))?>" required>
			                  <div class="input-group-addon">
				               	<i class="fa fa-database"></i>
				               </div>
				          </div>
		                </div>

		                <div class="form-group">
		                  <label for="plate_no">Plate No</label>
		                  <div class="input-group">
			                  <input type="text" class="form-control" id="plate_no" name="plate_no" value="<?php echo escape(Input::get('plate_no'))?>" required>
			                  <div class="input-group-addon">
				               	<i class="fa fa-database"></i>
				               </div>
				          </div>
		                </div>

		                <div class="form-group">
		                  <label for="engine_no">Engine No</label>
		                  <div class="input-group">
			                  <input type="text" class="form-control" id="engine_no" name="engine_no" value="<?php echo escape(Input::get('engine_no'))?>" required>
			                  <div class="input-group-addon">
				               	<i class="fa fa-database"></i>
				               </div>
				          </div>
		                </div>

		                <div class="form-group">
			                <label>AC &nbsp;</label>
			                <label>
			                	<input type="radio" name="ac" value="1"> Yes
			                </label>
			                <label>
			                	<input type="radio" name="ac" value="0" checked> No
			                </label>
		                </div>

		                <div class="form-group">
			                <label>Current Location</label>
			                <select style="text-transform:capitalize;" class="form-control select2" name="location" style="width: 100%;" required>
			                  <option value="">--select--</option>
			                  <?php
			                  	foreach ($park as $value) { ?>
			                  		<option value="<?php echo $value->id; ?>"><?php echo $value->park; ?></option>
			                  	<?php } ?>
			                </select>
			            </div>
		            
	            </div>
	            <div class="box-footer">
	                <input type="submit" class="btn btn-primary" name="vehicleCreate" value="Add Vehicle">
	            </div>
	            </form>
			</div> 
        </section>
        <!-- /.Left col -->
        <!-- right col (We are only adding the ID to make the widgets sortable)-->
        <section class="col-lg-7 connectedSortable">
        	     
        </section>
        <!-- right col -->
      </div>
      <!-- /.row (main row) -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
 
<?php 
	require_once 'includes/content/footer.php';
?>