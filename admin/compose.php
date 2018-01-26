<?php
	require_once 'includes/content/header.php';
  $reply = '';
  $forward = '';
  if($Qstring) {
    $arr = explode('=', $Qstring);
    if(count($arr) >= 2) {
      $id = decode($arr[1]);
      if($arr[0] == 'forward') {
        $forward = $messageObj->get(array('id', '=', $id),'message');
        if(empty($forward)) {
          Session::flash('home', "Please select a valid mail");
          Redirect::to('mailbox');
        }
      } elseif($arr[0] == 'reply') {
        $reply = $messageObj->get(array('id', '=', $id),"`from`");
        if(empty($reply)) {
          Session::flash('home', "Please select a valid mail");
          Redirect::to('mailbox');
        }
      } 
    }else {
      Session::flash('home', "Please select a valid mail");
      Redirect::to('mailbox');
    }
  }

  $staffs = $user->getStaffs(array('id','!=', $user->data()->id));
  if(Input::get('forward')) {
    $id = decode(Input::get('forward'));
    $forward = $messageObj->get(array('id', '=', $id),'message');
    if(empty($forward)) {
      Session::flash('home', "Please select a valid mail");
      Redirect::to('mailbox');
    }
  }
  $errors = array();
  $pass = true;
  $park = $parkObj->get();
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
            $_POST['location'] = '';    //necessary to prevent location from being available in the put method of the message class
              if(strstr($value,'all')) {
              $_POST['location'] = explode('--',$value)[1];
              $message->put();
            } else{
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
        Mailbox
        <small><?php echo ($newMail == 0) ? 'No' : $newMail ?> new messages</small>
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
        <li class="active">Mailbox</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-md-3">
          <a href="mailbox" class="btn btn-primary btn-block margin-bottom">Back to Inbox</a>

          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Folders</h3>

              <div class="box-tools">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="box-body no-padding">
              <ul class="nav nav-pills nav-stacked">
                <li><a href="mailbox"><i class="fa fa-inbox"></i> Inbox
                  <span class="label label-primary pull-right"><?php echo ($newMail == 0) ? '' : $newMail ?></span></a></li>
                <li><a href="sentmail"><i class="fa fa-envelope-o"></i> Sent</a></li>
                <li><a href="trash"><i class="fa fa-trash-o"></i> Trash <span class="label label-warning pull-right"><?php echo (count($trash) == 0) ? '' : count($trash) ?></span></a></li>
              </ul>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Compose New Message</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <form action="" method="post" name="newMail" id="newMail">
                <?php if(!empty($reply[0])) {?>
                    <input type="hidden" name="recipient[]" value="<?php echo $reply[0]->from; ?>" />
                    <?php }?>
                <div class="form-group">
                  <label>Recipients</label>
                  <select class="form-control select2" multiple="multiple" name="recipient[]" data-placeholder="Select recipient(s)" style="width: 100%;" <?php
                    if(!empty($reply[0]))  echo "disabled";?>>
                  <?php
                  if($user->checkPermission(array('admin','manager'))) {
                    foreach ($park as $key => $value) { ?>
                      <option value="<?php echo "all--".$value->id?>"><?php echo "All in ".$value->park; ?></option>
                    <?php } }
                      foreach ($staffs as $value) { ?>
                        <option value="<?php echo $value->id;?>" <?php
                          if(!empty($reply[0])) {
                            if($reply[0]->from == $value->id) echo "selected";
                          }
                        ?>><?php echo str_pad($value->name,20,'-');?>
                          <span class="pull-right"><?php echo $user->getTitle($value->groups)." in ".$parkObj->get($value->location, 'park')->park; ?></span></option>
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
                  <input class="form-control" placeholder="Subject:" name="subject" value="<?php echo escape(Input::get('subject')) ?>" required>
                </div>
                <div class="form-group">
                  <textarea id="compose-textarea" name="message" class="form-control" style="height: 300px" required>
                    <?php echo escape(Input::get('message')); if(!Input::exists() && !empty($forward[0])) echo $forward[0]->message;?>
                  </textarea>
                </div>
                <div class="form-group">
                  <div class="btn btn-default btn-file">
                    <i class="fa fa-paperclip"></i> Attachment (no action yet)
                    <input type="file" name="attachment">
                  </div>
                  <p class="help-block">Max. 32MB</p>
                </div>
              </form>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
              <div class="pull-right">
                <button type="submit" form="newMail" name="sendEmail" value="send" class="btn btn-primary"><i class="fa fa-envelope-o"></i> Send</button>
              </div>
              <button type="reset" form="newMail" class="btn btn-default"><i class="fa fa-times"></i> Discard</button>
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /. box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
<?php
	require_once 'includes/content/footer.php';
?>
