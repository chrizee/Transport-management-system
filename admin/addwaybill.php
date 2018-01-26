<?php 
	require_once 'includes/content/header.php';
	if(!$user->checkPermission(array('staff'))) {    //only staff can see it
	    Session::flash('home', "You don't have permission to view that page");
	    Redirect::to('dashboard');
	}
	$errors = array();
	if(!Session::exists(Config::get('session/load')) || !Input::exists()) {
		Session::flash('home', "Can't come here without allowing sessions");
		Redirect::to('load');
	}
	$session = Session::get(Config::get('session/load'));
	$destination = Input::get('destination');
	$source = $user->data()->location;

	$passengerObj = new Passenger('temp_passengers');
	$routeObj = new Route('routes');
	$vehicleObj = new Vehicle('vehicles');	
	$driverObj = new Driver('drivers');
	$waybillObj = new Waybill('waybill');
	
	try {
		$passengers = $passengerObj->get(array('hash', '=', $session, 'user_id', '=', $user->data()->id));
		$route = $routeObj->get(array('source', '=', $source, 'destination', '=', $destination));
		$vehicle = $vehicleObj->get(array('id', '=', Input::get('vehicle')), 'plate_no,no_of_seats,ac')[0];
		$driver = $driverObj->get(array('id', '=', Input::get('driver')))[0];
		$waybill = $waybillObj->get(array('source', '=', $source, 'destination', '=', $destination, 'status', '=', Config::get('waybill/placed')));
	} catch (Exception $e) {
		print_r($e->getMessage());
	}
	$price = (!empty($route)) ? $route[0]->price: Input::get('priceF');
	$duration = (!empty($route)) ? $route[0]->duration: "Not set";
