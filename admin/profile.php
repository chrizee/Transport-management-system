<?php 
	require_once 'includes/content/header.php';	
  if(!$user->checkPermission(array('*'))) {    //all can see it
    Session::flash('home', "You don't have permission to view that page");
    Redirect::to('dashboard');
  }
	$errors = array();
	//if(!empty($user->getMeta()->results())) $meta = $user->getMeta()->first();
	if(Input::exists()) {
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'phone' => array(
				'function' => 'checkPhone',
				),
			'email' => array(
				'function' => 'checkEmail',
				),
			));
		
		if(!empty($_FILES['photo'])) {
			$validation->checkPic('photo');
		}
		
		if($validation->passed()) {

			$picname = (!empty($meta->pic_src)) ? $meta->pic_src : '' ;
			if(!empty($_FILES['photo'])) {
				if($name = $user->movePic('photo')) {
					$picname = $name;
				}
			}
			try {
				$user->update(array(
					'phone' => Input::get('phone'),
					'email' => Input::get('email'),
				));
				$user->updateMeta(array(
          'user_id' => $user->data()->id,
					'degree' => Input::get('degree'),
					'school' => Input::get('school'),
					'location' => Input::get('location'),
					'course' => Input::get('course'),
					'experience' => Input::get('experience'),
					'skills' => Input::get('skills'),
					'pic_src' => $picname,
					));
				Session::flash('home', 'Profile updated' );	
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
              <img class="profile-user-img img-responsive img-circle" src="img/<?php echo (empty($meta->pic_src)) ? 'avatar-male.png' : $meta->pic_src ?>" alt="User profile picture">

              <h3 class="profile-username text-center"><?php echo $user->data()->name ?></h3>
              <p class="text-center" style="margin:-10px 0px 0px"><small><?php echo $user->data()->email?></small></p>
              <p class="text-muted text-center"><?php  echo $user->getTitle()." in ".$parkObj->get($user->data()->location, 'park')->park; ?></p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Trips loaded</b> <span class="pull-right text-primary"><?php  echo $user->getTripCount(); ?></span>
                </li>
                <li class="list-group-item">
                  <b>Phone</b> <span class="pull-right text-primary"><?php echo $user->data()->phone; ?></span>
                </li>
                <li class="list-group-item">
                  <b>status</b> <span class="pull-right text-primary"><?php 
                    switch ($user->data()->status) {
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
                  <b>Date Joined</b> <span class="pull-right text-primary"><?php $date = new DateTime($user->data()->created); echo $date->format('d-M-Y');?></span>
                </li>
              </ul>

            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          <!-- About Me Box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">About Me</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <strong><i class="fa fa-book margin-r-5"></i> Education</strong>

              <p class="text-muted">
              	<?php 
              		$text = '';
              		if(!empty($meta->degree)) {
	              		if($meta->degree) {
	              			$text = $meta->degree;
	              			if($meta->course) $text .= " in ".$meta->course;
	              			if($meta->school) $text .= " from the ".$meta->school;
	              			if($meta->location) $text .= " at ".$meta->location;
	              		}
	              	}
              		echo $text;
              	?>
              </p>

              <hr>

              <strong><i class="fa fa-map-marker margin-r-5"></i> Location</strong>

              <p class="text-muted"><?php echo $parkObj->get($user->data()->location, 'park')->park; ?></p>

              <hr>

              <strong><i class="fa fa-pencil margin-r-5"></i> Skills</strong>

              <p>
              	<?php 
              		if(!empty($meta->skills)) {
	              		$skills = explode(',', $meta->skills);
	              		$style = ['label-danger', 'label-success', 'label-info', 'label-warning', 'label-primary'];
	              		foreach ($skills as $key => $value) {
	              			$class = $style[rand(1, count($style)-1)];
              		
              	?>
                <span class="label <?php echo $class; ?>"><?php echo $value?></span>
                <?php } } ?>
              </p>

              <hr>

              <strong><i class="fa fa-file-text-o margin-r-5"></i> Experience</strong>

              <p><?php echo (!empty($meta->skills)) ? $meta->experience: '' ; ?></p>
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
              <li><a href="#settings" data-toggle="tab">Settings</a></li>
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

              <div class="tab-pane" id="settings">
                <form class="form-horizontal" method="post" enctype="multipart/form-data">
                  <div class="form-group">
                    <label for="inputPhone" class="col-sm-2 control-label">Phone</label>

                    <div class="col-sm-10">
                      <input type="tel" class="form-control" id="inputPhone" name="phone" placeholder="phone" value="<?php echo $user->data()->phone; ?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputEmail" class="col-sm-2 control-label">Email</label>

                    <div class="col-sm-10">
                      <input type="email" class="form-control" id="inputEmail" name="email" placeholder="Email" value="<?php echo $user->data()->email; ?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputDegree" class="col-sm-2 control-label">Degree</label>

                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputDegree" name="degree" placeholder="B.Sc, B.Eng, B.Art..." value="<?php echo (!empty($meta->degree)) ? $meta->degree: '' ?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputCourse" class="col-sm-2 control-label">Course</label>

                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputCourse" name="course" placeholder="" value="<?php echo (!empty($meta->course)) ? $meta->course: '' ?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputSchool" class="col-sm-2 control-label">School</label>

                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputSchool" name="school" placeholder="University of ..." value="<?php echo (!empty($meta->school)) ? $meta->school: '' ?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputLocation" class="col-sm-2 control-label">Location</label>

                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputLocation" name="location" placeholder="location of school" value="<?php echo (!empty($meta->location)) ? $meta->location : '' ?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputExperience" class="col-sm-2 control-label">Experience</label>

                    <div class="col-sm-10">
                      <textarea class="form-control" id="inputExperience" name="experience" placeholder="Experience" ><?php echo (!empty($meta->experience)) ? $meta->experience : '' ?></textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputSkills" class="col-sm-2 control-label">Skills (separate by comma)</label>

                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputSkills" name="skills" placeholder="Skills" value="<?php echo (!empty($meta->skills)) ? $meta->skills : '' ?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputPhoto" class="col-sm-2 control-label">Photo</label>

                    <div class="col-sm-10">
                      <input type="file" class="form-control" id="inputPhoto" name="photo">
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-danger">Update</button>
                    </div>
                  </div>
                </form>
              </div>
              <!-- /.tab-pane -->
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
	$(document).ready(function() {
		
	})

</script> 
<?php 
	require_once 'includes/content/footer.php';
?>