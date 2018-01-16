<?php
	require_once 'includes/content/header.php';
	if(!$user->checkPermission(array('*'))) {    //all can see it
	    Session::flash('home', "You don't have permission to view that page");
	    Redirect::to('dashboard.php');
	}
	$errors = array();
	$park = $parkObj->get();
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?php echo $parkObj->get($user->data()->location, 'park')->park; ?>
        <small>Parks</small>
      </h1>
      <?php if(Session::exists('home')) {
		        echo "<p class='text-danger text text-center'>".Session::flash('home')."</p>";
		    }
		    if($errors) {
    			foreach ($errors as  $value) {
    				echo "<p class='text text-center'>$value</p>";
    			}
    		}
      ?>
      <p class="created text text-center text-danger"></p>
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
      	<a href="createlocation.php"><button class="btn btn-sm btn-success pull-right">New <i class="fa fa-plus"></i></button></a>
      </div>
      <?php } ?>
      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <section class="col-lg-4 connectedSortable">
					<div class="box box-success">
						<div class="box-header with-border">
              <h3 class="box-title">Parks</h3>
            	<div class="pull-right box-tools">
	                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
	                  <i class="fa fa-minus"></i></button>
	                <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
	                  <i class="fa fa-times"></i></button>
	            </div>
            </div>
            <div class="box-body">
            <?php
            	if($park) { ?>
	           	<table class="table table-hover table-condensed">
	                <thead>
		                <tr>
		                  <th>Parks</th>
		                  <th>Date Created</th>
		                  <th class="text-success"><i class="fa fa-info"></i></th>
		                </tr>
	                </thead>
		            <tbody>
		            	<?php

		            		foreach ($park as $key => $value) {

		            	?>
		                <tr>
		                  <td><?php echo $value->park ?></td>
		                  <td><?php $date = new DateTime($value->date_created); echo $date->format('d-M-Y');  ?></td>
		                  <td>
		                  	<a href="viewlocation.php?park=<?php echo encode($value->id) ?>">
		                  		<button class="btn btn-info">View <i class="fa  fa-angle-double-right"></i></button>
		                  	</a>
		                  </td>
		              <?php }
		              ?>
		            </tbody>
	            </table>
          	<?php } else {
	              	echo "No park to display yet";
		         } ?>
            </div>
					</div>
        </section>
        <!-- /.Left col -->
        <!-- right col (We are only adding the ID to make the widgets sortable)-->
        <section class="col-lg-8 connectedSortable">
      	 <?php
      	 	if(!empty(Input::get('park'))) {
      	 		$parkid = decode(Input::get('park'));
      	 		$go = false;
      	 		foreach ($park as $key => $value) {
      	 			if($parkid == $value->id) $go = true;
      	 		}
      	 		if(!$go) {
      	 			Redirect::to('404');
      	 		}
      	 		$routeObj = new Route('routes');
      	 		$routes1 = $routeObj->get(array('source', '=', $parkid));
      	 		$routes2 = $routeObj->get(array('destination', '=', $parkid));
      	 		foreach ($park as $key => $value) {
      	 			$parks[$value->id] = $value->park;
      	 		}
      	 		if(array_key_exists($parkid, $parks)) unset($parks[$parkid]);
      	 		?>
        	 	<div class="box box-success">
							<div class="box-header with-border">
	              <h3 class="box-title"><?php echo $parkObj->get($parkid, "park")->park; ?></h3>
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
			                  <th>Source</th>
			                  <th>Destination</th>
			                  <th>Price</th>
			                  <th>Duration</th>
			                  <?php
									    		if($user->hasPermission('admin') || $user->hasPermission('manager')) {
									    	?>
			                  <th></th>
			                  <?php } ?>
			                </tr>
		                </thead>
			            <tbody>
				            <form role="form" method="post" name="routes">
				            	<?php
				            	if($parks) {
				            		foreach ($parks as $key => $value) {
				            			$arr = array(); $arr2 = array();
				            			foreach ($routes1 as $key1 => $value1) {
				            				if($value1->source == $parkid && $value1->destination == $key) {
				            					$arr = $value1;
				            				}
				            			}
				            			foreach ($routes2 as $key2 => $value2) {
				            				if($value2->source == $key && $value2->destination == $parkid) {
				            					$arr2 = $value2;
				            				}
				            			}
				            			if($arr) {
				            			?>
			            					<tr>
						                  <td>
						                  	<input type="hidden" name="<?php echo "fsource".$key?>" value="<?php echo $parkid ?>" />
						                  	<input type="hidden" name="<?php echo "fid".$key?>" value="<?php echo $arr->id ?>" />
				                  			<?php echo $parkObj->get($parkid, "park")->park; ?>
															</td>
						                  <td>
						                  	<input type="hidden" name="<?php echo "fdestination".$key?>" value="<?php echo $key ?>" />
				                  			<?php echo $parkObj->get($key, "park")->park; ?>
						                  </td>
						                  <td>
						                  	<input type="text" class="form-control" value ="<?php echo $arr->price?>" name="<?php echo "fprice".$key?>" data-inputmask="'alias': 'decimal'" data-mask>
															</td>
						                  <td>
						                  	<input type="text" class="form-control" value="<?php echo $arr->duration?>" name="<?php echo "fduration".$key?>"  data-inputmask="'alias': 'hh:mm'" data-mask>
						                  </td>
						                  <?php
												    		if($user->hasPermission('admin') || $user->hasPermission('manager')) {
												    	?>
						                  <td><button class="btn btn-info uforward" name="<?php echo $key; ?>" >Update</button></td>
						                  <?php } ?>
						                </tr>
							            <?php } else {	?>
						            		<tr>
						                  <td>
						                  	<input type="hidden" name="<?php echo "fsource".$key?>" value="<?php echo $parkid ?>" />
				                  			<?php echo $parkObj->get($parkid, "park")->park; ?>
															</td>
						                  <td>
						                  	<input type="hidden" name="<?php echo "fdestination".$key?>" value="<?php echo $key ?>" />
				                  			<?php echo $parkObj->get($key, "park")->park; ?>
						                  </td>
						                  <td>
						                  	<input type="text" class="form-control" name="<?php echo "fprice".$key?>" data-inputmask="'alias': 'decimal'" data-mask>
															</td>
						                  <td>
						                  	<input type="text" class="form-control" name="<?php echo "fduration".$key?>"  data-inputmask="'alias': 'hh:mm'" data-mask>
						                  </td>
						                  <?php
												    		if($user->hasPermission('admin') || $user->hasPermission('manager')) {
												    	?>
						                  <td><button class="btn btn-warning nforward" name="<?php echo $key; ?>" >Add</button></td>
						                  <?php } ?>
						                </tr>


							            <?php }

							            if($arr2) {
				            			?>
				            				<tr>
						                  <td>
						                  	<input type="hidden" name="<?php echo "rsource".$key?>" value="<?php echo $key ?>" />
						                  	<input type="hidden" name="<?php echo "rid".$key?>" value="<?php echo $arr2->id ?>" />
				                  			<?php echo $parkObj->get($key, "park")->park; ?>
															</td>
						                  <td>
						                  	<input type="hidden" name="<?php echo "rdestination".$key?>" value="<?php echo $parkid ?>" />
				                  			<?php echo $parkObj->get($parkid, "park")->park; ?>
						                  </td>
						                  <td>
						                  	<input type="text" class="form-control" value="<?php echo $arr2->price?>" name="<?php echo "rprice".$key?>" data-inputmask="'alias': 'decimal'" data-mask>
															</td>
						                  <td>
						                  	<input type="text" class="form-control" value="<?php echo $arr2->duration?>" name="<?php echo "rduration".$key?>"  data-inputmask="'alias': 'hh:mm'" data-mask>
						                  </td>
						                  <?php
												    		if($user->hasPermission('admin') || $user->hasPermission('manager')) {
												    	?>
						                  <td><button class="btn btn-info ureverse" name="<?php echo $key; ?>" >Update</button></td>
						                  <?php } ?>
						                </tr>

							            <?php } else {	?>
						            		<tr>
						                  <td>
						                  	<input type="hidden" name="<?php echo "rsource".$key?>" value="<?php echo $key ?>" />
				                  			<?php echo $parkObj->get($key, "park")->park; ?>
															</td>
						                  <td>
						                  	<input type="hidden" name="<?php echo "rdestination".$key?>" value="<?php echo $parkid ?>" />
				                  			<?php echo $parkObj->get($parkid, "park")->park; ?>
						                  </td>
						                  <td>
						                  	<input type="text" class="form-control" name="<?php echo "rprice".$key?>" data-inputmask="'alias': 'decimal'" data-mask>
															</td>
						                  <td>
						                  	<input type="text" class="form-control" name="<?php echo "rduration".$key?>"  data-inputmask="'alias': 'hh:mm'" data-mask>
						                  </td>
						                  <?php
												    		if($user->hasPermission('admin') || $user->hasPermission('manager')) {
												    	?>
						                  <td><button class="btn btn-warning nreverse" name="<?php echo $key; ?>" >Add</button></td>
						                  <?php } ?>
						                </tr>
							            <?php }?>
				              <?php
				            		}
				            	} ?>
				            </form>
			            </tbody>
		            </table>
	            </div>
						</div>
        	 	<?php }
        	 ?>
        </section>
        <!-- right col -->
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
		function Success($result,name,btn) {
			if($result == 'X') {
				var $message = "Atleast price is required";
				$('p.created').text($message);
			} else if($result == 'A') {
				var $message = "Route successfully created with duration";
				$('p.created').text($message);
				$('button.'+btn+'[name='+name+']').removeClass('btn-warning').addClass('btn-success').attr('disabled', 'disabled').html("<i class='glyphicon glyphicon-ok'></i>");
			} else if($result == 'B') {
				var $message = "Route successfully created without duration";
				$('p.created').text($message);
				$('button.'+btn+'[name='+name+']').removeClass('btn-warning').addClass('btn-success').attr('disabled', 'disabled').html("<i class='glyphicon glyphicon-ok'></i>");
			} else if($result == 'U') {
				var $message = "Route updated successfully";
				$('p.created').text($message);
				$('button.'+btn+'[name='+name+']').removeClass('btn-info').addClass('btn-success').attr('disabled', 'disabled').html("<i class='glyphicon glyphicon-ok'></i>");
			}
		}
		$('button.nforward').click(function($e) {
			$e.preventDefault();
			var name = this.name;
			var $source = $("input[name=fsource"+name+"]").val();
			var $destination = $("input[name=fdestination"+name+"]").val();
			var $price = $("input[name=fprice"+name+"]").val();
			var $duration = $("input[name=fduration"+name+"]").val();
			$.post('_addroute.php', {source: $source, destination: $destination, price: $price, duration: $duration }, function($return) {
				Success($return, name, 'nforward')
			});
		});
		$('button.nreverse').click(function($e) {
			$e.preventDefault();
			var name = this.name;
			var $source = $("input[name=rsource"+name+"]").val();
			var $destination = $("input[name=rdestination"+name+"]").val();
			var $price = $("input[name=rprice"+name+"]").val();
			var $duration = $("input[name=rduration"+name+"]").val();
			$.post('_addroute.php', {source: $source, destination: $destination, price: $price, duration: $duration }, function($return) {
				Success($return,name, 'nreverse');
			});
		});

		$('button.uforward').click(function($e) {
			$e.preventDefault();
			var name = this.name;
			var $price = $("input[name=fprice"+name+"]").val();
			var $duration = $("input[name=fduration"+name+"]").val();
			var $id = $("input[name=fid"+name+"]").val();
			$.post('_addroute.php', {id: $id, price: $price, duration: $duration }, function($return) {
				Success($return, name, 'uforward');
			});
		});
		$('button.ureverse').click(function($e) {
			$e.preventDefault();
			var name = this.name;
			var $source = $("input[name=rsource"+name+"]").val();
			var $destination = $("input[name=rdestination"+name+"]").val();
			var $price = $("input[name=rprice"+name+"]").val();
			var $duration = $("input[name=rduration"+name+"]").val();
			var $id = $("input[name=rid"+name+"]").val();
			$.post('_addroute.php', {id: $id, price: $price, duration: $duration }, function($return) {
				Success($return, name, 'ureverse');
			});
		});
	})

</script>
<?php
	}
	require_once 'includes/content/footer.php';
?>
