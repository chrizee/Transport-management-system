<?php
	require_once 'includes/content/header.php';
	
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
                <li class="active"><a href="trash.php"><i class="fa fa-trash-o"></i> Trash <span class="label label-warning pull-right"><?php echo (count($trash) == 0) ? '' : count($trash) ?></span></a></li>
              </ul>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /. box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="box box-danger">
            <div class="box-header with-border">
              <h3 class="box-title">Trash</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body no-padding">
              <?php
                if(!empty($trash)) {
              ?>
              <div class="mailbox-controls">
                <!-- Check all button -->
                <button type="button" title="mark all" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                </button>
                <div class="btn-group">
                  <button type="button" title="restore back to inbox" class="btn btn-default btn-sm"><i class="fa fa-reply"></i></button>
                </div>
                <!-- /.btn-group -->
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>"><button type="button" title="refresh" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button></a>
                <p class="mail-error text-center text-danger"></p>
                <!--<div class="pull-right">
                  1-50/200
                  <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                  </div>
                </div>-->
                <!-- /.pull-right -->
              </div>
              <div class=" mailbox-messages">
                <table class="table table-hover table-striped datatable">
                  <thead>
                    <tr>
                      <td></td>
                      <td>Sender</td>
                      <td>Subject</td>
                      <td>time</td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    foreach ($trash as $key => $value) {
                    ?>
                  <tr>
                    <td><input type="checkbox" name="markedMails[]" value="<?php echo encode($value->id)?>"></td>
                    <td class="mailbox-name"><a href="read-mail.php?mail=<?php echo encode($value->id);?>"><?php $sender = new User($value->from); echo $sender->data()->name;?></a></td>
                    <td class="mailbox-subject"><a class="<?php echo ($value->status == Config::get('message/not_read')) ?'text-success' : 'text-default text-sm';?>" href="read-mail.php?mail=<?php echo encode($value->id);?>"><b><?php echo $value->subject ?></b> - <?php echo substr(strip_tags($value->message),0,30) . "..."; ?>
                    </td></a>
                    <td class="mailbox-date"><a href="read-mail.php?mail=<?php echo encode($value->id);?>"><?php echo $messageObj->date($value->date)." ago"; ?></td>
                  </tr>
                <?php }?>

                  </tbody>
                </table>
                <!-- /.table -->
              </div>
              <!-- /.mail-box-messages -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer no-padding">
              <div class="mailbox-controls">
                <!-- Check all button -->
                <button type="button" title="mark all" class="btn btn-default btn-sm checkbox-toggle"><i class="fa fa-square-o"></i>
                </button>
                <div class="btn-group">
                  <button type="button" title="restore back to inbox" class="btn btn-default btn-sm"><i class="fa fa-reply"></i></button>
                </div>
                <!-- /.btn-group -->
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>"><button type="button" title="refresh" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i></button></a>
                <!--<div class="pull-right">
                  1-50/200
                  <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-left"></i></button>
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-right"></i></button>
                  </div>
                </div>-->
                <!-- /.pull-right -->
              </div>
            </div>
            <?php } else { ?>
              <p class="text-center">No message in trash.</p>
            <?php }?>
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
<script src="plugins/iCheck/icheck.min.js"></script>
<script>
  $(document).ready(function () {
    //Enable iCheck plugin for checkboxes
    //iCheck for checkbox and radio inputs
    $('.mailbox-messages input[type="checkbox"]').iCheck({
      checkboxClass: 'icheckbox_flat-blue',
      radioClass: 'iradio_flat-blue'
    });

    //Enable check and uncheck all functionality
    $(".checkbox-toggle").click(function () {
      var clicks = $(this).data('clicks');
      if (clicks) {
        //Uncheck all checkboxes
        $(".mailbox-messages input[type='checkbox']").iCheck("uncheck");
        $(".fa", this).removeClass("fa-check-square-o").addClass('fa-square-o');
      } else {
        //Check all checkboxes
        $(".mailbox-messages input[type='checkbox']").iCheck("check");
        $(".fa", this).removeClass("fa-square-o").addClass('fa-check-square-o');
      }
      $(this).data("clicks", !clicks);
    });

    $(document).on('click', 'button[title^=restore]', function(e) {
      var $clicked = [];
      $('input[name^=markedMails]').filter(':checked').each(function(i,val) {
        $clicked.push($(this).val());
      });
      if($clicked.length > 0) {
        $('p.mail-error').fadeOut();
        $.post('_mail.php', {id: $clicked, action: 'restore'}, function($result) {
          if($result == 1) {
            $('p.mail-error').text('Moved back to inbox').fadeOut('1000');
            $('input[name^=markedMails]').filter(':checked').each(function(i,val) {
              if($.inArray($(this).val(),$clicked) != -1 ) {
                $(this).parents('tr').fadeOut('slow').remove();
              }
            });
            //$('button[title=refresh]').click();
          }
        });
      } else { 
        $('p.mail-error').text('Select at least one message');
      }
      });
  });

</script>
<?php
	require_once 'includes/content/footer.php';
?>
