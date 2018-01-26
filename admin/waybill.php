<?php
	require_once 'includes/content/header.php';
	if(!$user->checkPermission(array('staff'), false)) {		//only staff cannot see it
		Session::flash('home', "You don't have permission to view that page");
		Redirect::to('dashboard');
	}
	$park = $parkObj->get();
	$errors = array();
	$waybillObj = new Waybill('waybill');
	$incoming = $waybillObj->get(array('destination', '=', $user->data()->location));
	$outgoing = $waybillObj->get(array('source', '=', $user->data()->location));
	if(Input::exists()) {
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'destination' => array(
				'required' => true,
				),
			'sender_name' => array(
				'required' => true,
				'max' => 100,
				),
			'sender_address' => array(
				'required' => true,
				'max' => 100,
				),
			 'sender_phone' => array(
				'required' => true,
				'function' => 'checkPhone',
				),
			 'receiver_name' => array(
				'required' => true,
				'max' => 100,
				),
			 'receiver_address' => array(
				'required' => true,
				'max' => 100,
				),
			 'receiver_phone' => array(
				'required' => true,
				'function' => 'checkPhone',
				),
			 'item' => array(
				'required' => true,
				'max' => 150,
				),
			 'weight' => array(
				'required' => true,
				),
			 'price' => array(
				'required' => true,
				'numeric' => 2,
				),
			));

		if($validation->passed()) {
			try {
				$pass = Hash::random_password();		//send this value to receiver
				$iv = Hash::openssl_salt();
				$waybillObj->create(array(
					'source' => $user->data()->location,
					'destination' => Input::get('destination'),
					'sender_name' => Input::get('sender_name'),
					'sender_phone' => Input::get('sender_phone'),
					'sender_address' => Input::get('sender_address'),
					'receiver_name' => Input::get('receiver_name'),
					'receiver_phone' => Input::get('receiver_phone'),
					'receiver_address' => Input::get('receiver_address'),
					'item' => Input::get('item'),
					'weight' => Input::get('weight'),
					'price' => Input::get('price'),
					'user_id' => $user->data()->id,
					'salt' => $iv,
					'collection_key' => Hash::encrypt($pass, $iv),
					'spy_key' => $pass,
					));
					Session::flash('home', "Waybill added successfully");
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
        <small>Waybills send code to receiver to use in claiming the item</small>
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
        <li class="active">Add</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
    <?php
    	if($user->hasPermission('waybill')) {
    ?>
      <div style="display:inline-block;">
      	<button class="btn btn-success pull-right new">New <i class="fa fa-plus"></i></button>
      </div>
    <?php } ?>
      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
				<?php
		    	if($user->hasPermission('waybill')) {
		    ?>
        <section class="col-lg-5 connectedSortable hidden">
					<div class="box box-success">
						<div class="box-header with-border">
			              <h3 class="box-title">Waybill Info</h3>
			            	<div class="pull-right box-tools">
				                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
				                  <i class="fa fa-minus"></i></button>
				                <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
				                  <i class="fa fa-times"></i></button>
				            </div>
			            </div>
			            <div class="box-body">
			            	<form role="form" method="post" name="waybill">
				               	<div class="form-group">
				                  <label for="sender_name">Sender's Name</label>
				                  <div class="input-group">
					                  <input type="text" class="form-control" id="sender_name" name="sender_name" value="<?php echo escape(Input::get('sender_name'))?>" required autofocus="on">
					                  <div class="input-group-addon">
						               	<i class="fa fa-user"></i>
						               </div>
						          </div>
				                </div>

				                <div class="form-group">
				                  <label for="sender_address">Sender's Address</label>
				                  <div class="input-group">
					                  <input type="text" class="form-control" id="sender_address" name="sender_address" value="<?php echo escape(Input::get('sender_address'))?>" required>
					                  <div class="input-group-addon">
						               	<i class="fa fa-building"></i>
						               </div>
						          </div>
				                </div>

				                <div class="form-group">
				                  <label for="sender_phone">Sender's Phone</label>
				                  <div class="input-group">
					                  <input type="text" class="form-control" id="sender_phone" name="sender_phone" value="<?php echo escape(Input::get('sender_phone'))?>" required>
					                  <div class="input-group-addon">
						               	<i class="fa fa-phone"></i>
						               </div>
						          </div>
				                </div>

				                <div class="form-group">
				                  <label for="receiver_name">Receiver's Name</label>
				                  <div class="input-group">
					                  <input type="text" class="form-control" id="receiver_name" name="receiver_name" value="<?php echo escape(Input::get('receiver_name'))?>" required>
					                  <div class="input-group-addon">
						               	<i class="fa fa-user"></i>
						               </div>
						          </div>
				                </div>

				                <div class="form-group">
				                  <label for="receiver_address">Receiver's Address</label>
				                  <div class="input-group">
					                  <input type="text" class="form-control" id="receiver_address" name="receiver_address" value="<?php echo escape(Input::get('receiver_address'))?>" required>
					                  <div class="input-group-addon">
						               	<i class="fa fa-building-o"></i>
						               </div>
						          </div>
				                </div>

				                <div class="form-group">
				                  <label for="receiver_phone">Receiver's Phone</label>
				                  <div class="input-group">
					                  <input type="text" class="form-control" id="receiver_phone" name="receiver_phone" value="<?php echo escape(Input::get('receiver_phone'))?>" required>
					                  <div class="input-group-addon">
						               	<i class="fa fa-phone"></i>
						               </div>
						          </div>
				                </div>

				                <div class="form-group">
				                  <label for="item">Item</label>
				                  <div class="input-group">
					                  <input type="text" class="form-control" id="item" name="item" value="<?php echo escape(Input::get('item'))?>" required>
					                  <div class="input-group-addon">
						               	<i class="fa fa-diamond"></i>
						               </div>
						          </div>
				                </div>

				                <div class="form-group">
				                  <label for="weight">Weight (kg)</label>
				                  <div class="input-group">
					                  <input type="text" class="form-control" id="weight" name="weight" value="<?php echo escape(Input::get('weight'))?>" required data-inputmask="'alias': 'decimal'" data-mask>
					                  <div class="input-group-addon">
						               	<i class="fa fa-anchor"></i>
						               </div>
						          </div>
				                </div>

				                <div class="form-group">
					                <label for="destination">Destination</label>
					                <select style="text-transform:capitalize;" id="destination" class="form-control select" name="destination" style="width: 100%;" required>
					                  <option value="">--select--</option>
					                  <?php
					                  	foreach ($park as $value) {
					                  		if($value->id == $user->data()->location) continue; ?>
					                  		<option value="<?php echo $value->id; ?>"><?php echo $value->park; ?></option>
					                  	<?php } ?>
					                </select>
					            </div>

				                <div class="form-group">
				                  <label for="price">Price (N)</label>
				                  <div class="input-group">
					                  <input type="text" class="form-control" id="price" name="price" value="<?php echo escape(Input::get('price'))?>" required data-inputmask="'alias': 'decimal'" data-mask>
					                  <div class="input-group-addon">
						               	<i class="fa fa-exchange"></i>
						               </div>
						          </div>
				                </div>
			            </div>
			            <div class="box-footer">
			                <input type="submit" class="btn btn-primary" name="addwaybill" value="Add Waybill">
			            </div>
			            </form>
					</div>
        </section>
			<?php } ?>
        <!-- /.Left col -->
        <!-- right col (We are only adding the ID to make the widgets sortable)-->
        <section class="col-lg-10 connectedSortable">
        	<div class="box box-success">
						<div class="box-header with-border">
		          <h3 class="box-title">Waybill Status</h3>
		        	<div class="pull-right box-tools">
		              <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
		                <i class="fa fa-minus"></i></button>
		              <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
		                <i class="fa fa-times"></i></button>
		          </div>
		        </div>
		        <div class="box-body">
		          <div class="nav-tabs-custom">
		            <ul class="nav nav-tabs pull-right">
		              <li class="active"><a href="#incoming-travelling" data-toggle="tab">Travelling</a></li>
		              <li><a href="#incoming-arrived" data-toggle="tab">Arrived</a></li>
		              <li><a href="#incoming-in-park" data-toggle="tab">Availlable for pick up</a></li>
		              <li class="dropdown">
		                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
		                  others <span class="caret"></span>
		                </a>
		                <ul class="dropdown-menu">
		                  <li role="presentation"><a role="menuitem" data-toggle="tab" tabindex="-1" href="#incoming-placed">Order Placed</a></li>
		                  <li role="presentation"><a role="menuitem" data-toggle="tab" tabindex="-1" href="#incoming-picked">Picked Up</a></li>
		                </ul>
		              </li>
		              <li class="pull-left header"><i class="fa fa-angle-double-down"></i> Incoming Items</li>
		            </ul>
		            <div class="tab-content">
		            	<div class="tab-pane active" id="incoming-travelling">
		            		<table class="table table-hover table-condensed datatable">
				                <thead>
				                	<tr>
				                		<th>S/n</th>
				                		<th>Item</th>
				                		<th>Receiver</th>
				                		<th>Sender</th>
				                		<th>Receiver's Phone</th>
				                		<th>View</th>
				                	</tr>
				                </thead>
				                <tbody>
		            	<?php
		            		$travelling = 0;
		            		foreach ($incoming as $key => $value) {
		            			if($value->status == Config::get('waybill/travelling')) {
		            				$travelling++;
		            	?>
				                	<tr>
				                		<td><?php echo ($travelling);?></td>
				                		<td><?php echo $value->item; ?></td>
				                		<td><?php echo $value->receiver_name?></td>
				                		<td><?php echo $value->sender_name; ?></td>
				                		<td><?php echo $value->receiver_phone;?></td>
				                		<td><button class="btn btn-info details" data-toggle="modal" data-target="#details" name="<?php echo $value->id ?>">Details</button></td>
				                	</tr>
		              <?php
		              	} } if($travelling == 0) {?>
		              		<!--<tr><td>No incoming travelling</td></tr>-->
		              		<?php } ?>
		              			</tbody>
			                </table>
		              	</div>
		              	<div class="tab-pane" id="incoming-arrived">
		              		<table class="table table-hover table-condensed datatable">
				                <thead>
				                	<tr>
				                		<th>S/n</th>
				                		<th>Item</th>
				                		<th>Receiver</th>
				                		<th>Sender</th>
				                		<th>Receiver's Phone</th>
				                		<?php
									    	if($user->hasPermission('waybill')) {
									    ?>
				                		<th>Mark as received</th>
				                		<?php }else {?>
				                		<th>View</th>
				                			<?php } ?>
				                	</tr>
				                </thead>
				                <tbody>
		              	<?php
		              	$arrived = 0;
		              	foreach ($incoming as $key => $value) {
		              		if($value->status == Config::get('waybill/arrived')) {
		              			$arrived++;
		              ?>
				                	<tr>
				                		<td><?php echo ($arrived);?></td>
				                		<td><?php echo $value->item; ?></td>
				                		<td><?php echo $value->receiver_name?></td>
				                		<td><?php echo $value->sender_name; ?></td>
				                		<td><?php echo $value->receiver_phone;?></td>
				                		<?php
									    	if($user->hasPermission('waybill')) {
									    ?>
				                		<td><input type="checkbox" name="arrived[]" value="<?php echo $value->id ;?>" /></td>
				                		<?php } else {?>
				                			<td><button class="btn btn-info details" data-toggle="modal" data-target="#details" name="<?php echo $value->id ?>">Details</button></td>
				                			<?php }?>
				                	</tr>
			            <?php
		              	} } if($arrived == 0) { ?>
		              	<!--<tr><td>No incoming has arrived</td></tr>-->
		              	<?php } ?>
		              		</tbody>
			                </table>
			                <?php if($arrived != 0 && $user->hasPermission('waybill')) {?>
		              		<div class="clearfix" style="margin-top:2em;">
		              			<button class="btn btn-warning pull-right sendMarked">Confirm Receipt</button>
		              			<div style="background:darkkhaki;color:#fff;float:right;margin-right: 4em;
														margin-top: 9px;"><span>Mark all</span><input style="margin-left:3em;" type="checkbox" name="markall" />
												</div>

		              		</div>
		              		<?php } ?>
		              	</div>
		              	<div class="tab-pane" id="incoming-in-park">
		              		<table class="table table-hover table-condensed datatable">
				                <thead>
				                	<tr>
				                		<th>S/n</th>
				                		<th>Item</th>
				                		<th>Receiver</th>
				                		<th>Sender</th>
				                		<th>Receiver's Phone</th>
				                		<?php if($user->hasPermission('waybill')) { ?>
				                		<th>Verify</th>
				                		<?php }else{?>
				                			<th>View</tr>
				                			<?php }?>
				                	</tr>
				                </thead>
				                <tbody>
		              	<?php
		              	$arrived = 0;
		              	foreach ($incoming as $key => $value) {
		              		if($value->status == Config::get('waybill/in_park')) {
		              			$arrived++;
		              ?>
				                	<tr>
				                		<td><?php echo ($arrived);?></td>
				                		<td><?php echo $value->item; ?></td>
				                		<td><?php echo $value->receiver_name?></td>
				                		<td><?php echo $value->sender_name; ?></td>
				                		<td><?php echo $value->receiver_phone;?></td>
				                		<?php if($user->hasPermission('waybill')) { ?>
				                			<td><button class="btn btn-success verify details" data-toggle="modal" data-target="#details" name="<?php echo $value->id ?>">Verify</button></td>
				                		<?php }else { ?>
				                			<td><button class="btn btn-info details" data-toggle="modal" data-target="#details" name="<?php echo $value->id ?>">Details</button></td>
				                		<?php } ?>
				                	</tr>
			            <?php
		              	} } if($arrived == 0) { ?>
		              	<!--<tr><td>No incoming has arrived</td></tr>-->
		              	<?php } else {?>
		              		<?php } ?>
		              			</tbody>
			                </table>
		              	</div>
		              	<div class="tab-pane" id="incoming-placed">
		              		<table class="table table-hover table-condensed datatable">
				                <thead>
				                	<tr>
				                		<th>S/n</th>
				                		<th>Item</th>
				                		<th>Receiver</th>
				                		<th>Sender</th>
				                		<th>Receiver's Phone</th>
				                		<th>View</th>
				                	</tr>
				                </thead>
				                <tbody>
		              	<?php
		              	$placed = 0;
		              	foreach ($incoming as $key => $value) {
		              		if($value->status == Config::get('waybill/placed')) {
		              			$placed++;
		              ?>
				                	<tr>
				                		<td><?php echo ($placed);?></td>
				                		<td><?php echo $value->item; ?></td>
				                		<td><?php echo $value->receiver_name?></td>
				                		<td><?php echo $value->sender_name; ?></td>
				                		<td><?php echo $value->receiver_phone;?></td>
				                		<td><button class="btn btn-info details" data-toggle="modal" data-target="#details" name="<?php echo $value->id ?>">Details</button></td>
				                	</tr>
		              <?php
		              	} } if($placed == 0) { ?>
		            		<!--<tr><td>No order is placed to come</td></tr>-->
		            		<?php } ?>
		            				</tbody>
			                </table>
		            		</div>
		            		<div class="tab-pane" id="incoming-picked">
		            			<table class="table table-hover table-condensed datatable">
				                <thead>
				                	<tr>
				                		<th>S/n</th>
				                		<th>Item</th>
				                		<th>Receiver</th>
				                		<th>Sender</th>
				                		<th>Receiver's Phone</th>
				                		<th>View</th>
				                	</tr>
				                </thead>
				                <tbody>
		            	<?php
		            	$picked = 0;
		            	foreach ($incoming as $key => $value) {
		            		if($value->status == Config::get('waybill/picked')) {
		            			$picked++;
		              ?>
				                	<tr>
				                		<td><?php echo ($picked);?></td>
				                		<td><?php echo $value->item; ?></td>
				                		<td><?php echo $value->receiver_name?></td>
				                		<td><?php echo $value->sender_name; ?></td>
				                		<td><?php echo $value->receiver_phone;?></td>
				                		<td><button class="btn btn-info details" data-toggle="modal" data-target="#details" name="<?php echo $value->id ?>">Details</button></td>
				                	</tr>
		              <?php
		              	} } if($picked == 0) {?>
		              	<!--<tr><td>No incoming had been picked up</td></tr>-->
		              	<?php } ?>
		              			</tbody>
			                </table>
		              	</div>
		            </div>
		          </div>
		        </div>
		        <div class="box-body">
		          <div class="nav-tabs-custom">
		            <ul class="nav nav-tabs pull-right">
		              <li class="active"><a href="#outgoing-travelling" data-toggle="tab">Travelling</a></li>
		              <li><a href="#outgoing-arrived" data-toggle="tab">Arrived</a></li>
		              <li class="dropdown">
		                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
		                  others <span class="caret"></span>
		                </a>
		                <ul class="dropdown-menu">
		                  <li role="presentation"><a role="menuitem" data-toggle="tab" tabindex="-1" href="#outgoing-placed">Order Placed</a></li>
		                  <li role="presentation"><a role="menuitem" data-toggle="tab" tabindex="-1" href="#outgoing-picked">Picked Up</a></li>
		                </ul>
		              </li>
		              <li class="pull-left header"><i class="fa fa-angle-double-up"></i> Outgoing Items</li>
		            </ul>
		            <div class="tab-content">
		            	<div class="tab-pane active" id="outgoing-travelling">
		            		<table class="table table-hover table-condensed datatable">
				                <thead>
				                	<tr>
				                		<th>S/n</th>
				                		<th>Item</th>
				                		<th>Receiver</th>
				                		<th>Sender</th>
				                		<th>Receiver's Phone</th>
				                		<th>View</th>
				                	</tr>
				                </thead>
				                <tbody>
		            	<?php
		            		$travelling = 0;
		            		foreach ($outgoing as $key => $value) {
		            			if($value->status == Config::get('waybill/travelling')) {
		            				$travelling++;
		            	?>
				                	<tr>
				                		<td><?php echo ($travelling);?></td>
				                		<td><?php echo $value->item; ?></td>
				                		<td><?php echo $value->receiver_name?></td>
				                		<td><?php echo $value->sender_name; ?></td>
				                		<td><?php echo $value->receiver_phone;?></td>
				                		<td><button class="btn btn-info details" data-toggle="modal" data-target="#details" name="<?php echo $value->id ?>">Details</button></td>
				                	</tr>
		              <?php
		              	} } if($travelling == 0) {?>
		              		<!--<tr><td colspan='6'>No outgoing travelling</td></tr>-->
		              		<?php } ?>
		              			</tbody>
			                </table>
		              	</div>
		              	<div class="tab-pane" id="outgoing-arrived">
		              		<table class="table table-hover table-condensed datatable">
				                <thead>
				                	<tr>
				                		<th>S/n</th>
				                		<th>Item</th>
				                		<th>Receiver</th>
				                		<th>Sender</th>
				                		<th>Receiver's Phone</th>
				                		<th>View</th>
				                	</tr>
				                </thead>
				                <tbody>
		              	<?php
		              	$arrived = 0;
		              	foreach ($outgoing as $key => $value) {
		              		if($value->status == Config::get('waybill/arrived') || $value->status == Config::get('waybill/in_park')) {
		              			$arrived++;
		              ?>
				                	<tr>
				                		<td><?php echo ($arrived);?></td>
				                		<td><?php echo $value->item; ?></td>
				                		<td><?php echo $value->receiver_name?></td>
				                		<td><?php echo $value->sender_name; ?></td>
				                		<td><?php echo $value->receiver_phone;?></td>
				                		<td><button class="btn btn-info details" data-toggle="modal" data-target="#details" name="<?php echo $value->id ?>">Details</button></td>
				                	</tr>
			            <?php
		              	} } if($arrived == 0) { ?>
		              	<!--<tr><td colspan='6'>No outgoing has arrived</td></tr>-->
		              	<?php }?>
		              			</tbody>
			                </table>
		              	</div>
		              	<div class="tab-pane" id="outgoing-placed">
		              		<table class="table table-hover table-condensed datatable">
				                <thead>
				                	<tr>
				                		<th>S/n</th>
				                		<th>Item</th>
				                		<th>Receiver</th>
				                		<th>Sender</th>
				                		<th>Receiver's Phone</th>
				                		<th></th>
				                	</tr>
				                </thead>
				                <tbody>
		              	<?php
		              	$placed = 0;
		              	foreach ($outgoing as $key => $value) {
		              		if($value->status == Config::get('waybill/placed')) {
		              			$placed++;
		              ?>
				                	<tr>
				                		<td><?php echo ($placed);?></td>
				                		<td><?php echo $value->item; ?></td>
				                		<td><?php echo $value->receiver_name?></td>
				                		<td><?php echo $value->sender_name; ?></td>
				                		<td><?php echo $value->receiver_phone;?></td>
				                		<td><button class="btn btn-info details" data-toggle="modal" data-target="#details" name="<?php echo $value->id ?>">Details</button></td>
				                	</tr>
		              <?php
		              	} } if($placed == 0) { ?>
		            		<!--<tr><td colspan='6'>No order is placed to come</td></tr>-->
		            		<?php } ?>
		            				</tbody>
			                </table>
		            		</div>
		            		<div class="tab-pane" id="outgoing-picked">
		            			<table class="table table-hover table-condensed datatable">
				                <thead>
				                	<tr>
				                		<th>S/n</th>
				                		<th>Item</th>
				                		<th>Receiver</th>
				                		<th>Sender</th>
				                		<th>Receiver's Phone</th>
				                		<th></th>
				                	</tr>
				                </thead>
				                <tbody>
		            	<?php
		            	$picked = 0;
		            	foreach ($outgoing as $key => $value) {
		            		if($value->status == Config::get('waybill/picked')) {
		            			$picked++;
		              ?>
				                	<tr>
				                		<td><?php echo ($picked);?></td>
				                		<td><?php echo $value->item; ?></td>
				                		<td><?php echo $value->receiver_name?></td>
				                		<td><?php echo $value->sender_name; ?></td>
				                		<td><?php echo $value->receiver_phone;?></td>
				                		<td><button class="btn btn-info details" data-toggle="modal" data-target="#details" name="<?php echo $value->id ?>">Details</button></td>
				                	</tr>
		              <?php
		              	} } if($picked == 0) {?>
		              	<!--<tr><td colspan='6'>No outgoing had been picked up</td></tr>-->
		              	<?php } ?>
		              			</tbody>
			                </table>
		              	</div>
		            </div>
		          </div>
		        </div>
					</div><p class="test"></p>
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
 		var $pass = true;
 		$picked = <?php echo Config::get('waybill/picked'); ?>;
 		$(document).on('keyup', 'input[name=weight], input[name=price]', function() {
 			if($(this).val() < 0 ) {
 				$(this).css('border', '1px solid red');
 			}else {
 				$(this).css('border', '1px solid green');
 			}
 		}).on('click', 'input[name=addwaybill]', function(e) {
 			$('form[name=waybill] input, form[name=waybill] select').each(function() {
 				if($(this).val() == '') {
 					e.preventDefault();
 					$(this).css('border', '1px solid red');
 				}else{
 					$(this).css('border', '1px solid green');
 				}
 			});

 		}).on('click', 'input[name=markall]', function() {
 			$('input[name^=arrived]').each(function() {
 				if(!this.checked) {
 					$(this).click();
 				}
 			});
 		}).on('click', 'button.sendMarked', function() {
 			$arr = [];
 			$('input[name^=arrived]').each(function() {
 				if(this.checked){
 					$arr.push($(this).val());
 				}
 			});
	 			$.post('-updatestatus', {id: $arr, key: 'waybill'}, function($result) {
	 				if($result == 1) {
	 					$('input[name^=arrived]').each(function() {
			 				if(this.checked){
			 					$(this).replaceWith("Done");
			 				}
			 			});
	 				}
	 			});
 		}).on('click', 'button.details', function() {
 			$('form[name=verify]').addClass('hidden');
 			$id = $(this).attr('name');
 			$('button.confirm').attr('name', $id);
 			$('button.changeKey').attr('name', $id);
 			$.post('-getwaybilldetails', {id: $id}, function($result) {
 				if($result != 'X') {
 					$details = JSON.parse($result);
 					$('table.item tr.picked_up').remove();
 					$('table.item td.item').text($details.item);
 					$('table.item td.weight').text($details.weight);
 					$('table.item td.date_placed').text($details.date_placed);
 					$('table.sender td.sender_name').text($details.sender_name);
 					$('table.sender td.sender_phone').text($details.sender_phone);
 					$('table.sender td.sender_address').text($details.sender_address);
 					$('table.receiver td.receiver_name').text($details.receiver_name);
 					$('table.receiver td.receiver_phone').text($details.receiver_phone);
 					$('table.receiver td.receiver_address').text($details.receiver_address);
 					if($details.status == $picked) {
 						$('table.item').append("<tr class='picked_up'><th>Date Picked</th><td>"+$details.date_picked_up+"</td></tr>");
 					}
 				}
 			});
 		}).on('click', 'button.verify', function() {
 			$('form[name=verify]').removeClass('hidden');
 		}).on('keyup', 'input[name=key]', function() {
 			if(this.value.length < 8) {
 				$(this).css('border', '1px solid red');
 			} else{
 				$(this).css('border', '1px solid green');
 				$('div.confirmbtn').removeClass('hidden');
 			}
 		}).on('click', 'button.confirm', function(e) {
 			e.preventDefault();
 			$key = $('input[name=key]').val();
 			id = $(this).attr('name');
 			$.post('-getwaybilldetails', {key: $key, id: id}, function($result) {
 				if($result == '1') {
 					alert('correct key');
 					$('td button[name='+$id+']').replaceWith("<span class='text text-success'>Verified</span>");
 					$('button.close').click();
 				} else {
 					alert('Wrong key');
 				}
 			});
 		}).on('click', 'button.new', function(e) {
 			e.preventDefault();
 			$('a.sidebar-toggle').click();
 			$('section.col-lg-10').removeClass('col-lg-10').addClass('col-lg-7');
 			$('section.col-lg-5').removeClass('hidden');
 		}).on('click', 'button.changeKey', function(e) {
			e.preventDefault();
			$id = $(this).attr('name');
 			$.post('-updatestatus', {action: 'keychange', id: $id}, function($result) {
 				if($result == '1') {
 					alert('key changed');
 					$('button.close').click();
 				} else {
 					alert('Problem changing key');
 				}
 			});
		})
 	})