?>
	
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?php echo $parkObj->get($user->data()->location, 'park')->park; ?>
        <small>Add waybill</small>
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
        <li class="active">Trip</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      
      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <section class="col-lg-5 connectedSortable">
			<div class="box box-success initialwaybilldiv">
				<div class="box-header with-border">
	              <h3 class="box-title">Waybill</h3>
	            	<div class="pull-right box-tools">
		                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
		                  <i class="fa fa-minus"></i></button>
		                <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
		                  <i class="fa fa-times"></i></button>
		            </div>
	            </div>
	            <div class="box-body">
	            	<form method="post" action="confirmtrip" role="form" id="tripgo">
            			<input type="hidden" name="total_weight" />
            			<input type="hidden" name="destination" value="<?php echo $destination; ?>" />
            			<input type="hidden" name="priceF" value="<?php echo Input::get('priceF'); ?>" />
            			<input type="hidden" name="vehicle" value="<?php echo Input::get('vehicle');?>" />
            			<input type="hidden" name="driver" value="<?php echo Input::get('driver')?>" />
	            		<?php if(!empty($waybill)) { ?>
		            	<table class="table table-hover table-condensed initial">
			            	<thead>
			            		<tr>
			            			<th>#</th>
			            			<th>Item</th>
			            			<th>Weight (KG)</th>
			            			<th></th>
			            		</tr>
			            	</thead>
			            	<tbody>
			            		
			            		<?php 
			            			foreach ($waybill as $key => $value) {?>
			            				<tr>
			            					<td class="sn"><?php echo $key+1;?></td>
			            					<td class="item"><?php echo $value->item ;?></td>
			            					<td class="weight"><?php echo $value->weight ;?></td>
			            					<td><input type="checkbox" name="waybill[]" class="<?php echo $value->weight ;?>" value="<?php echo $value->id ;?>" /></td>
			            				</tr>
			            		<?php }
			            		?>
			            		
			            	</tbody>
			            	<tfoot>
			            		<tr>
			            			<th colspan="2">Total Weight:</th>
			            			<th colspan="2"></th>
			            		</tr>
			            	</tfoot>
		            	</table>
	            		<?php } else { ?>
	            		<p>No waybill to add</p>
	            		<?php } ?>
	            	</form>
	            </div>
	            <div class="box-footer">
	            	<p></p>
	            	<button class="btn btn-primary pull-right hidden" id="addselect">Add Selected Items</button>
	            </div>
	            
			</div>

			<div class="box box-success finalwaybilldiv hidden">
				<div class="box-header with-border">
	              <h3 class="box-title">Waybill Added</h3>
	            	<div class="pull-right box-tools">
		                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
		                  <i class="fa fa-minus"></i></button>
		                <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
		                  <i class="fa fa-times"></i></button>
		            </div>
	            </div>
	            <div class="box-body">
	            	<table class="table table-hover table-condensed final">
	            	<thead>
	            		<tr>
	            			<th>#</th>
	            			<th>Item</th>
	            			<th>Weight (KG)</th>
	            		</tr>
	            	</thead>
	            	<tbody>
	    				
	            		
	            	</tbody>
	            	<tfoot>
	            		<tr>
	            			<th colspan="2">Total Weight:</th>
	            			<th colspan="2"></th>
	            		</tr>
	            	</tfoot>
	            	</table>
	            </div>
	            <div class="box-footer">
	            </div>
	            
			</div>
			 <p class="test"></p>
        </section>
        <!-- /.Left col -->
        <!-- right col (We are only adding the ID to make the widgets sortable)-->
        <section class="col-lg-7 connectedSortable">
        	<div class="box box-success">
				<div class="box-header with-border">
	              	<h3 class="box-title">Trip details to <?php echo $parkObj->get($destination, "park")->park;?></h3>
	            	<div class="pull-right box-tools">
		                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
		                  <i class="fa fa-minus"></i></button>
		                <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
		                  <i class="fa fa-times"></i></button>
		            </div>
	            </div>
	            <div class="box-body">
	            	<div class="details" style="margin-bottom:1em;display:flex;">
	            		<div class="details col-lg-4">
	            			<aside>
	            				<strong>Source:</strong> <span><?php echo $parkObj->get($source, "park")->park;?></span><br />
	            				<strong>Destination:</strong> <span><?php echo $parkObj->get($destination, "park")->park;?></span><br />
	            				<strong>Price:</strong> <span><?php echo $price; ?></span><br />
	            				<strong>Duration:</strong> <span><?php echo $duration; ?></span><br />
	            			</aside>
	            		</div>
	            		<div class="col-lg-5">
	            			<aside>
	            				<strong>Driver:</strong> <span><?php echo $driver->name; ?></span><br />
	            				<strong>Vehicle:</strong> <span><?php echo $vehicle->plate_no; ?></span><br />
	            				<strong>Seats in vehicle:</strong> <span><?php echo $vehicle->no_of_seats ; ?></span><br />
	            				<strong>No of Passengers:</strong> <span><?php echo Count($passengers); ?></span><br />
	            				<strong>AC:</strong> <span><?php echo ($vehicle->ac == 1) ? "Yes" : "No"; ?></span><br />
	            			</aside>
	            		</div>
	            	</div>
	            	
	            	<table class="table table-bordered table-hover table-striped datatable">
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
	            <div class="box-footer">
	            	<button class="pull-right btn btn-success confirm">Confirm</button>
	            </div>
			</div>
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
		$('table.initial tfoot').hide();
		var $totalLoad = <?php echo Config::get('waybill/total_load_weight'); ?>;
		var $loaded = 0;
		function checkLoad() {
			var $load = 0;
			$("input[name^='waybill']").each(function() {
				if(this.checked) {
					$load += parseFloat($(this).attr('class'));	
				}
			});
			$loaded = $load.toFixed(2);
			if($load != 0 && $load <= $totalLoad) {
				return true;
			} else {
				return false;
			}
		}
		function renumber() {
	      if($('table.final tbody tr').length > 0) {
	        var sn = 1;
	        $('table.final tbody tr').each(function() {
	          $(this).find('td:first-child').text(sn);
	          sn++;
	        });
	      }
	    }
		$("input[name^='waybill']").click(function() {
			$('table.initial tfoot').show();
			$('div.box-footer p').hide();

			$check = checkLoad();
			$('input[name=total_weight]').val($loaded);
			$('tfoot tr th:last-child').text($loaded+" KG");
			if($check) {
				$('button#addselect').removeClass('hidden');
			} else {
				$('button#addselect').addClass('hidden');
				$('div.box-footer p').show().text('Items added must not Exceed '+$totalLoad+' KG');
			}
		});

		$('button#addselect').click(function(e) {
			e.preventDefault();
			$("input[name^='waybill']").each(function() {
				if(this.checked){
					$('table.final tbody').append(
						"<tr>"+
							"<td>#</td>"+
							"<td>"+$(this).parent().siblings('.item').text().trim()+"</td>"+
							"<td>"+$(this).parent().siblings('.weight').text().trim()+"</td>"+
						"</tr>"
						);
				}
			});
			renumber();
			$('div.initialwaybilldiv').slideUp('slow');
			$('div.finalwaybilldiv').removeClass('hidden').slideDown('slow');
		});
		$('button.confirm').click(function(e) {
			e.preventDefault();
			var $confirm = confirm("Do you wish to continue. This cannot be reversed");
			if($confirm) {
				$('form#tripgo').submit();
			}
		});
	})
</script> 
<?php 
	require_once 'includes/content/footer.php';
?>