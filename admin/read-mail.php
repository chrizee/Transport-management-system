<?php
	require_once 'includes/content/header.php';
  if(empty(Input::get('mail'))) {
    Session::flash('home', "Please select a valid mail");
    Redirect::to('mailbox.php');
  }
	$id = decode(Input::get('mail'));
  $message = $messageObj->get(array('id', '=', $id));
  if(empty($message)) {
    Session::flash('home', "Please select a valid mail");
    Redirect::to('mailbox.php');
  }
  $message = $message[0];
  //change status only when its not read and you are  not the sender
  if($message->status == Config::get('message/not_read') && $message->from != $user->data()->id) {
    try {
      $messageObj->update($id,array('status' => Config::get('message/read')));
    } catch (Exception $e) {
      die($e->getMessage());
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
          <a href="compose.php" class="btn btn-primary btn-block margin-bottom">Compose</a>

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
                <li><a href="mailbox.php"><i class="fa fa-inbox"></i> Inbox
                  <span class="label label-primary pull-right"><?php echo ($newMail == 0) ? '' : $newMail ?></span></a></li>
                <li><a href="sentmail.php"><i class="fa fa-envelope-o"></i> Sent</a></li>
                </li>
                <li><a href="trash.php"><i class="fa fa-trash-o"></i> Trash</a></li>
              </ul>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Read Mail</h3>

              <div class="box-tools pull-right">
                <a href="#" class="btn btn-box-tool" data-toggle="tooltip" title="Previous"><i class="fa fa-chevron-left"></i></a>
                <a href="#" class="btn btn-box-tool" data-toggle="tooltip" title="Next"><i class="fa fa-chevron-right"></i></a>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
              <div class="mailbox-read-info">
                <h3><?php echo escape($message->subject) ?></h3>
                <?php if($message->recipient == $user->data()->id) {?>
                <h5>From: <?php $staff = new User($message->from); echo $staff->data()->name;?>
                  <span class="mailbox-read-time pull-right"><?php $date = new dateTime($message->date); echo $date->format('d M. Y h:i a')?></span></h5>
                <?php } if($message->from == $user->data()->id) { ?>
                <h5>To: <?php $staff = new User($message->recipient); echo $staff->data()->name;?>
                  <span class="mailbox-read-time pull-right"><?php $date = new dateTime($message->date); echo $date->format('d M. Y h:i a')?></span></h5>
                <?php }?>
                <p class="mail-error text text-center text-danger"></p>
              </div>
              <!-- /.mailbox-read-info -->
              <?php
                if($message->status != Config::get('message/deleted')) {
              ?>
              <div class="mailbox-controls with-border text-center">
                <div class="btn-group">
                <?php if($message->from != $user->data()->id) { ?>
                  <button type="button" title="Delete" class="delete btn btn-default btn-sm" data-toggle="tooltip" data-container="body">
                    <i class="fa fa-trash-o"></i></button>
                  <button type="button" title="Reply" class="reply btn btn-default btn-sm" data-toggle="tooltip" data-container="body">
                    <i class="fa fa-reply"></i></button>
                    <?php } ?>
                  <button type="button" title="Forward" class="forward btn btn-default btn-sm" data-toggle="tooltip" data-container="body">
                    <i class="fa fa-share"></i></button>
                </div>
                <!-- /.btn-group -->
              </div>
              <?php }?>
              <!-- /.mailbox-controls -->
              <div class="mailbox-read-message">
                <?php echo $message->message; ?>
              </div>
              <!-- /.mailbox-read-message -->
            </div>
            <!-- /.box-body -->
            <?php
              if($message->status != Config::get('message/deleted') && $message->from != $user->data()->id) {
            ?>
            <div class="box-footer">
              <div class="pull-right">
                <button type="button" title="Reply" class="btn btn-default"><i class="fa fa-reply"></i> Reply</button>
                <button type="button" title="Forward" class="btn btn-default"><i class="fa fa-share"></i> Forward</button>
              </div>
              <button type="button" title="Delete" class="btn btn-default"><i class="fa fa-trash-o"></i> Delete</button>
            </div>
            <?php } ?>
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
<script>
  $(document).ready(function () {
    var $clicked = ["<?php echo Input::get('mail') ?>"];
    $(document).on('click', 'button[title=Delete], button.delete', function(e) {
      if($clicked.length > 0) {
        $('p.mail-error').fadeOut();
        $.post('_mail.php', {id: $clicked, action: 'delete'}, function($result) {
          if($result == 1) {
            $('p.mail-error').text('Deleted').fadeOut('1000');
            window.location = "mailbox.php";
          }
        });
      } else { 
        $('p.mail-error').text('Select at least one message');
      }
      }).on('click', 'button[title=Reply], button.reply', function(e) {
      if($clicked.length == 1) {
        window.location = "compose.php?reply="+$clicked[0]; 
      }else {
        e.preventDefault();
       $('p.mail-error').text('Select one message to reply'); 
      }
      }).on('click', 'button[title=Forward], button.forward', function(e) {
      if($clicked.length == 1) {
        window.location = "compose.php?forward="+$clicked[0]; 
      }else {
        e.preventDefault();
       $('p.mail-error').text('Select one message to forward1'); 
      }
      });




      
    //Handle starring for glyphicon and font awesome
    $(".mailbox-star").click(function (e) {
      e.preventDefault();
      //detect type
      var $this = $(this).find("a > i");
      var glyph = $this.hasClass("glyphicon");
      var fa = $this.hasClass("fa");

      //Switch states
      if (glyph) {
        $this.toggleClass("glyphicon-star");
        $this.toggleClass("glyphicon-star-empty");
      }

      if (fa) {
        $this.toggleClass("fa-star");
        $this.toggleClass("fa-star-o");
      }
    });
  });
</script>
</script>
<?php
	require_once 'includes/content/footer.php';
?>
