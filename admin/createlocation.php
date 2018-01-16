<?php
	require_once 'includes/content/header.php';
	if(!$user->checkPermission(array('manager', 'admin'))) {    //only ceo and manager can see it
	    Session::flash('home', "You don't have permission to view that page");
	    Redirect::to('dashboard.php');
	}
	$success = false;
	$errors = array();
	if(Input::exists()) {
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'park' => array(
				'required' => true,
				'max' => 20,
				'unique' => 'location_of_park',
				),
			));

		if($validation->passed()) {
			try {
				$parkObj->create(array(
					'park' => ucfirst(Input::get('park'))
					));
					$success = true;
					//$notice = new Notification();
					$notification->create(array(
						'message' => "A new park has been created at ".Input::get('park'),
						'initiated' => $user->data()->id,
						'location_initiated' => $user->data()->location,
						'affected' => Config::get('permissions/all'),
						'location_affected' => Config::get('permissions/all'),
						'category' => Config::get('notification/location_add'),
						));
					Session::flash('home', "Location created successfully");		//no reload to fill in location routes
			} catch (Exception $e) {
				print_r($e->getMessage());
			}
		} else {
			foreach ($validation->errors() as $error) {
				$errors[] = $error;
			}
		}
	}
	$park = $parkObj->get();
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?php echo $parkObj->get($user->data()->location, 'park')->park; ?>
        <small>New Park Location</small>
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
        <li class="active">Create</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
			<div style="display:inline-block;">
      	<a href="viewlocation.php"><button class="btn btn-sm btn-success pull-right">View locations <i class="fa fa-link"></i></button></a>
      </div>
      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <section class="col-lg-4 connectedSortable">
			<div class="box box-success">
				<div class="box-header with-border">
	              <h3 class="box-title">Enter park Name</h3>
	            	<div class="pull-right box-tools">
		                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
		                  <i class="fa fa-minus"></i></button>
		                <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
		                  <i class="fa fa-times"></i></button>
		            </div>
	            </div>
	            <div class="box-body">
	            	<form role="form" method="post" name="park">
		                <div class="form-group">
		                  <label for="park">Name of park</label>
		                  <div class="input-group">
			                  <input type="text" class="form-control" id="park" name="park" value="<?php echo escape(Input::get('park'))?>" required autofocus>
			                  <div class="input-group-addon">
				               	<i class="fa fa-institution"></i>
				               </div>
				          </div>
		                </div>
		                <?php
		                	if($park) {
		                ?>
		                <div class="form-group">
			                <label>Sources and Destination</label>
			                <select class="form-control select2" multiple="multiple" name="destination[]" data-placeholder="Select a Park" style="width: 100%;">
			                <?php
			                  	foreach ($park as $value) { ?>
			                  		<option value="<?php echo $value->id; ?>"><?php echo $value->park; ?></option>
		                  	<?php } ?>
			                </select><br/><br/>
			                <label>Select All &nbsp;&nbsp;&nbsp;</label>
			                <label>
			                	<input type="radio" name="destination" value="all">
			                </label>
			            	</div>
		            		<?php } ?>
	            </div>
	            <div class="box-footer">
	                <input type="submit" class="btn btn-sm btn-primary" name="park_create" value="Create Park">
	            </div>
	            </form>
			</div>
        </section>
        <!-- /.Left col -->
        <!-- right col (We are only adding the ID to make the widgets sortable)-->
        <section class="col-lg-8 connectedSortable">
        	<?php
        		//print_r($_POST);
        		if(Input::get('park_create') && !empty(Input::get('destination')) && $success) {
        			$new = Input::get('park');
        			if(Input::get('destination') == 'all') {
        				$park2 = $parkObj->get(NULL, "id,park");
        			} else {
        				foreach (Input::get('destination') as $key => $value) {
        					$park2[] = $parkObj->get($value, "id,park");
        				}
        			}
        			$sourceId = $parkObj->lastId();
        			foreach ($park2 as $key => $value) {
        				if($sourceId == $value->id) {
        					unset($park2[$key]);		//remove newly created location in route option
        				}
        			}
        	?>
        	<div class="box box-success">
				<div class="box-header with-border">
              <h3 class="box-title">Enter price and duration for each route</h3>
            	<div class="pull-right box-tools">
	                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
	                  <i class="fa fa-minus"></i></button>
	                <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
	                  <i class="fa fa-times"></i></button>
	            </div>
            </div>
            <div class="box-body">
            	<form role="form" method="post" name="routes">
		            <table class="table table-bordered table-hover">
		            	<thead>
		            		<tr>
			                  <th>Source</th>
			                  <th>Destination</th>
			                  <th>Price</th>
			                  <th>Duration</th>
			                  <th>Send</th>
			                </tr>
		            	</thead>
		                <tbody>
		                	<?php
		                		foreach ($park2 as $key => $value) { ?>
		                	<tr>
			                  <td>
				                  <input type="hidden" name="<?php echo "source".$key?>" value="<?php echo $sourceId; ?>" />
				                  <input type="text" class="form-control" placeholder="<?php echo $new; ?>" disabled>
				              </td>
			                  <td>
				                  <input type="hidden" name="<?php echo "destination".$key?>" value="<?php echo $value->id; ?>" />
				                  <input type="text" class="form-control" placeholder="<?php echo $value->park; ?>" disabled>
				                </td>
			                  <td>
			                  	<input type="text" class="form-control" name="<?php echo "price".$key?>" data-inputmask="'alias': 'decimal'" data-mask>
			                  </td>
			                  <td>
			                  	<input type="text" class="form-control" name="<?php echo "duration".$key?>"  data-inputmask="'alias': 'hh:mm'" data-mask>
			                  </td>
			                  <td>
			                  	<button class="btn btn-info forward" name="<?php echo $key; ?>" >Send</button>
			                  </td>
			                </tr>
			                <?php } ?>
		                </tbody>
		            </table>
		            <table class="table table-bordered table-hover">
		            	<thead>
		            		<tr>
			                  <th>Source</th>
			                  <th>Destination</th>
			                  <th>Price</th>
			                  <th>Duration</th>
			                  <th>Send</th>
			                </tr>
		            	</thead>
		                <tbody>
		                	<?php
		                		foreach ($park2 as $key => $value) { ?>
		                	<tr>
			                  <td>
				                  <input type="hidden" name="<?php echo "rsource".$key?>" value="<?php echo $value->id; ?>" />
				                  <input type="text" class="form-control" placeholder="<?php echo $value->park; ?>" disabled>
				                </td>
				                <td>
				                  <input type="hidden" name="<?php echo "rdestination".$key?>" value="<?php echo $sourceId; ?>" />
				                  <input type="text" class="form-control" placeholder="<?php echo $new; ?>" disabled>
				                </td>
			                  <td>
			                  	<input type="text" class="form-control" name="<?php echo "rprice".$key?>" data-inputmask="'alias': 'decimal'" data-mask>
			                  </td>
			                  <td>
			                  	<input type="text" class="form-control" name="<?php echo "rduration".$key?>"  data-inputmask="'alias': 'hh:mm'" data-mask>
			                  </td>
			                  <td>
			                  	<button class="btn btn-info reverse" name="<?php echo $key; ?>" >Send</button>
			                  </td>
			                </tr>
			                <?php } ?>
		                </tbody>
		            </table>
	            </form>
            </div>
            <div class="box-footer">
            	<p class="test"></p>
            </div>
					</div>
						<?php
							}
						?>
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
		$('form[name=park]')[0].reset();
		function Success($result,name,btn) {
			if($result == 'X') {
				var $message = "Atleast price is required";
				$('p.created').text($message);
			} else if($result == 'A') {
				var $message = "Route successfully created with duration";
				$('p.created').text($message);
				$('button.'+btn+'[name='+name+']').removeClass('btn-info').addClass('btn-success').attr('disabled', 'disabled').html("<i class='glyphicon glyphicon-ok'></i>");
			} else if($result == 'B') {
				var $message = "Route successfully created without duration";
				$('p.created').text($message);
				$('button.'+btn+'[name='+name+']').removeClass('btn-info').addClass('btn-success').attr('disabled', 'disabled').html("<i class='glyphicon glyphicon-ok'></i>");
			}
		}
		$('button.forward').click(function($e) {
			$e.preventDefault();
			var name = this.name;
			var $source = $("input[name=source"+name+"]").val();
			var $destination = $("input[name=destination"+name+"]").val();
			var $price = $("input[name=price"+name+"]").val();
			var $duration = $("input[name=duration"+name+"]").val();
			$.post('_addroute.php', {source: $source, destination: $destination, price: $price , duration: $duration }, function($return) {
				Success($return, name, 'forward')
			});
		});
		$('button.reverse').click(function($e) {
			$e.preventDefault();
			var name = this.name;
			var $source = $("input[name=rsource"+name+"]").val();
			var $destination = $("input[name=rdestination"+name+"]").val();
			var $price = $("input[name=rprice"+name+"]").val();
			var $duration = $("input[name=rduration"+name+"]").val();
			$.post('_addroute.php', {source: $source, destination: $destination, price: $price , duration: $duration }, function($return) {
				Success($return,name, 'reverse');
			});
		});
	})

</script>
<?php
	require_once 'includes/content/footer.php';
?>
