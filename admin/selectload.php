<?php 
	require_once 'includes/content/header.php';
  if(!$user->checkPermission(array('staff'))) {    //only staff can see it
    Session::flash('home', "You don't have permission to view that page");
    Redirect::to('dashboard.php');
  }
	$errors = array();
	if(!Input::exists()) {
		Session::flash('home', 'select the valid data to proceed');
		Redirect::to('load.php');
	}
	$hash = Session::get(Config::get('session/load'));
	$destination = Input::get('destination');
	$price = Input::get('priceF');

	$vehicleObj = new Vehicle('vehicles');
	$driverObj = new Vehicle('drivers');
	$passengerObj = new Passenger('temp_passengers');

	try {
		$vehicle = $vehicleObj->get(array('id', '=', Input::get('vehicle')));	
		$prefill = $passengerObj->get(array('user_id', '=', $user->data()->id));  //try and filter with date too
    $sql = "SELECT COUNT(*) as count FROM `travels` WHERE DATE(date) = CURRENT_DATE AND source =". $user->data()->location." AND destination =". Input::get('destination')."";  //get total no of trip done for that day
      $tripNO = ((int) DB::getInstance()->query($sql)->first()->count) + 1 + Config::get('ticket/start');   //next value of ticket to use
    if(count($prefill) > $vehicle[0]->no_of_seats) {
      $prefill = array_slice($prefill, 0, $vehicle[0]->no_of_seats);
    }
		$driver = $driverObj->get(array('id', '=', Input::get('driver')));	
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
        <small>New Trip</small>
        <span style="margin-left:20px; font-size:.7em" class='alertme text text-center text-danger'></span>
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
        <li class="active">Load</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Main row -->
      <div class="row">
      	<section class="col-lg-9 connectedSortable" id="middle">
          <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title passengertitle">Passengers</h3>
                <div class="pull-middle box-tools">
                    <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                      <i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
                      <i class="fa fa-times"></i></button>
                </div>
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                <p class="passengererror hidden"></p>
                <form role="form" name="travelsettings" method="post" action="addwaybill.php">
                	<input type="hidden" name="destination" value="<?php echo $destination; ?>">
                	<input type="hidden" name="vehicle" value="<?php echo $vehicle[0]->id ; ?>">
                	<input type="hidden" name="driver" value="<?php echo $driver[0]->id ; ?>">
                	<input type="hidden" name="priceF" value="<?php echo $price; ?>" />
                </form>
                <table class="table table-bordered table-striped table-hover" id="passengers">
                  <thead>
                    <tr>
                      <th>S/N</th>
                      <th>Name</th>
                      <th>Phone</th>
                      <th>Email</th>
                      <th>Address</th>
                      <th>Blood group</th>
                      <th>Kin</th>
                      <th>Ticket No</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody id="finaltbody">
                  <?php
                  	$remaining = $vehicle[0]->no_of_seats;
                    if($prefill) { 
                    	$remaining = $vehicle[0]->no_of_seats - count($prefill);
                      foreach ($prefill as $key => $value) { 
                        $passengerObj->update($value->id, array('hash' => $hash, 'ticket' => $tripNO));
                        ?>
                      <tr>
                        <td class="sn"><?php echo $key + 1?></td>
                        <td class="name"><?php echo $value->name?></td>
                        <td><?php echo $value->phone?></td>
                        <td><?php echo $value->email?></td>
                        <td><?php echo $value->address?></td>
                        <td><?php echo $value->blood_group?></td>
                        <td><?php echo $value->next_of_kin?></td>
                        <td><?php echo $tripNO?></td>
                        <td class="remove"><i class='fa fa-times text-danger'></i></td>
                      </tr>
                    <?php } } ?>
                    
                    <?php 
                    if($remaining != 0) { 
                    	$no = count($prefill);
                    	for($i = 1; $i <= $remaining; $i++) {
                    	?>
                    	<tr class="formfill">
                    		<td class="sn"><?php echo ($i + $no);?></td>
                        <td class="name"><input type="text" class="form-control" id="name" name="name" value="" required autofocus="on"></td>
                        <td class='phone'><input type="tel" class="form-control" id="phone" name="phone" value=""></td>
                        <td class='email'><input type="email" class="form-control" id="email" name="email" value=""></td>
                        <td class='address'><input type="address" class="form-control" id="address" name="address" value=""></td>
                        <td class='blood_group'><input type="text" class="form-control" id="blood" name="blood" value=""></td>
                        <td class='kin'><input type="text" class="form-control" id="kin" name="kin" value=""></td>
                        <td><?php echo $tripNO;?></td>
                        <td><input type="submit" class="btn btn-primary" name="addpassenger" value="Add" /></td>
                    	</tr>
                    <?php } }
                  ?>
                  </tbody>
                </table>
                
                <!-- /.box-body -->
              </div>
              <div class="box-footer continue <?php if($remaining != 0) echo 'hidden'; ?>">
                <button class="btn btn-warning pull-right" id="continue">Continue</button>
              </div>
          </div> <p class="test"></p>
        </section>

        <section class="col-lg-3 connectedSortable" id="right">
          <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">Loading Status</h3>
                <div class="pull-middle box-tools">
                    <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                      <i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
                      <i class="fa fa-times"></i></button>
                </div>
              </div>
              <!-- /.box-header -->
              <div class="box-body">
                <table class="table table-bordered table-striped table-hover" id="status">
                  <tbody>
                    <tr class="price"><th>Price</th><td><?php echo $price; ?></td></tr>
                    <tr class="vehiclestatus"><th>Vehicle Status</th><td><?php echo ($remaining != 0)? $remaining." seat(s) left": "Full"; ?></td></tr>
                    <tr class="ac"><th>AC</th><td><?php echo ($vehicle[0]->ac == 0) ? "No" : "Yes" ?></td></tr>
                    <tr class="noofseats"><th>No of seats</th><td><?php echo $vehicle[0]->no_of_seats; ?></td></tr>
                    <tr class="driver"><th>Driver</th><td><?php echo $driver[0]->name ?></td></tr>
                    <tr class="destination"><th>Destination</th><td><?php echo $parkObj->get($destination, "park")->park; ?></td></tr>
                    <tr class="vehicle"><th>Price</th><td><?php echo $vehicle[0]->plate_no; ?></td></tr>
                  </tbody>
                </table>  
                <!-- /.box-body -->
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
		function appendToStatus($th, $td) {
      $class = $th.trim().toLowerCase().replace(/ +/g, "");
      if($('table#status tbody tr.'+$class).hasClass('init')) {
        $('table#status tbody tr.'+$class).removeClass('init');
        $('table#status tbody tr.'+$class).append(
          "<th>"+$th+"</th><td>"+$td+"</td>"
          );
      } else{
        $('table#status tbody tr.'+$class+' td').text($td);
      }
    }
		$hash = "<?php echo Session::get(Config::get('session/load')); ?>";

		//tied all events to this object
		$(document).on('click', 'input[name=addpassenger]', function(e) {
      e.preventDefault();
      $name = $(this).parent().siblings('td').find('input[name=name]').val();
      $current = this;
      if($name == '') {
        $(this).parent().siblings('td').find('input[name=name]').css('border', '1px solid red');
        return false;
      }
      $sn = $(this).parent().siblings('td.sn').text().trim();
      $phone = ($(this).parent().siblings('td').find('input[name=phone]').val() != "") ? $('input[name=phone]').val() : '08088888888';
      $email = ($(this).parent().siblings('td').find('input[name=email]').val() != "") ? $('input[name=email]').val() :'example@transport.com';
      $address = ($(this).parent().siblings('td').find('input[name=address]').val() != "") ? $('input[name=address]').val() : 'none';
      $blood_group = ($(this).parent().siblings('td').find('input[name=blood]').val() != "") ? $('input[name=blood]').val() : 'none';
      $next_of_kin = ($(this).parent().siblings('td').find('input[name=kin]').val() != "") ? $('input[name=kin]').val() : 'none';
      
      $.post('_addpassengers.php', {
        name: $name,
        phone: $phone,
        address: $address,
        email: $email,
        next_of_kin: $next_of_kin,
        blood_group: $blood_group,
        ticket: <?php echo $tripNO;?>,
        hash: $hash
      }, function($result) {
        if($result == '1') {
        	$('p.passengererror').addClass('hidden'); 
        	$($current).parent().siblings('td').find('input[name=name]').css('border', '1px solid gray');
          $($current).parents('tr').replaceWith(
            "<tr>"+
              "<td class='sn'>"+$sn+"</td>"+
              "<td class='name'>"+$name+"</td>"+
              "<td>"+$phone+"</td>"+
              "<td>"+$email+"</td>"+
              "<td>"+$address+"</td>"+
              "<td>"+$blood_group+"</td>"+
              "<td>"+$next_of_kin+"</td>"+
              "<td><?php echo $tripNO;?></td>"+
              "<td class='text-success'>Added</td>"+
            "</tr>"
            );
          $len = ($('tr.formfill').length == 0) ? "full" : $('tr.formfill').length + " seat(s) left" ;
          appendToStatus('Vehicle status', $len );
          if($('tr.formfill').length == 0) {
          	$('div.continue').removeClass('hidden');
          }
        } else if($result == 'X') {
          $('p.passengererror').text("Ensure all fields are filled with the correct value!");
        } else{
          $error = JSON.parse($result);
          $('p.passengererror').text($error).removeClass('hidden');   
        }
      });
      //send to temp table in DB and add to table
    }).on('click', 'td.remove', function(e) {
    	e.preventDefault();
    	$name = $(this).siblings('.name').text().trim();
    	$sn = $(this).siblings('.sn').text().trim();
      $current = this;
      $('div.continue').addClass('hidden');
      //remove from database and renumber table
      $.post('_addpassengers.php', {name: $name, hash: $hash, flag: 'D' }, function($result) {
        if($result == '1') {
          $('span.alertme').text($name+" removed.").slideDown('fast');
          $($current).parents('tr').replaceWith(
          	"<tr class='formfill'>"+
          		"<td class='sn'>"+$sn+"</td>"+
              "<td class='name'><input type='text' class='form-control' id='name' name='name' required ></td>"+
              "<td class='phone'><input type=tel class=form-control id=phone name='phone' ></td>"+
              "<td class='email'><input type='email' class='form-control' id='email' name='email' ></td>"+
              "<td class='address'><input type='address' class='form-control' id='address' name='address' ></td>"+
              "<td class='blood_group'><input type='text' class='form-control' id='blood' name='blood' ></td>"+
              "<td class='kin'><input type=text' class='form-control' id='kin' name='kin' ></td>"+
              "<td><?php echo $tripNO;?></td>"+
              "<td><input type='submit' class='btn btn-primary' name='addpassenger' value='Add' /></td>"+
          	"</tr>"
          	);
          $len = ($('tr.formfill').length == 0) ? "full" : $('tr.formfill').length + " seat(s) left" ;
          appendToStatus('Vehicle status', $len );
          if($('tr.formfill').length != 0) {
          	if(!($('div.continue').hasClass('hidden'))) {
          		$('div.continue').addClass('hidden');
          	}
          }
        } else{
          $('span.alertme').text('error removing passenger');
        }
      });
    }).on('click', 'button#continue', function(e) {
    	e.preventDefault();
      var $pass = true;
      $('input[name=vehicle], input[name=driver], input[name=destination], input[name=priceF]').each(function() {
        if($(this).val() =='') {
          alert("This page has been altered internally. Refresh or go to load page");
          $pass = false;
        } 
      });
      if($pass === true) {
        $('form[name=travelsettings]').submit();
      }
    });
	})
</script> 
<?php 
	require_once 'includes/content/footer.php';
?>