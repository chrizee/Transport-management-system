<?php
	require_once 'includes/content/header.php';
	$db = DB::getInstance();
	$routeObj = new Route('routes');
	$route = $routeObj->get(array('1','=','1'),'source,destination,price');
	$park = $parkObj->get();
	$errors = array();
	$pass = true;
	if(Input::exists() && !empty(Input::get('sendEmail'))) {
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'subject' => array(
				'required' => true,
				'max' => '100',
				),
			'message' => array(
				'required' => true,
				'min' => '5',
				),
			));
		if(empty(Input::get('recipient')) && empty(Input::get('recipients'))) {
			$errors[] = "Recipient is required";
			$pass = false;
		}
		if($validation->passed() && $pass) {
			try {
				$message = new Message();
				if(!empty(Input::get('recipients'))) {
					$_POST['to'] = "*";
					$message->put();
				} elseif(!empty(Input::get('recipient'))) {
					foreach (Input::get('recipient') as $key => $value) {
						$_POST['location'] = '';		//necessary to prevent location from being available in the put method of the message class
							if(strstr($value,'all')) {
							$_POST['location'] = explode('--',$value)[1];
							$message->put();
						}	else{
							$_POST['to'] = $value;
							$message->put();
						}
					}
				}
				Session::flash('home', 'Message sent');
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
        <small>Control panel</small>
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
        <li class="active">Dashboard</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php echo $total = $db->query("SELECT COUNT(id) AS count FROM travels")->first()->count ?></h3>

              <p>Total trips</p>
            </div>
            <div class="icon">
              <i class="ion ion-speedometer"></i>
            </div>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php $lo = $db->query("SELECT COUNT(id) AS count FROM travels WHERE source = {$user->data()->location}")->first()->count; echo round(($lo/$total)*100, 2) ?><sup style="font-size: 20px">%</sup></h3>

              <p>Trips Initiated from <?php echo $parkObj->get($user->data()->location, 'park')->park;?></p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3><?php print_r($db->query("SELECT COUNT(id) AS count FROM waybill WHERE status = ".Config::get('waybill/picked'))->first()->count) ?></h3>

              <p>Total waybill delivered </p>
            </div>
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3>65</h3>

              <p>Unique Visitors</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
      </div>
      <!-- /.row -->
      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <section class="col-lg-7 connectedSortable">
          <!-- Custom tabs (Charts with tabs)-->
          <div class="nav-tabs-custom">
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs pull-right">
              <li><a href="#routes" data-toggle="tab">Routes</a></li>
              <li class="active"><a href="#list" data-toggle="tab">Price list</a></li>
              <li class="pull-left header"><i class="fa fa-inbox"></i> Our Routes</li>
            </ul>
            <div class="tab-content no-padding">
              <!-- Morris chart - Sales -->
              <div class="chart tab-pane" id="routes" style="position: relative; height: 300px; padding:10px;">
								<?php if(count($park) != 0) {?>
								<p class="text text-info">Our parks are located in the following locations across the country:</p>
								<ol style="text-transform:capitalize">
									<?php

									foreach ($park as $key => $value) {
									?>
									<li><?php echo $value->park."."; ?></li>
								<?php } ?>
							</ol>
						<?php }else{?>
							<p>No park Available.</p>
						<?php }?>
							</div>
              <div class="chart tab-pane active" id="list" style="position: relative;padding:.4em;">
								<table class="table text-center table-condensed table-hover datatable">
									<thead>
										<tr>
											<td>Source</td>
											<td></td>
											<td>destination</td>
											<td>Price</td>
										</tr>
									</thead>
									<tbody>
										<?php
											$classes = ['primary','danger','info','success'];
											foreach ($route as $key => $value) {
												$class = $classes[rand(1, count($classes)-1)];
												//$class2 = $classes[rand(1, count($classes)-1)];
										?>
										<tr class="bg-<?php echo $class; ?>">
											<td><?php echo $parkObj->get($value->source, 'park')->park;?></td>
											<td><i class="fa fa-angle-double-right"></i></td>
											<td><?php echo $parkObj->get($value->destination, 'park')->park;?></td>
											<td><?php echo "#".$value->price?></td>
										</tr>
									<?php } ?>
									</tbody>
								</table>
							</div>
            </div>
          </div>
          <!-- /.nav-tabs-custom -->

          <!-- quick email widget -->
          <div class="box box-info">
            <div class="box-header">
              <i class="fa fa-envelope"></i>

              <h3 class="box-title">Quick Email</h3>
              <!-- tools box -->
              <div class="pull-right box-tools">
                <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove">
                  <i class="fa fa-times"></i></button>
              </div>
              <!-- /. tools -->
            </div>
            <div class="box-body">
              <form action="" method="post" name="quickMail">
								<div class="form-group">
									<?php
										$staffs = $user->getStaffs(array('id','!=', $user->data()->id));
									?>
									<label>Recipients</label>
									<select class="form-control select2" multiple="multiple" name="recipient[]" data-placeholder="Select recipient(s)" style="width: 100%;">
									<?php
									if($user->checkPermission(array('admin','manager'))) {
										foreach ($park as $key => $value) { ?>
											<option value="<?php echo "all--".$value->id?>"><?php echo "All in ".$value->park; ?></option>
										<?php } }
											foreach ($staffs as $value) { ?>
												<option value="<?php echo $value->id; ?>"><?php echo str_pad($value->name,20,'-'); ?><span class="pull-right"><?php echo $user->getTitle($value->groups)." in ".$parkObj->get($value->location, 'park')->park; ?></span></option>
										<?php } ?>
									</select>
									<?php if($user->checkPermission(array('admin','manager'))) { ?>
										<br/><br/>
									<label>All staffs&nbsp;&nbsp;&nbsp;</label>
									<label>
										<input type="radio" name="recipients" value="all">
									</label>
									<?php } ?>
								</div>
                <div class="form-group">
                  <input type="text" class="form-control" name="subject" placeholder="Subject" required>
                </div>
                <div>
                  <textarea class="textarea" name="message" placeholder="Message" style="width: 100%; height: 125px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;" required></textarea>
                </div>
		            <div class="box-footer clearfix">
		              <input type="submit" class="pull-right btn btn-default" name="sendEmail" id="sendEmail" value="Send">
		            </div>
							</form>
						</div>
          </div>

        </section>
        <!-- /.Left col -->
        <!-- right col (We are only adding the ID to make the widgets sortable)-->
        <section class="col-lg-5 connectedSortable">
          <!-- Calendar -->
          <div class="box box-solid bg-green-gradient">
            <div class="box-header">
              <i class="fa fa-calendar"></i>
              <h3 class="box-title">Calendar</h3>
              <!-- tools box -->
              <div class="pull-right box-tools">
                <button type="button" class="btn btn-success btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-success btn-sm" data-widget="remove"><i class="fa fa-times"></i>
                </button>
              </div>
              <!-- /. tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
              <!--The calendar -->
              <div id="calendar" style="width: 100%"></div>
            </div>
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
		$(document).on('click', 'input[name=sendEmail]', function(e) {
			//e.preventDefault();
			//alert('Select recipient');
			if($("select[name^=recipient] :selected").val() == '') {
				e.preventDefault();
				alert('Select recipient');
			}
		});
		//The Calender
		$("#calendar").datepicker();
	})
</script>
<?php
	require_once 'includes/content/footer.php';
?>