</script>
<div class="example-modal">
  <div id="details" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Waybill info</h4>
          <p class="passengererror text-danger"></p>
        </div>
        <div class="modal-body">
					<?php if($user->hasPermission('manager')) {?>
						<form role="form" method="post" name="changeKey">
	            <div class="form-group col-lg-6">
	              <label for="changekey"></label>
	              <div class="input-group">
	              <button style="margin-top:5px;" class="btn btn-sm btn-info changeKey">Send new key to receiver</button>
	              </div>
	            </div>
	          </form>

					<?php }?>
					<?php if($user->hasPermission('waybill')) {?>
        	<form role="form" method="post" name="verify" class="hidden">
            <div class="form-group col-lg-6">
              <label for="key">Enter Key to confirm</label>
              <div class="input-group">
                <input type="text" class="form-control" id="key" name="key" maxlength="8" required autofocus="on">
              </div>
            </div>
            <div class="form-group col-lg-6 hidden confirmbtn">
              <label for="key"></label>
              <div class="input-group">
              <button style="margin-top:5px;" class="btn btn-warning confirm">Confirm</button>
              </div>
            </div>
          </form>
				<?php } ?>
          <table class="table table-condensed item">
          	<caption>Item description</caption>
          	<tr>
          		<th>Item</th>
          		<td class="item"></td>
          	</tr>
          	<tr>
          		<th>Weight (KG)</th>
          		<td class="weight"></td>
          	</tr>
          	<tr>
          		<th>Date placed</th>
          		<td class="date_placed"></td>
          	</tr>
          </table>
          <table class="table table-condensed sender">
          	<caption>Sender's Info</caption>
          	<tr>
          		<th>Name</th>
          		<td class="sender_name"></td>
          	</tr>
          	<tr>
          		<th>Phone</th>
          		<td class="sender_phone "></td>
          	</tr>
          	<tr>
          		<th>Address</th>
          		<td class="sender_address"></td>
          	</tr>
          </table>
          <table class="table table-condensed receiver">
          	<caption>Receiver's Info</caption>
          	<tr>
          		<th>Name</th>
          		<td class="receiver_name"></td>
          	</tr>
          	<tr>
          		<th>Phone</th>
          		<td class="receiver_phone"></td>
          	</tr>
          	<tr>
          		<th>Address</th>
          		<td class="receiver_address"></td>
          	</tr>
          </table>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->
</div>
<?php
	require_once 'includes/content/footer.php';
?>
