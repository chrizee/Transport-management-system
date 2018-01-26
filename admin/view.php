<?php 
	require_once 'includes/content/header.php';
	if(!$user->checkPermission(array('*'))) {    //all can see it
	    Session::flash('home', "You don't have permission to view that page");
	    Redirect::to('dashboard');
	}
	$vehicle = new Vehicle('vehicles');
	$driver = new Driver('drivers');
	$travelObj = new Travels('travels');
	$routeObj = new Route('routes');
	try {
		$travels = $travelObj->get(array('destination', '=', $user->data()->location, 'status', '=', Config::get('status/travels/travelling')));
		
	} catch (Exception $e) {
		die($e->getMessage());
	}
	
?>
	
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?php echo $parkObj->get($user->data()->location, 'park')->park; ?>
        <small>Incoming trip</small>
      </h1>
      <?php if(Session::exists('home')) {
		        echo "<p class='text text-center'>".Session::flash('home')."</p>";
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
        <section class="col-lg-9 connectedSortable">
					<div class="box box-success">
						<div class="box-header with-border">
		          <h3 class="box-title">Incoming</h3>
		        	<div class="pull-right box-tools">
		              <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
		                <i class="fa fa-minus"></i></button>
		              <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
		                <i class="fa fa-times"></i></button>
		          </div>
		        </div>
		        <div class="box-body">
		         	<?php
		         		if($travels) {
		         			$classes = ['progress-bar-primary', 'progress-bar-success', 'progress-bar-warning', 'progress-bar-danger','progress-bar-aqua'];
		         			foreach ($travels as $key => $value) {
		         				$class = $classes[rand(1, count($classes)-1)];
		         				$travelObj->calcPercent($value->route_id, $routeObj, $value->date, $value->id);
		         			?>
		         			<div><?php if($value->type == 1) {?>
		         				<h5 class="text text-center">Response to request</h5>
		         				<?php } ?>
			         			<h4 id="<?php echo $driver->get(array('id', '=', $value->driver_id), 'name')[0]->name;?>" title="Driver-<?php echo $driver->get(array('id', '=', $value->driver_id), 'name')[0]->name;?>" class="text-center">Vehicle: <?php echo $vehicle->get(array('id', '=', $value->vehicle_id), 'plate_no')[0]->plate_no;?></h4>
			         			<div class="text-success clearfix">
				         			<p class="pull-left col-lg-6 source"><span>From: <?php echo $parkObj->get($value->source, 'park')->park; ?></span>
				         				<small class="pull-right text-warning"><?php if($travelObj->expectedArrival){echo "Arrival time: ".$travelObj->expectedArrival;} else{echo $travelObj->msg;}  ?></small>
				         			</p>
				         			<p class="pull-right col-lg-4 destination">To: <?php echo $parkObj->get($value->destination, 'park')->park; ?></p>
				         		</div>
				         		<div class="targetparent">
				         			<div style="padding:0;" class="col-lg-10 col-md-10 col-sm-10 progress active" title="Driver-<?php echo $driver->get(array('id', '=', $value->driver_id), 'name')[0]->name;?>">
				                <div class="progress-bar <?php echo $class; ?> progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $travelObj->percent."%"; ?>"><span class="percent"><?php echo $travelObj->percent."%"; ?></span>
				                  <span class="sr-only"><?php echo $travelObj->percent."%"; ?> Complete (success)</span>
				                </div>
				              </div>
				              <?php 
				              if($user->hasPermission('staff')) {
				              ?>
				              <div class="col-lg-2 col-md-2 col-sm-2 form" style="margin-top:-25px;">
				              	<p class="text-center" style="font-weight:700;">Recieved</p>
				              	<div class="input-group" style="margin:0 auto;">
				              		<input type="checkbox" name="arrived" class="pull-right" value="<?php echo $value->id ;?>" />
				              	</div>
				              </div>
				            </div>
				            <?php } ?>
				            <p class="clearfix"></p>
				          </div>
		         	<?php } } else { ?>
							 <p> No incoming </p>
	         		<?php } ?>
		        </div>
					</div><p class="test"></p> 
        </section>
        <!-- /.Left col -->

        <section class="col-lg-3 connectedSortable status">
					<div class="box box-success">
						<div class="box-header with-border">
		          <h3 class="box-title">Status</h3>
		        	<div class="pull-right box-tools">
		              <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
		                <i class="fa fa-minus"></i></button>
		              <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
		                <i class="fa fa-times"></i></button>
		          </div>
		        </div>
		        <div class="box-body">
		         <p><strong class="vehicle"></strong> from <strong class="source"></strong> to <strong class="destination"></strong> driven by <strong class="driver"></strong> received. </p>
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
 		$('section.status').hide();
 		var $no_completed_by_clicks = 0;			//to store no of trips clicked as complete for clearing the interval when all arrived before time
 		$(document).on('click', 'input[name=arrived]', function() {
 			$('section.status').fadeOut('100000');
 			$id = $(this).val();
 			$targetparent = $(this).parents('div.targetparent');

 			$.post('-arrive', {tripid: $id}, function($result) {
 				if($result == 1) {
 					$no_completed_by_clicks++;
 					$('strong.vehicle').text($($targetparent).siblings('h4').text().trim().replace(/:/g, " -"));
 					$('strong.source').text($($targetparent).siblings('div.clearfix').find('p.source span').text().trim().replace(/From: /g, ""));
 					$('strong.driver').text($($targetparent).siblings('h4').attr('id').trim());
 					$('strong.destination').text($($targetparent).siblings('div.clearfix').find('p.destination').text().trim().replace(/To: /g, ""));
 					$('section.status').fadeIn('100000');
 					$($targetparent).find('div.progress-bar').css('width', "100%");
 					$($targetparent).find('div.form').remove();
 					$($targetparent).find('div.progress').removeClass('active col-lg-10 col-md-10 col-sm-10').addClass('col-lg-12 col-md-12 col-sm-12');
 					$($targetparent).find('span.percent').text("100%");
 					$($targetparent).find('span.sr-only').text('100% Complete (success)');
 				} else {
 					$('p.test').text($result);
 				}
 			});
 		});

 		$obj = <?php echo $travelObj->json(); ?>;
 		$originalLength = $obj.length;
 		var $complete_100 = 0;			//used to clear timer when all counter get to 100, 
 		if($obj.length > 0) {				//no need setting the interval if no object to loop through
	 		var $offset = 2;					//must be equal to the interval time
	 		var $interval = setInterval(function() {
	    	$($obj).each(function($index) {
	 			$id = this.id;
	 			$right = this.right + $offset;
	 			$left = this.left;
	 			$div = this.div;
	 			$percent = 100 - (($left-$right)/$div * 100);
	 			if($percent>=100) {
					$percent = 100;
					$obj.splice($index,1);		//when any timer is complete 100%, remover it from the object and increment 
					$complete_100 ++;
				}
				if($percent < 0) {
					$percent = 40;
				}
				if($complete_100 == $originalLength || $no_completed_by_clicks == $originalLength) {
					clearInterval($interval);			//clear interval when all the trip in object hits 100% or when all are clciked as arrived
				}
	 			$parent = $('input[value='+$id+']').parents('div.targetparent').find('div.progress-bar').css('width', $percent.toFixed()+'%' );
	 			$parent = $('input[value='+$id+']').parents('div.targetparent').find('span.percent').text($percent.toFixed()+'%');
	 			$parent = $('input[value='+$id+']').parents('div.targetparent').find('span.sr-only').text($percent.toFixed()+"%  Complete (success)");

	 		});
	 		$offset += 2; 
	    }, 2000);
	  }
 	})
 </script>
<?php 
	require_once 'includes/content/footer.php';
?>