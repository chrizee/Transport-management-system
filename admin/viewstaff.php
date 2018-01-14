<?php 
	require_once 'includes/content/header.php';
	if(!$user->checkPermission(array('*'))) {		//all can view
		Session::flash('home', "You don't have permission to view that page");
		Redirect::to('dashboard.php');
	}
	
	$errors = array();
	$driverObj = new Driver('drivers');
	$driversInfo = $driverObj->get(array('status', '!=', '0'));
	$staffsInfo = $user->getStaffs(array('status', '!=', '0'));
	$parkGet = $parkObj->get();
?>
	
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?php echo $parkObj->get($user->data()->location, 'park')->park; ?>
        <small>Staffs</small>
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
      <div style="display:inline-block;">
      	<a href="createstaff.php"><button class="btn btn-success pull-right">New <i class="fa fa-plus"></i></button></a>
      </div>
      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <section class="col-lg-12 connectedSortable">
					<div class="box box-success">
						<div class="box-header with-border">
              <h3 class="box-title">Drivers </h3>
              <p class="dtest text text-center text-danger"></p>
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
	                  <th>Name</th>
	                  <th>Email</th>
	                  <th>Phone</th>
	                  <th>Date Joined</th>
	                  <th>Status</th>
	                  <th>Current Location</th>
	                  <?php if($user->hasPermission('admin') || $user->hasPermission('manager') || $user->hasPermission('staff')) {?>
	                  <th></th>
	                  <?php } ?>
	                </tr>
	              </thead>
		            <tbody>
		            	<?php 
		            		foreach ($driversInfo as $key => $value) {
		            		
		            	?>
	                <tr>
	                  <td class="name"><?php echo $value->name ?></td>
	                  <td class="email"><?php echo $value->email ?></td>
	                  <td class="phone"><?php echo $value->phone ?></td>
	                  <td><?php echo $value->date_created ?></td>
	                  <td class="status"><?php 
		                  switch ($value->status) {
		                  	case Config::get('status/sacked'):
		                  		echo "Sacked";
		                  		break;
		                  	case Config::get('status/active'):
		                  		echo "Active";
		                  		break;
		                  	case Config::get('status/leave'):
		                  		echo "On leave";
		                  		break;
		                  	case Config::get('status/sick'):
		                  		echo "Sick";
		                  		break;
		                  	case Config::get('status/selected'):
		                  		echo "Selected for travel";
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
	                 	<td class="location"><?php echo $parkObj->get($value->current_location, "park")->park;?></td>
	                 	<?php if($user->hasPermission('admin') || $user->hasPermission('manager') || $user->hasPermission('staff')) {?>
	                 	<td><button type="button" class="btn btn-info driver" name="<?php echo $value->id; ?>" id="<?php echo "driver".$value->id; ?>" data-toggle="modal" data-target="#drivers">Edit</button></td>
	                 	<?php } ?>
	                </tr>
	              <?php } ?>
		            </tbody>
	            </table>
            </div>
					</div>
					
					<div class="example-modal">
		        <div id="drivers" class="modal fade" role="dialog">
		          <div class="modal-dialog">
		            <div class="modal-content">
		              <div class="modal-header">
		                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                  <span aria-hidden="true">&times;</span></button>
		                <h4 class="modal-title">Edit</h4>
		              </div>
		              <form role="form" method="post" id="driveredit" name="driveredit">
		              	<div class="modal-body">
		                	<input type="hidden" name="id" value>
				              <div class="box-body">
				              <?php if($user->hasPermission('admin') || $user->hasPermission('manager')) { ?>
				                <div class="form-group">
				                  <label for="drivername">Name</label>
				                  <div class="input-group">
					                  <input type="text" class="form-control" id="drivername" name="name" value="" required>
					                  <div class="input-group-addon">
						               	<i class="fa fa-database"></i>
						               </div>
						          		</div>
				                </div>

				                <div class="form-group">
				                  <label for="driveremail">Email</label>
				                  <div class="input-group">
					                  <input type="email" class="form-control" id="driveremail" name="email" value="" required="">
					                  <div class="input-group-addon">
						               	<i class="fa fa-database"></i>
						               </div>
						          		</div>
				                </div>

				                <div class="form-group">
				                  <label for="driverphone">Phone</label>
				                  <div class="input-group">
					                  <input type="tel" class="form-control" id="driverphone" name="phone" value="" required="">
					                  <div class="input-group-addon">
						               	<i class="fa fa-database"></i>
						               </div>
						          		</div>
				                </div>

				                <div class="form-group">
					                <label>Status &nbsp;</label>
					                <label>
					                	<input type="radio" class="active" name="status" value="<?php echo Config::get('status/active')?>"> Active
					                </label>
					                <label>
					                	<input type="radio" class="leave" name="status" value="<?php echo Config::get('status/leave')?>"> leave
					                </label>
					                <label>
					                	<input type="radio" class="sick" name="status" value="<?php echo Config::get('status/sick')?>"> Sick
					                </label>
				                </div>
				                <?php } ?>
				                <div class="form-group">
					                <label>Location</label>
					                <select style="text-transform:capitalize;" class="form-control" id="driverlocation" name="dlocation" style="width: 100%;">
					                  <option value="">--select--</option>
					                  <?php
					                  	foreach ($parkGet as $value) { ?>
					                  		<option value="<?php echo $value->id; ?>"><?php echo $value->park; ?></option>
					                  	<?php } ?>
					                </select>
					            	</div>

				                
				              </div>
				            
		              	</div>
			              <div class="modal-footer">
			                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
			                <input type="submit" class="btn btn-primary" name="editdriver" value="Save changes">
			              </div>
			            </form>
		            </div>
		            <!-- /.modal-content -->
		          </div>
		          <!-- /.modal-dialog -->
		        </div>
		        <!-- /.modal -->
		      </div>



					<div class="box box-success">
						<div class="box-header with-border">
              <h3 class="box-title">Staffs </h3>
              <p class="stest text text-center text-danger"></p>
            	<div class="pull-right box-tools">
	                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
	                  <i class="fa fa-minus"></i></button>
	                <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
	                  <i class="fa fa-times"></i></button>
	            </div>
            </div>
            <div class="box-body">
	             <table class="datatable table table-bordered table-hover table-striped">
	                <thead>
		                <tr>
		                  <th>Name</th>
		                  <th>Email</th>
		                  <th>Phone</th>
		                  <th>Branch</th>
		                  <th>Gender</th>
		                  <th>Address</th>
		                  <th>Date Joined</th>
		                  <th>Status</th>
		                  <th>Role</th>
		                  <?php if($user->hasPermission('admin') || $user->hasPermission('manager')) {?>
		                  <th></th>
		                  <?php } ?>
		                </tr>
	                </thead>
		            <tbody>
		            	<?php 
		            		foreach ($staffsInfo as $key => $value) {
		            			if(($value->groups == Config::get('permissions/ceo') && !$user->hasPermission('admin')) || $value->id == $user->data()->id) continue; //staff cant see ceo's info and their own info
		            	?>
		                <tr>
		                  <td class="name"><a href="viewprofile.php?user=<?php echo encode($value->id)?>"><?php echo $value->name ?></a></td>
		                  <td class="email"><?php echo $value->email ?></td>
		                  <td class="phone"><?php echo $value->phone ?></td>
		                  <td class="location"><?php 
		                  	$park = $parkObj->get($value->location, "id,park");
		                  	echo $park->park; 
		                  ?></td>
		                  <td class="gender"><?php echo $value->gender ?></td>
		                  <td class="address"><?php echo $value->address ?></td>
		                  <td><?php echo $value->created ?></td>
		                  <td class="status"><?php 
			                  switch ($value->status) {
		                          case Config::get('status/sacked'):
		                            echo "Sacked";
		                            break;
		                          case Config::get('status/active'):
		                            echo "Active";
		                            break;
		                          case Config::get('status/leave'):
		                            echo "On leave";
		                            break;
		                          case Config::get('status/sick'):
		                            echo "Sick";
		                            break;
		                          
		                          default:
		                            echo "Undefined";
		                            break;
		                        }
		                  	?> 
		                 	</td>
		                  <td class="level"><?php 
		                  		switch ($value->groups) {
			                  	case Config::get('permissions/loading_officer'):
			                  		echo "Loading officer";
			                  		break;
			                  	case Config::get('permissions/ceo'):
			                  		echo "Ceo";
			                  		break;
			                  	case Config::get('permissions/manager'):
			                  		echo "Manager";
			                  		break;
			                  	case Config::get('permissions/waybill'):
			                  		echo "Waybill";
			                  		break;
			                  	
			                  	default:
			                  		echo "Undefined";
			                  		break;
			                  } 
							?>	
							</td>
							<?php if($user->hasPermission('admin')) { // for ceo and branch managers for their branch staff ?>
		                  		<td><button type="button" class="btn btn-info staff" name="<?php echo $value->id; ?>" data-toggle="modal" data-target="#staffs">Edit</button></td>
		                  <?php }else { 
		                  		if(($user->hasPermission('manager') && $user->data()->location == $value->location)) { ?>
		                  			<td><button type="button" class="btn btn-info staff" name="<?php echo $value->id; ?>" data-toggle="modal" data-target="#staffs">Edit</button></td>
		                  <?php } else {
		                  		echo "<td><i class='fa fa-th'></i></td>";
		                  	} } ?>
		                </tr>
		              <?php } ?>
		            </tbody>
	              </table>
            </div>
					</div> 

					<div class="example-modal">
		        <div id="staffs" class="modal fade" role="dialog">
		          <div class="modal-dialog">
		            <div class="modal-content">
		              <div class="modal-header">
		                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                  <span aria-hidden="true">&times;</span></button>
		                <h4 class="modal-title">Edit</h4>
		              </div>
		              <div class="modal-body">
		              	<form role="form" method="post" action="<?php echo $_SERVER['PHP_SELF']?>" name="staffedit">
				              <input type="hidden" name="id">
				              <div class="box-body">
				                <div class="form-group">
				                  <label for="staffname">Name</label>
				                  <div class="input-group">
					                  <input type="text" class="form-control" id="staffname" name="name" value="">
					                  <div class="input-group-addon">
						               	<i class="fa fa-database"></i>
						               </div>
						          		</div>
				                </div>

				                <div class="form-group">
				                  <label for="email">Email</label>
				                  <div class="input-group">
					                  <input type="email" class="form-control" id="staffemail" name="email" value="">
					                  <div class="input-group-addon">
						               	<i class="fa fa-database"></i>
						               </div>
						          		</div>
				                </div>

				                <div class="form-group">
				                  <label for="phone">Phone</label>
				                  <div class="input-group">
					                  <input type="tel" class="form-control" id="staffphone" name="phone" value="">
					                  <div class="input-group-addon">
						               	<i class="fa fa-database"></i>
						               </div>
						          		</div>
				                </div>

				                <div class="form-group">
				                  <label for="address">Address</label>
				                  <div class="input-group">
					                  <input type="address" class="form-control" id="staffaddress" name="address" value="">
					                  <div class="input-group-addon">
						               	<i class="fa fa-database"></i>
						               </div>
						          		</div>
				                </div>

				                <div class="form-group">
					                <label>Status &nbsp;</label>
					                <label>
					                	<input type="radio" class="sactive" name="sstatus" value="<?php echo Config::get('status/active')?>"> Active
					                </label>
					                <label>
					                	<input type="radio" class="sleave" name="sstatus" value="<?php echo Config::get('status/leave')?>"> leave
					                </label>
					                <label>
					                	<input type="radio" class="ssick" name="sstatus" value="<?php echo Config::get('status/sick')?>"> Sick
					                </label>
				                </div>

				                <div class="form-group">
					                <label>Location</label>
					                <select style="text-transform:capitalize;" class="form-control" id="stafflocation" name="location" style="width: 100%;">
					                  <option value="">--select--</option>
					                  <?php
					                  	foreach ($parkGet as $value) { ?>
					                  		<option value="<?php echo $value->id; ?>"><?php echo $value->park; ?></option>
					                  	<?php } ?>
					                </select>
					            </div>

				                <div class="form-group">
					                <label>Role &nbsp;</label>
					                <label>
					                	<input type="radio" class="loading" name="level" value="<?php echo Config::get('permissions/loading_officer')?>"> loading officer
					                </label>
					                <label>
					                	<input type="radio" class="waybill" name="level" value="<?php echo Config::get('permissions/waybill')?>"> Waybill
					                </label>
					                <?php if($user->hasPermission('admin')) {?>
					                <label>
					                	<input type="radio" class="manager" name="level" value="<?php echo Config::get('permissions/manager')?>"> Manager
					                </label>
					                <label>
					                	<input type="radio" class="admin" name="level" value="<?php echo Config::get('permissions/ceo')?>"> CEO
					                </label>
					                <?php } ?>
					                </div>
					              </div>
				              <!-- /.box-body -->

				              <div class="box-footer">
				                <input type="button" class="btn btn-danger" name="staff" value="Generate new password">
				              </div>
				            </form>
		              </div>
		              <div class="modal-footer">
		                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
		                <button type="button" class="btn btn-primary" name="editstaff">Save changes</button>
		              </div>
		            </div>
		            <!-- /.modal-content -->
		          </div>
		          <!-- /.modal-dialog -->
		        </div>
		        <!-- /.modal -->
		      </div>
        </section>
        <!-- /.Left col -->
      </div>
      <!-- /.row (main row) -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php if($user->hasPermission('admin') || $user->hasPermission('manager')) { ?>
<script type="text/javascript">
	$(document).ready(function() {
		$('button.driver').click(function($e) {
			$e.preventDefault();
			var parent = $(this).parent();
			$('form[name=driveredit] input[type=hidden]').val($(this).attr('name'));
			$('input#drivername').val($(parent).siblings('.name').text());
			$('input#driverphone').val($(parent).siblings('.phone').text());
			$('input#driveremail').val($(parent).siblings('.email').text());
			var status = $(parent).siblings('.status').text();			
			switch(status.trim()) {
				case "Active":
					$("input.active").click();
					break;
				case "On leave":
					$("input.leave").click();
					break;
				case "Sick":
					$("input.sick").click();
					break;
			};
			var dlocation = $(parent).siblings('.location').text().trim().toLowerCase();
			$('select[name=dlocation] option').each(function() {
				if($(this).text().trim().toLowerCase() == dlocation) {
					$(this).attr('selected', 'selected');
				}
			});
		});

		$('input[name=editdriver]').click(function(e) {
			e.preventDefault();
			var $statusVal = $('input[name=status]:checked').val();
			$.post('_editstaff.php', {id: driveredit.id.value, location: driveredit.dlocation.value, name: driveredit.name.value, phone: driveredit.phone.value, email: driveredit.email.value, status: $statusVal, identity: 'driver' }, function(result) {
				$('p.dtest').text(result);
				$('button.close').click();
			});
		});

		$('button.staff').click(function($e) {
			$e.preventDefault();
			var parent = $(this).parent();
			$('form[name=staffedit] input[type=hidden]').val($(this).attr('name'));
			$('input#staffname').val($(parent).siblings('.name').text());
			$('input#staffphone').val($(parent).siblings('.phone').text());
			$('input#staffemail').val($(parent).siblings('.email').text());
			$('input#staffaddress').val($(parent).siblings('.address').text());

			var level = $(parent).siblings('.level').text().trim().toLowerCase();

			if(level == 'loading officer') {
				$('input.loading').click();
			} else if(level == 'ceo') {
				$('input.admin').click();
			} else if(level == 'waybill') {
				$('input.waybill').click();
			} else if(level == 'manager') {
				$('input.manager').click();
			}
			var location = $(parent).siblings('.location').text().trim().toLowerCase();
			$('select[name=location] option').each(function() {
				if($(this).text().trim().toLowerCase() == location) {
					$(this).attr('selected', 'selected');
				}
			});

			var status = $(parent).siblings('.status').text();			
			switch(status.trim()) {
				case "Active":
					$("input.sactive").click();
					break;
				case "On leave":
					$("input.sleave").click();
					break;
				case "Sick":
					$("input.ssick").click();
					break;
			};
		});

		$('button[name=editstaff]').click(function(e) {
			e.preventDefault();
			var $statusVal = $('input[name=sstatus]:checked').val();
			var $levelVal = $('input[name=level]:checked').val();
			$.post('_editstaff.php', {
				identity: 'staff',
				id: staffedit.id.value,
				name: staffedit.name.value,
				phone: staffedit.phone.value,
				email: staffedit.email.value,
				status: $statusVal,
				level: $levelVal,
				address: staffedit.address.value,
				location: staffedit.location.value },
				 function(result) {
				$('p.stest').text(result);
				$('button.close').click();
			});
		});
	})
</script> 
<?php } if($user->hasPermission('staff')) {?>
	<script type="text/javascript">
	$(document).ready(function() {
		$('button.driver').click(function($e) {
			$e.preventDefault();
			var parent = $(this).parent();
			$('form[name=driveredit] input[type=hidden]').val($(this).attr('name'));
			var dlocation = $(parent).siblings('.location').text().trim().toLowerCase();
			$('select[name=dlocation] option').each(function() {
				if($(this).text().trim().toLowerCase() == dlocation) {
					$(this).attr('selected', 'selected');
				}
			});
		});

		$('input[name=editdriver]').click(function(e) {
			e.preventDefault();
			$.post('_editstaff.php', {id: driveredit.id.value, location: driveredit.dlocation.value, identity: 'driver', sender: 'staff' }, function(result) {
				$('p.dtest').text(result);
				$('button.close').click();
			});
		});

	})
</script>
<?php 
}
	require_once 'includes/content/footer.php';
?>