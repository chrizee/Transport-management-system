<?php
	require_once 'includes/content/header.php';
	if(!$user->checkPermission(array('*'))) {
	    Session::flash('home', "You don't have permission to view that page");
	    Redirect::to('dashboard');
	}
	$parks = $parkObj->get(null, 'id,park');
	$errors = array();
	if(Input::exists() && !empty(Input::get('fetch_invoice'))) {
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'invoice_period' => array(
				//'function' => 'checkDateNow',
				),
			));
		$start = '';
		$end = '';
		if(!empty(Input::get('invoice_period')) && Input::get('period') == 'others') {
			$period =explode('- ', Input::get('invoice_period'));
    	$start = cleanDate($period[0]);
    	$end = cleanDate($period[1]);
    	if(strtotime($end) > time()) {
    		$errors[] = "End date in range cannot be greater than now";
    	}
		}

		if($user->checkPermission(['admin'])) {
			if((empty(Input::get('period')) || empty(Input::get('invoice_period'))) || (empty(Input::get('location')) && empty(Input::get('staff')))) {
				$errors[] = "Enter a valid combination of either location or staff and period to proceed";
			}
		}

		if($user->checkPermission(['manager'])) {
			if(empty(Input::get('staff')) || (empty(Input::get('period')) || empty(Input::get('invoice_period')))) {
				$errors[] = "Enter a valid combination of staff and period to proceed";
			}
		}

		if($user->checkPermission(['staff'])) {
			if(empty(Input::get('period')) || empty(Input::get('invoice_period'))) {
				$errors[] = "Enter a valid period to proceed";
			}
		}

		if($validation->passed() && empty($errors)) {
			$invoiceObj = new Invoice();
    	try {
    		$invoice = array_reverse($invoiceObj->getN(Input::get('period'), $user, $start, $end, Input::get('location'), Input::get('staff'),$parkObj));	
    	} catch (Exception $e) {
    		die($e->getMessage());
    	}
		}else {
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
        <small>Accounts</small>
      </h1>
      <?php if(Session::exists('home')) {
		        echo "<p class='created text text-center text-danger'>".Session::flash('home')."</p>";
		    }
		    if($errors) {
    			foreach ($errors as  $value) {
    				echo "<p class='text text-center text-danger'>$value</p>";
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
      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <section class="col-lg-4 connectedSortable left">
					<div class="box box-success">
						<div class="box-header with-border">
              <h3 class="box-title">Sort by</h3>
            	<div class="pull-right box-tools">
                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                  <i class="fa fa-minus"></i></button>
                <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
                  <i class="fa fa-times"></i></button>
	            </div>
            </div>
            <div class="box-body">
	            <form method="post" action="" role="form" id="period">
	            	<div class="form-group">
	                <label>View invoice for </label>
	                <select class="form-control select2" name="period" data-placeholder="Select period" style="width: 100%;" required>
	                  <option value="">--select--</option>
	                  <option value="<?php echo Config::get('periods/today') ?>">Today</option>
	                  <option value="<?php echo Config::get('periods/yesterday'); ?>">Yesterday</option>
	                  <option value="<?php echo Config::get('periods/this_week'); ?>">This week</option>
	                  <option value="<?php echo Config::get('periods/last_week') ?>">Last week</option>
	                  <option value="<?php echo Config::get('periods/this_month'); ?>">This Month</option>
	                  <option value="<?php echo Config::get('periods/last_month'); ?>">Last Month</option>
	                  <option value="others">others...</option>
	                </select>
	            	</div>

	            	<div class="input-group range hidden" >
	            		<label for="date">Select range of period</label>
	                <input type="text" name="invoice_period" id="date" class="form-control" value="<?php echo escape(Input::get('invoice_period'))?>">
		            </div>

	            	<?php if($user->checkPermission(['admin'])) {?>
	            		 	<div class="form-group">
			                <label>View by location</label>
			                <select class="form-control select2" name="location" data-placeholder="Select a Park" style="width: 100%;" required>
			                	<option value="">--select--</option>
			                	<option value="*" selected>all</option>
			                <?php
			                  	foreach ($parks as $value) { ?>
			                  		<option value="<?php echo $value->id; ?>"><?php echo $value->park; ?></option>
		                  	<?php } ?>
			                </select>
			            	</div>
	            	<?php } ?>

	            	<?php if($user->checkPermission(['manager','admin'])) {
	            		$staffs = $user->getStaffs(array('id', '!=', $user->data()->id, 'groups', '=', Config::get('permissions/loading_officer')));
	            		if($staffs) {
	            		?>
	            		 	<div class="form-group">
			                <label>Select to view invoice by a particular staff</label>
			                <select class="form-control select2" name="staff" data-placeholder="Select a staff" style="width: 100%;">
			                	<option value="">--select--</option>
			                <?php
			                  	foreach ($staffs as $value) {
			                  		if($user->hasPermission('manager') && $value->location != $user->data()->location) continue; ?>
			                  		<option value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>
		                  	<?php } ?>
			                </select>
			            	</div>
	            	<?php } }?>
	            	
	            </form>
	          </div>
            <div class="box-footer">
            	<input type="submit" form="period" class="btn btn-sm btn-primary" name="fetch_invoice" value="Fetch Invoice">
            </div>
					</div>
        </section>
        <?php 
        	if(Input::exists() && !empty(Input::get('fetch_invoice')) && isset($invoice)) {
        ?>
        <section class="col-lg-8 connectedSortable">
        	<div class="box box-success">
						<div class="box-header with-border">
              <h3 class="box-title"><?php echo $invoiceObj->message; ?></h3>
            	<div class="pull-right box-tools">
	                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
	                  <i class="fa fa-minus"></i></button>
	                <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
	                  <i class="fa fa-times"></i></button>
	            </div>
            </div>
            <div class="box-body">
            	<?php if(!empty($invoice) && isset($invoice[0]->amount)) { ?>
            	<table class="table table-condensed">
            		<thead>
            			<tr>
            				<th>Date</th>
            				<?php if($user->checkPermission(['admin','manager'])) {?>
            				<th>Raised by</th>
            				<?php } ?>
            				<th>source</th>
            				<th>Destination</th>
            				<th>Amount (N)</th>
            			</tr>
            		</thead>
            		<tbody>
            			<?php $total = 0;
            				foreach ($invoice as $key => $value) {
            					$total += $value->amount;
            					?>
            					<tr>
            					<td><?php echo (new dateTime($value->date))->format('d-M-Y h:i a');?></td>
            				<?php if($user->checkPermission(['admin','manager'])) {?>
            					<td><?php echo $user->getStaffs(array('id', '=', $value->user))[0]->name;?></td>
            				<?php } ?>
            					<td><?php echo $parkObj->get($value->source, 'park')->park;?></td>
            					<td><?php echo $parkObj->get($value->destination, 'park')->park;?></td>
            					<td><?php echo $value->amount;?></td>
            					</tr>
            				<?php }
            			?>
            		</tbody>
            		<tfoot>
            			<tr>
            				<td>Total (N):</td>
            				<td><?php echo $total;?></td>
            			</tr>
            		</tfoot>
            	</table>
            	<?php } else{ ?>
            		<p>No result for your query</p>
            	<?php } ?>
            </div>
					</div>
        </section>
        <?php } ?>
      </div>
      <!-- /.row (main row) -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <!-- daterangepicker -->
<script src="plugins/daterangepicker/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('section.left').on('change', 'select[name=period]', function() {
			$(this).attr('required');
			$('div.range').slideUp('slow');
			$('input[name=invoice_period]').removeAttr('required', 'required');
			if($(this).val() == 'others') {
				$(this).removeAttr('required');
				$('div.range').removeClass('hidden').slideDown('slow');
				$('input[name=invoice_period]').attr('required', 'required');
			}
		});

		$("input[name='invoice_period']").daterangepicker({
        timePicker: true,
        timePickerIncrement: 1,
        locale: {
            format: 'MM/DD/YYYY h:mm A'
        }
    });
	})

</script>
<?php
	require_once 'includes/content/footer.php';
?>
