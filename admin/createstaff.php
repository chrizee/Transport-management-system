<?php
	require_once 'includes/content/header.php';
	if(!$user->checkPermission(array('manager', 'admin'))) {    //only ceo and manager can see it
	    Session::flash('home', "You don't have permission to view that page");
	    Redirect::to('dashboard.php');
	}
	$park = $parkObj->get();
	$errors = array();
	//$notice = new Notification();
	if(Input::exists()) {
		if(Input::get('staff')) {
			$validate = new Validate();
			$validation = $validate->check($_POST, array(
				'name' => array(
					'required' => true,
					'max' => 60,
					),
				'email' => array(
					'required' => true,
					'max' => 60,
					),
				'password1' => array(
					'required' =>true,
					'min' => 6
					),
				'password2' => array(
					'required' =>true,
					'matches' =>'password1',
					),
				'phone' => array(
					'required' => true,
					'max' => 11,
					'function' => 'checkPhone',
					),
				'address' => array(
					'required' => true,
					'max' => 100,
					),
				'location' => array(
					'required' => true,
					)
				));

			if($validation->passed()) {
        		$salt = Hash::salt(32);

				try {
					$user->create(array(
						'name' => Input::get('name'),
						'password' => Hash::make(Input::get('password1'),$salt),
						'email' => Input::get('email'),
						'phone' => Input::get('phone'),
						'address' => Input::get('address'),
						'gender' => Input::get('gender'),
						'salt' => $salt,
						'location' => Input::get('location'),
						'groups' => Input::get('role')
						));
						$location =  $parkObj->get(Input::get('location'), 'park')->park;
						$notification->create(array(
							'message' => Input::get('name')." is now a staff at " .$location,
							'initiated' => $user->data()->id,
							'location_initiated' => $user->data()->location,
							'affected' => Config::get('permissions/all'),
							'location_affected' => Config::get('permissions/all'),
							'category' => Config::get('notification/staff_add'),
							));
						Session::flash('home', "Staff created successfully");
						Redirect::to('createstaff.php');
				} catch (Exception $e) {
					print_r($e->getMessage());
				}
			} else {
				foreach ($validation->errors() as $error) {
					$errors[] = $error;
				}
			}
		}
		if(Input::get('driver')) {
			$validate = new Validate();
			$validation = $validate->check($_POST, array(
				'name' => array(
					'required' => true,
					'max' => 60,
					),
				'email' => array(
					'required' => true,
					'max' => 60,
					),
				'phone' => array(
					'required' => true,
					'max' => 11,
					'function' => 'checkPhone',
					),
				'current_location' => array(
					'required' => true,
					),
				));

			if($validation->passed()) {

				try {
					$driver = new Driver('drivers');
					$driver->create(array(
						'name' => Input::get('name'),
						'email' => Input::get('email'),
						'phone' => Input::get('phone'),
						'current_location' => Input::get('current_location'),
						));
						$notification->create(array(
							'message' => Input::get('name')." is now a driver",
							'initiated' => $user->data()->id,
							'location_initiated' => $user->data()->location,
							'affected' => Config::get('permissions/all'),
							'location_affected' => Config::get('permissions/all'),
							'category' => Config::get('notification/driver_add'),
							));
					Session::flash('home', "Driver created successfully");
					Redirect::to('createstaff.php');
				} catch (Exception $e) {
					print_r($e->getMessage());
				}
			} else {
				foreach ($validation->errors() as $error) {
					$errors[] = $error;
				}
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
        <small>New Registration</small>
      </h1>
      <?php if(Session::exists('home')) {
        echo "<p class='text text-center'>".Session::flash('home')."</p>";
        }
      ?>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Create</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
			<div style="display:inline-block;">
      	<a href="viewstaff.php"><button class="btn btn-sm btn-success pull-right">View staffs <i class="fa fa-link"></i></button></a>
      </div>
      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <section class="col-lg-5 connectedSortable">
			<div class="box box-success">
				<div class="box-header with-border">
	              <h3 class="box-title">Select category to add</h3>
	            	<div class="pull-right box-tools">
		                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
		                  <i class="fa fa-minus"></i></button>
		                <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
		                  <i class="fa fa-times"></i></button>
		            </div>
	            </div>
	            <div class="box-body">
	            	<form role="form" method="post" name="test">
		                <div class="form-group">
		                  <label></label>
		                  <select class="form-control" name="staff" required>
												<option value="">--Select--</option>
												<option value="driver">Driver</option>
												<option value="staff">Staff</option>
		                  </select>
		                </div>
		            </form>
	            </div>
			</div>
        </section>
        <!-- /.Left col -->
        <!-- right col (We are only adding the ID to make the widgets sortable)-->
        <section class="col-lg-7 connectedSortable">
        	<div class="callout callout-warning">
		        <h4>Notice!</h4>Use PHPMailer to send password to mail upon registration of staff.
		    </div>
        	<?php if($errors) {
        			foreach ($errors as  $value) {
        				echo "<p class='text text-center'>$value</p>";
        			}
        		}
        	?>
        	<div class="box box-primary hidden big">
	            <div class="box-header with-border">
	              <h3 class="box-title"> </h3>
	              <div class="pull-right box-tools">
		                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
		                  <i class="fa fa-minus"></i></button>
		                <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
		                  <i class="fa fa-times"></i></button>
		            </div>
	            </div>
	            <!-- /.box-header -->
	            <!-- form start -->
	            <form role="form" class="hidden" method="post" action="<?php echo $_SERVER['PHP_SELF']?>" id="staff">
	              <div class="box-body">
	                <div class="form-group">
	                  <label for="staffname">Name</label>
	                  <div class="input-group">
		                  <input type="text" class="form-control" id="staffname" name="name" value="<?php echo escape(Input::get('name'))?>">
		                  <div class="input-group-addon">
			               	<i class="text-success fa fa-user"></i>
			               </div>
			          </div>
	                </div>

	                <div class="form-group">
	                  <label for="email">Email</label>
	                  <div class="input-group">
		                  <input type="email" class="form-control" id="email" name="email" value="<?php echo escape(Input::get('email'))?>">
		                  <div class="input-group-addon">
			               	<i class="text-success fa fa-envelope"></i>
			               </div>
			          </div>
	                </div>

	                <div class="form-group">
	                  <label for="password1">Password</label>
	                  <div class="input-group">
		                  <input type="password" class="form-control" id="password1" name="password1" value="<?php echo escape(Input::get('password1'))?>">
		                  <div class="input-group-addon">
			               	<i class="text-success fa fa-lock"></i>
			               </div>
			          </div>
	                </div>

	                <div class="form-group">
	                  <label for="password2">Re-enter password</label>
	                  <div class="input-group">
		                  <input type="password" class="form-control" id="password2" name="password2" value="<?php echo escape(Input::get('password2'))?>">
		                  <div class="input-group-addon">
			               	<i class="text-success fa fa-lock"></i>
			               </div>
			          </div>
	                </div>

	                <div class="form-group">
	                  <label for="phone">Phone</label>
	                  <div class="input-group">
		                  <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo escape(Input::get('phone'))?>">
		                  <div class="input-group-addon">
			               	<i class="text-success fa fa-phone"></i>
			               </div>
			          </div>
	                </div>

	                <div class="form-group">
	                  <label for="address">Address</label>
	                  <div class="input-group">
		                  <input type="address" class="form-control" id="address" name="address" value="<?php echo escape(Input::get('address'))?>">
		                  <div class="input-group-addon">
			               	<i class="text-success fa fa-building"></i>
			               </div>
			          </div>
	                </div>

	                <div class="form-group">
		                <label>Location</label>
		                <select style="text-transform:capitalize;" class="form-control select2" name="location" style="width: 100%;" required>
		                  <option value="">--select--</option>
		                  <?php
		                  	foreach ($park as $value) { ?>
		                  		<option value="<?php echo $value->id; ?>"><?php echo $value->park; ?></option>
		                  	<?php } ?>
		                </select>
		            </div>

		             <div class="form-group">
		                <label>Role</label>
		                <select style="text-transform:capitalize;" class="form-control select2" name="role" style="width: 100%;" required>
		                  <option value="">--select--</option>
		                  		<option value="1">Loading officer</option>
		                  		<option value="4">Waybill officer</option>
		                  		<?php
		                  			if($user->hasPermission('admin')) {
		                  		?>
		                  		<option value="3">Branch Manager</option>
		                  		<option value="2">CEO</option>
		                  		<?php }?>
		                </select>
		            </div>

	                <div class="form-group">
		                <label>Gender &nbsp;</label>
		                <label>
		                	<input type="radio" name="gender" value="M"> Male
		                </label>
		                <label>
		                	<input type="radio" name="gender" value="F"> Female
		                </label>
	                </div>
	              </div>
	              <!-- /.box-body -->

	              <div class="box-footer">
	                <input type="submit" class="btn btn-primary" name="staff" value="Submit">
	              </div>
	            </form>

	            <form role="form" class="hidden" method="post" action="<?php echo $_SERVER['PHP_SELF']?>" id="driver">
	              <div class="box-body">
	                <div class="form-group">
	                  <label for="drivername">Name</label>
	                  <div class="input-group">
		                  <input type="text" class="form-control" id="drivername" name="name" value="<?php echo escape(Input::get('name'))?>">
		                  <div class="input-group-addon">
			               	<i class="text-success fa fa-user"></i>
			               </div>
			          </div>
	                </div>

	                <div class="form-group">
	                  <label for="driveremail">Email</label>
	                  <div class="input-group">
		                  <input type="email" class="form-control" id="driveremail" name="email" value="<?php echo escape(Input::get('email'))?>">
		                  <div class="input-group-addon">
			               	<i class="text-success fa fa-envelope"></i>
			               </div>
			          </div>
	                </div>

	                <div class="form-group">
	                  <label for="driverphone">Phone</label>
	                  <div class="input-group">
		                  <input type="tel" class="form-control" id="driverphone" name="phone" value="<?php echo escape(Input::get('phone'))?>">
		                  <div class="input-group-addon">
			               	<i class="text-success fa fa-phone"></i>
			               </div>
			          </div>
	                </div>

	                <div class="form-group">
		                <label>Current Location</label>
		                <select style="text-transform:capitalize;" class="form-control select2" name="current_location" style="width: 100%;" required>
		                  <option value="">--select--</option>
		                  <?php
		                  	foreach ($park as $value) { ?>
		                  		<option value="<?php echo $value->id; ?>" <?php if($user->data()->location == $value->id) echo "selected='selected'"?>><?php echo $value->park; ?></option>
		                  	<?php } ?>
		                </select>
		            </div>

	              </div>
	              <!-- /.box-body -->

	              <div class="box-footer">
	                <input type="submit" class="btn btn-primary" name="driver" value="Submit">
	              </div>
	            </form>
          	</div>
          <!-- /.box -->
        </section>
        <!-- right col -->
      </div>
      <!-- /.row (main row) -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
 <script type="text/javascript">
 	$(document).ready(function() {
 		$('input').add('select').attr('required', 'required');
 		$('select[name=staff]').click(function() {
 			if( $('select[name=staff]').val() == '') {
 				$('form#staff').addClass('hidden');
 				$('form#driver').addClass('hidden');
 			}
 			if( $('select[name=staff]').val() == 'driver') {
 				$('div.big').find('h3.box-title').text("Driver Info");
 				$('form#staff').addClass('hidden');
 				$('div.big').removeClass('hidden');
 				$('form#driver').removeClass('hidden');
 			}
 			if($('select[name=staff]').val() == 'staff') {
 				$('div.big').find('h3.box-title').text("Staff Info");
 				$('form#driver').addClass('hidden');
 				$('div.big').removeClass('hidden');
 				$('form#staff').removeClass('hidden');
 			}
 		});
 	});

 </script>
<?php
	require_once 'includes/content/footer.php';
?>
