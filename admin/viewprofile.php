<?php
	require_once 'includes/content/header.php';
  if(empty(Input::get("user"))) {
    Session::flash('home', "select a valid user.");
    Redirect::to('viewstaff');
  }
  $userId = decode(Input::get('user'));
	$success = false;
	$errors = array();
  try {
    $staff = $user->getStaffs(array('id', '=', $userId));
    if(count($staff) == 0) {
      Session::flash('home', "select a valid user.");
      Redirect::to('viewstaff');
    }
    $staff = $staff[0];
  } catch (Exception $e) {
    print_r($e->getMessage());
  }
	if(!empty($user->getMeta($userId)->results())) $metaInfo = $user->getMeta($staff->id)->first();
	if(Input::exists()) {
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'subject' => array(
				'max' => '100',
				),
			));
		if($validation->passed()) {
			try {
				$message = new Message();
        $message->put();
				Session::flash('home', 'Message sent' );
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
        <small>Profile</small>
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
        <li class="active">View</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Main row -->
    <div class="row">
        <div class="col-md-3">
          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="img/<?php echo (empty($metaInfo->pic_src)) ? 'avatar-male.png' : $metaInfo->pic_src ?>" alt="User profile picture">

              <h3 class="profile-username text-center"><?php echo $staff->name ?></h3>
              <p class="text-center" style="margin:-10px 0px 0px"><small><?php echo $staff->email?></small></p>
              <p class="text-muted text-center"><?php  echo $user->getTitle($staff->groups)." in ".$parkObj->get($staff->location, 'park')->park; ?></p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Trips loaded</b> <span class="pull-right text-primary"><?php  echo $user->getTripCount($userId); ?></span>
                </li>
                <li class="list-group-item">
                  <b>Phone</b> <span class="pull-right text-primary"><?php echo $staff->phone; ?></span>
                </li>
                <li class="list-group-item">
                  <b>status</b> <span class="pull-right text-primary"><?php
                    switch ($staff->status) {
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
                   ?></span>
                </li>
                <li class="list-group-item">
                  <b>Date Joined</b> <span class="pull-right text-primary"><?php $date = new DateTime($staff->created); echo $date->format('d-M-Y');?></span>
                </li>
              </ul>

            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          <!-- About Me Box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">About <?php echo $staff->name ?></h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <strong><i class="fa fa-book margin-r-5"></i> Education</strong>

              <p class="text-muted">
              	<?php
              		$text = '';
              		if(!empty($metaInfo->degree)) {
	              		if($metaInfo->degree) {
	              			$text = $metaInfo->degree;
	              			if($metaInfo->course) $text .= " in ".$metaInfo->course;
	              			if($metaInfo->school) $text .= " from the ".$metaInfo->school;
	              			if($metaInfo->location) $text .= " at ".$metaInfo->location;
	              		}
	              	}
              		echo $text;
              	?>
              </p>

              <hr>

              <strong><i class="fa fa-map-marker margin-r-5"></i> Location</strong>

              <p class="text-muted"><?php echo $parkObj->get($staff->location, 'park')->park; ?></p>

              <hr>

              <strong><i class="fa fa-pencil margin-r-5"></i> Skills</strong>

              <p>
              	<?php
              		if(!empty($metaInfo->skills)) {
	              		$skills = explode(',', $metaInfo->skills);
	              		$style = ['label-danger', 'label-success', 'label-info', 'label-warning', 'label-primary'];
	              		foreach ($skills as $key => $value) {
	              			$class = $style[rand(1, count($style)-1)];

              	?>
                <span class="label <?php echo $class; ?>"><?php echo $value?></span>
                <?php } } ?>
              </p>

              <hr>

              <strong><i class="fa fa-file-text-o margin-r-5"></i> Experience</strong>

              <p><?php echo (!empty($metaInfo->skills)) ? $metaInfo->experience: '' ; ?></p>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#timeline" data-toggle="tab">Timeline</a></li>
              <li><a href="#contact" data-toggle="tab">Contact</a></li>
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="timeline">
                <!-- The timeline -->
                <ul class="timeline timeline-inverse">
                  <!-- timeline time label -->
                  <li class="time-label">
                        <span class="bg-red">
                          10 Feb. 2014
                        </span>
                  </li>
                  <!-- /.timeline-label -->
                  <!-- timeline item -->
                  <li>
                    <i class="fa fa-envelope bg-blue"></i>

                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock-o"></i> 12:05</span>

                      <h3 class="timeline-header"><a href="#">Support Team</a> sent you an email</h3>

                      <div class="timeline-body">
                        Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                        weebly ning heekya handango imeem plugg dopplr jibjab, movity
                        jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle
                        quora plaxo ideeli hulu weebly balihoo...
                      </div>
                      <div class="timeline-footer">
                        <a class="btn btn-primary btn-xs">Read more</a>
                        <a class="btn btn-danger btn-xs">Delete</a>
                      </div>
                    </div>
                  </li>
                  <!-- END timeline item -->
                  <!-- timeline item -->
                  <li>
                    <i class="fa fa-user bg-aqua"></i>

                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock-o"></i> 5 mins ago</span>

                      <h3 class="timeline-header no-border"><a href="#">Sarah Young</a> accepted your friend request
                      </h3>
                    </div>
                  </li>
                  <!-- END timeline item -->
                  <!-- timeline item -->
                  <li>
                    <i class="fa fa-comments bg-yellow"></i>

                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock-o"></i> 27 mins ago</span>

                      <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>

                      <div class="timeline-body">
                        Take me to your leader!
                        Switzerland is small and neutral!
                        We are more like Germany, ambitious and misunderstood!
                      </div>
                      <div class="timeline-footer">
                        <a class="btn btn-warning btn-flat btn-xs">View comment</a>
                      </div>
                    </div>
                  </li>
                  <!-- END timeline item -->
                  <!-- timeline time label -->
                  <li class="time-label">
                        <span class="bg-green">
                          3 Jan. 2014
                        </span>
                  </li>
                  <!-- /.timeline-label -->
                  <!-- timeline item -->
                  <li>
                    <i class="fa fa-camera bg-purple"></i>

                    <div class="timeline-item">
                      <span class="time"><i class="fa fa-clock-o"></i> 2 days ago</span>

                      <h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos</h3>

                      <div class="timeline-body">
                        <img src="http://placehold.it/150x100" alt="..." class="margin">
                        <img src="http://placehold.it/150x100" alt="..." class="margin">
                        <img src="http://placehold.it/150x100" alt="..." class="margin">
                        <img src="http://placehold.it/150x100" alt="..." class="margin">
                      </div>
                    </div>
                  </li>
                  <!-- END timeline item -->
                  <li>
                    <i class="fa fa-clock-o bg-gray"></i>
                  </li>
                </ul>
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="contact">
                <form class="form-horizontal" method="post" enctype="multipart/form-data" name="contactStaff">
                  <div class="box box-primary">
                    <div class="box-header with-border">
                      <h3 class="box-title">Compose New Message</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                      <div class="form-group no-margin-on-form">
                        <input class="form-control" type="email" name="recipient" value="<?php echo $staff->email;?>" disabled>
                        <input class="form-control" type="hidden" name="to" value="<?php echo $staff->id;?>">
                      </div>
                      <div class="form-group no-margin-on-form">
                        <input class="form-control" type="text" name="subject" placeholder="Subject:" autofocus="on" required>
                      </div>
                      <div class="form-group no-margin-on-form">
                        <textarea id="compose-textarea" name="message" class="form-control" style="height: 300px">

                        </textarea>
                      </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                      <div class="pull-right">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-envelope-o"></i> Send</button>
                      </div>
                      <button type="reset" class="btn btn-default"><i class="fa fa-times"></i> Discard</button>
                    </div>
                    <!-- /.box-footer -->
                  </div>
                </form>
              </div>
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row (main row) -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<script type="text/javascript">
  $(document).ready(function () {
    $(document).on('click', "button[type='submit']", function(e) {
      if($("form[name='contactStaff'] textarea").val() == '') {
        e.preventDefault();
        $('iframe').css('border', '1px solid red');
        $('textarea').css('border', '1px solid red');
      }
    });
  })
</script>
<?php
	require_once 'includes/content/footer.php';
?>
