<?php 
	require_once 'includes/content/header.php';
  if(!$user->checkPermission(array('staff'))) {    //only staff can see it
    Session::flash('home', "You don't have permission to view that page");
    Redirect::to('dashboard.php');
  }
  $parkGet = $parkObj->get();
	$vehicleObj = new Vehicle('vehicles');
  $vehicles = $vehicleObj->get(array('current_location', '=', $user->data()->location, 'status', '=', Config::get('status/good')));
  $driverObj = new Driver('drivers');
  $drivers = $driverObj->get(array('current_location', '=', $user->data()->location, 'status', '=', Config::get('status/active')));
  $hash = Hash::unique();
  Session::put(Config::get('session/load'), $hash);
  Cookie::put(Config::get('cookie/load'), $hash, Config::get('cookie/expiry_one_day'));
  $passengerObj = new Passenger('temp_passengers');
  $prefill = $passengerObj->get(array('user_id', '=', $user->data()->id));  //try and filter with date too
?>
	
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?php echo $parkObj->get($user->data()->location, 'park')->park; ?>
        <small>New Trip Fix: refereshing after selecting vehicle/driver is a problem(they are no longer visible upon refresh) and action request for vehicle button</small>
        <span style="margin-left:20px; font-size:.7em" class='alertme text text-center text-danger'></span>
      </h1>
      <?php if(Session::exists('home')) {
		        echo "<p class='text text-center text-danger'>".Session::flash('home')."</p>";
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
        <!-- Left col -->
        <section class="col-lg-4 connectedSortable">
    			<div class="box box-success">
    				<div class="box-header with-border">
              <h3 class="box-title">Travel Init </h3>
            	<div class="pull-middle box-tools">
                  <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                    <i class="fa fa-minus"></i></button>
                  <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
                    <i class="fa fa-times"></i></button>
              </div>
            </div>
            <form role="form" name="travelsettings" method="post" action="addwaybill.php">
            <div class="box-body">
              <?php
                if(!empty($parkGet)) {
              ?>
                <div class="form-group">
                  <label>Destination</label>
                  <select style="text-transform:capitalize;" class="form-control" id="destination" name="destination" style="width: 100%;">
                    <option value="">--select--</option>
                    <?php
                      foreach ($parkGet as $value) { 
                        if($value->id == $user->data()->location) continue; ?>
                        <option value="<?php echo $value->id; ?>"><?php echo $value->park; ?></option>
                      <?php } ?>
                  </select>
                </div>
                <input type="hidden" name="priceF" />
              <?php } else { ?>
                <P> No Location yet. Loading can't occur.</P>
            <?php }
              if(!empty($vehicles)) {
            ?>
             <div class="form-group">
                <label>Vehicle to load</label>
                <select style="text-transform:capitalize;" class="form-control " name="vehicle" style="width: 100%;">
                  <option value="">--select--</option>
                  <?php
                    foreach ($vehicles as $value) { ?>
                      <option value="<?php echo $value->id; ?>"><?php echo $value->plate_no; ?></option>
                    <?php } ?>
                </select>
              </div>
             <?php } else {?>
              <p>No vehicle in park currently.</p>
              <a href="vehiclerequest.php"><button class="btn btn-primary" name="vehicleRequest" value="Request for Vehicle"></button></a>
              <?php } 
              if(!empty($drivers)) {
            ?>
              <div class="form-group">
                <label>Select driver for journey</label>
                <select style="text-transform:capitalize;" class="form-control " name="driver" style="width: 100%;">
                  <option value="">--select--</option>
                  <?php
                    foreach ($drivers as $value) { ?>
                      <option value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>
                    <?php } ?>
                </select>
              </div>
             <?php } else {?>
              <p>No driver in park currently.</p>
              <?php } ?>
            </div>
            </form>
            <?php 
             if(!empty($drivers) && !empty($vehicles)) { 
            ?>
            <div class="box-footer">
              <input type="submit" class="btn btn-primary pull-middle" name="selectload" value="Go">
            </div>
            <?php } ?>
    			</div><p class="test"></p>          
        </section>
        <!-- /.Left col -->
        <!-- middle col (We are only adding the ID to make the widgets sortable)-->
        <section class="col-lg-5 connectedSortable" id="middle">
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
                <div class="price col-lg-6 hidden">
                  <div class="form-group">
                    <label for="price">Price</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="price" name="price" value="" autofocus="on" data-inputmask="'alias': 'decimal'" data-mask>
                      <div class="input-group-addon">
                      <i class="fa fa-database"></i>
                     </div>
                    </div>
                  </div>
                  <button class="btn btn-danger pull-middle" name="setprice" >Set Price</button>
                </div>
                <table class="table table-bordered table-striped table-hover" id="passengers">
                  <thead>
                    <tr>
                      <th>S/N</th>
                      <th>Name</th>
                      <th>Phone</th>
                      <th>Ticket No</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php
                    if($prefill) { 
                      foreach ($prefill as $key => $value) { 
                        $passengerObj->update($value->id, array('hash' => $hash));
                        ?>
                      <tr>
                        <td class="sn"><?php echo $key + 1?></td>
                        <td class="name"><?php echo $value->name?></td>
                        <td><?php echo $value->phone?></td>
                        <td class="ticket"><?php echo $value->ticket?></td>
                      </tr>
                    <?php } }
                  ?>
                  </tbody>
                </table>
                
                <!-- /.box-body -->
              </div>
              <div class="box-footer hidden addpassenger">
                  <button class="btn btn-primary addpassenger" data-toggle="modal" data-target="#passengersform">Add passenger</button>
                </div>
              <div class="box-footer continue hidden">
                <button class="btn btn-warning" id="continue">Continue</button>
              </div>
          </div> 
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
                    <tr class="init price"></tr>
                    <tr class="init vehiclestatus"></tr>
                    <tr class="init ac"></tr>
                    <tr class="init noofseats"></tr>
                    <tr class="init driver"></tr>
                    <tr class="init destination"></tr>
                    <tr class="init vehicle"></tr>
                  </tbody>
                </table>
                
                <!-- /.box-body -->
              </div>
          </div>
        </section>
        <!-- middle col -->
      </div>
      <!-- /.row (main row) -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
 <script type="text/javascript">
   $(document).ready(function() {
    $source = <?php echo $user->data()->location; ?> ;
    $('span.alertme').hide();
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
    function removeAppend($arr) {
      $($arr).each(function() {
        $class = this.trim().toLowerCase().replace(/ +/g, "");
        $('table#status tbody tr.'+$class).html("").addClass('init');
      });
    }
    function renumber() {
      if($('table#passengers tbody tr').length > 0) {
        var sn = 1;
        $('table#passengers tbody tr').each(function() {
          $(this).find('td.sn').text(sn);
          sn++;
        });
      }
    }
    function runPost() {
      $pre = $('select[name=vehicle]').val();
      $text = $('select[name=vehicle] option:selected').text().trim();
      $.post('_realtimestatus.php', {vehicle: 1, location: $source}, function($result) {
        $('select[name=vehicle] option:first-child').siblings().remove();
        $(JSON.parse($result)).each(function () {
          $('select[name=vehicle]').append(
            "<option value='"+this.id+"'>"+this.plate_no+"</option>"
            );
        });
      });
    }
    var $interval = setInterval(function() {
      if($('select[name=vehicle]').val() != '') {
        clearInterval($interval);
        return false; 
      }
      //runPost();
    }, 2000);

    var $noOfSeats = '', $ac = '', $hash = '', $priceCheck = true, $ticket = <?php echo Config::get('ticket/start');?>;
    $hash = "<?php echo $hash; ?>";
    $('section#middle, section#right').hide();
    
    $('select[name=destination]').change(function() {
      $ticket = <?php echo Config::get('ticket/start');?>;
      if(!($('body').hasClass('sidebar-collapse'))) {
        $('a.sidebar-toggle').click();                    //close navigation in this page
      }
      $('section#middle, section#right').hide();
      $('div.addpassenger').addClass('hidden');
      $('h3.passengertitle').text("Passengers to "+ $('select[name=destination] option:selected').text());
      $destination = $(this).val();
      if($destination != '') {
        $.post('_getprice.php', {source: $source, destination: $destination, hash: $hash }, function($result) {
          $parse = JSON.parse($result);
          $price = $parse.price;
          $ticket += parseInt($parse.trip);
          $('table#passengers tr td.ticket').text($ticket);
          if($price != "X") {
            appendToStatus('Price', $price);
            $('input[name=priceF').val($price);
            $('div.addpassenger').removeClass('hidden');
            $('table#passengers').show();
            $('div.price').addClass('hidden');
            $('section#middle, section#right').show();
            appendToStatus('Destination', $('select[name=destination] option:selected').text());
            $priceCheck = true;
          } else if($price == "X") {
            $('div.price').removeClass('hidden');
            $('table#passengers').hide();
            $('div.continue').addClass('hidden');
            removeAppend(['price']);
            $('input[name=price]').attr('required', 'required');
            $('h3.passengertitle').text("Set price for journey");
            $('section#middle, section#right').show();
            appendToStatus('Destination', $('select[name=destination] option:selected').text());
            $priceCheck = false;
          } else if($result == "0") {
            $('section#middle, section#right').hide();
          }
        });
      } else {
        $('section#middle, section#right').hide();
        removeAppend(['price', 'Destination']);
      }
    });

    $('button[name=setprice]').click(function(e) {
      e.preventDefault();
      if($('input[name=price]').val() == "" || $('input[name=price]').val() <= 0) {
        $('input[name=price]').css('border', '1px solid red');
      }else {
        $('input[name=price]').css('border', '1px solid gray');
        $('div.price').addClass('hidden');
        $('table#passengers').show();
        $('input[name=priceF').val($('input[name=price]').val());
        $('h3.passengertitle').text("Passengers to "+ $('select[name=destination] option:selected').text());
        $('div.addpassenger').removeClass('hidden');
        appendToStatus('Price', $('input[name=price]').val());
        $priceCheck = true;
      }
    });

    $(document).on('focus', 'select[name=driver]', function() {
      $(this).data('pre', $(this).val()); //save previous selected value
      }).on('change', 'select[name=driver]', function() {
      $old = $(this).data('pre');
      $(this).blur();   //blur the button to make it get old value when focus is received
      $new = $(this).val();
      $.post('_updatestatus.php', {old: $old, new: $new, driver: 1}, function($result) {
      });
      if($(this).val() != '') {
        appendToStatus('Driver', $('select[name=driver] option:selected').text());
        $('section#right').slideDown('slow'); 
      } else {
        removeAppend(['Driver']);
      }
    });

    $(document).on('focus', 'select[name=vehicle]', function() {
      $(this).data('pre', $(this).val()); //save previous selected value
      }).on('change', 'select[name=vehicle]', function() {
      $old = $(this).data('pre');
      $(this).blur();   //blur the button to make it get old value when focus is received
      $new = $(this).val();
      $.post('_updatestatus.php', {old: $old, new: $new, vehicle: 1}, function($result) {
      });

      $vehicleId = $(this).val();
      $('div.continue').addClass('hidden');
      if($vehicleId != '') {
        appendToStatus('Vehicle', $('select[name=vehicle] option:selected').text());
        $.post('_getprice.php', {vehicleid: $vehicleId}, function($result) {
          result = JSON.parse($result);
          $noOfSeats = result.no_of_seats;
          $ac = (result.ac == '1') ? "Yes" : "No";
          appendToStatus('No of seats', $noOfSeats);
          appendToStatus('Ac', $ac);

          $('section#right').slideDown('slow');
          if($('table#passengers tbody tr').length < ($noOfSeats)) {  //check after adding the last one
            $('div.addpassenger').show();
            appendToStatus("Vehicle Status", $noOfSeats - $('table#passengers tbody tr').length + " spaces(s) left");
          }
          if($('table#passengers tbody tr').length > ($noOfSeats)) {
            $('div.addpassenger').hide();
            alert("you need to unload some passengers");
            $('table#passengers tbody tr').each(function() {
              if(!($(this).find('td:last-child').hasClass('remove'))){
                $(this).append("<td class='remove'><i class='fa fa-times text-danger'></i></td>");
              }
            });
          }
          if($('table#passengers tbody tr').length == ($noOfSeats)) {
            $('div.addpassenger').hide();
            appendToStatus("vehicle Status", "Full");
            $('div.continue').removeClass('hidden');
          } 
        });
      } else {
        $noOfSeats = 0;
        removeAppend(['Vehicle', 'Ac', 'Vehicle status', 'No of seats']);
        $('div.addpassenger').show();
      }
    });

    $('table#passengers tbody').on('click', 'tr td.remove', function(e) {
      $name = $(this).siblings('.name').text().trim();
      $current = this;
      $('div.continue').addClass('hidden');
      //remove from database and renumber table
      $.post('_addpassengers.php', {name: $name, hash: $hash, flag: 'D' }, function($result) {
        if($result == '1') {
          $('span.alertme').text($name+" removed.").slideDown('fast');
          $($current).parents('tr').remove();
          if($('table#passengers tbody tr').length == ($noOfSeats)) {
            $('div.addpassenger').hide();
            appendToStatus("vehicle Status", "Full");
            $('div.continue').removeClass('hidden');
          }
          if($('table#passengers tbody tr').length < ($noOfSeats)) {
            $('div.addpassenger').show();
            appendToStatus("Vehicle Status", $noOfSeats - $('table#passengers tbody tr').length + " spaces(s) left");
            $('div.continue').addClass('hidden');
          }
          renumber()
        } else{
          $('span.alertme').text('error removing passenger');
        }
      });
    });

    $('input[name=addpassenger]').click(function(e) {
      e.preventDefault();
      $name = $('input[name=name]').val();
      if($name == '') {
        $('input[name=name]').css('border', '1px solid red');
        return false;
      }
      $('table#passengers').show();
      //remove addpassenger button when noofpassengers == noofseats
      if($noOfSeats != "") {
        if($('table#passengers tbody tr').length == ($noOfSeats-1)) {  //check after adding the last one
          $('div.addpassenger').hide();

          appendToStatus("vehicle Status", "Full")
          $('div.continue').removeClass('hidden');
        } else{
          appendToStatus("Vehicle status", ($noOfSeats - $('table#passengers tbody tr').length - 1) + " spaces(s) left")
        }
      }
      
      $phone = ($('input[name=phone]').val() != "") ? $('input[name=phone]').val() : '08088888888';
      $email = ($('input[name=email]').val() != "") ? $('input[name=email]').val() : 'example@transport.com';
      $address = ($('input[name=address]').val() != "") ? $('input[name=address]').val() : 'none';
      $blood_group = ($('input[name=blood]').val() != "") ? $('input[name=blood]').val() : 'none';
      $next_of_kin = ($('input[name=kin]').val() != "") ? $('input[name=kin]').val() : 'none';
      $.post('_addpassengers.php', {
        name: $name,
        phone: $phone,
        address: $address,
        email: $email,
        next_of_kin: $next_of_kin,
        blood_group: $blood_group,
        ticket: $ticket,
        hash: $hash
      }, function($result) {
        if($result == '1') {
         // $('form[name=passenger]').reset();
          $('button.close').click();
          $('table#passengers tbody').append(
            "<tr>"+
              "<td class='sn'>"+($('table#passengers tbody tr').length + 1)+"</td>"+
              "<td class='name'>"+$name+"</td>"+
              "<td>"+$phone+"</td>"+
              "<td class='ticket'>"+$ticket+"</td>"+
            "</tr>"
            );
        } else if($result == 'X') {
          $('p.passengererror').text("Ensure all fields are filled with the correct value!");
        } else{
          $error = JSON.parse($result);
          $('p.passengererror').text($error);   
        }
      });
      //send to temp table in DB and add to table
    });

    $('button#continue').click(function(e) {
      if($('table#passengers tbody tr').length > ($noOfSeats)) {
        $('div.addpassenger').hide();
        alert("you need to unload some passengers");
        $('table#passengers tbody tr').each(function() {
          if(!($(this).find('td:last-child').hasClass('remove'))){
            $(this).append("<td class='remove'><i class='fa fa-times text-danger'></i></td>");
          }
        });
        return false;
      }
      var $pass = true;
      $('select[name=vehicle], select[name=driver], select[name=destination]').each(function() {
        if($(this).val() =='') {
          e.preventDefault();
          $(this).css('border','1px solid red');
          $pass = false;
        } 
      });
      if($pass === true) {
        $('select[name=vehicle], select[name=driver], select[name=destination]').css('borderColor','green');
        $('form[name=travelsettings').submit();
      }
    });

    $('input[name=selectload]').click(function(e) {
      var $pass = true;
      e.preventDefault();
      $('select[name=vehicle], select[name=driver], select[name=destination]').each(function() {
        if($(this).val() =='') {
          e.preventDefault();
          $(this).css('border','1px solid red');
          $pass = false;
        } else{
          $(this).css('border', '1px solid gray');
        }
      });
      if(!$priceCheck) {
        $pass = false; 
        $('input[name=price]').css('border', '1px solid red');
      } else {
        $('input[name=price]').css('border', '1px solid gray');
      }
      if($('input[name=price]').val() != "" ) {
        $('button[name=setprice]').css('border', '1px solid red').removeClass('btn-danger').addClass('btn-success');
        $('input[name=price]').css('border', '1px solid gray');
      }
      if($pass === true) {
        $('select[name=vehicle], select[name=driver], select[name=destination]').css('borderColor','green');
        $('form[name=travelsettings]').attr('action', 'selectload.php');
        $('form[name=travelsettings').submit();
      }
    });
   })
 </script>
<div class="example-modal">
  <div id="passengersform" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Passenger's Info</h4>
          <p class="passengererror text-danger"></p>
        </div>
        <div class="modal-body">
          <form role="form" method="post" name="passenger">
            <div class="form-group">
              <label for="name">Name</label>
              <div class="input-group">
                <input type="text" class="form-control" id="name" name="name" value="" required autofocus="on">
                <div class="input-group-addon">
                <i class="fa fa-database"></i>
               </div>
              </div>
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <div class="input-group">
                <input type="email" class="form-control" id="email" name="email" value="">
                <div class="input-group-addon">
                <i class="fa fa-database"></i>
               </div>
              </div>
            </div>

            <div class="form-group">
              <label for="phone">Phone</label>
              <div class="input-group">
                <input type="tel" class="form-control" id="phone" name="phone" value="">
                <div class="input-group-addon">
                <i class="fa fa-database"></i>
               </div>
              </div>
            </div>

            <div class="form-group">
              <label for="address">Address</label>
              <div class="input-group">
                <input type="address" class="form-control" id="address" name="address" value="">
                <div class="input-group-addon">
                <i class="fa fa-database"></i>
               </div>
              </div>
            </div>

            <div class="form-group">
              <label for="blood">Blood Group</label>
              <div class="input-group">
                <input type="text" class="form-control" id="blood" name="blood" value="">
                <div class="input-group-addon">
                <i class="fa fa-database"></i>
               </div>
              </div>
            </div>

            <div class="form-group">
              <label for="kin">Next of kin</label>
              <div class="input-group">
                <input type="text" class="form-control" id="kin" name="kin" value="">
                <div class="input-group-addon">
                <i class="fa fa-database"></i>
               </div>
              </div>
            </div>

            <div class="box-footer">
              <input type="submit" class="btn btn-primary" name="addpassenger" value="Add">
            </div>
          </form>
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