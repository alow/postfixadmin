<?php if( !defined('POSTFIXADMIN') ) die( "This file cannot be used standalone." ); ?>
<script type="text/javascript">
function newLocation()
{
    window.location= "<?php echo $fCanceltarget; ?>"
}
</script>
<div id="edit_form">

<form name="edit-monitor" method="post" action=''>
<table>
   <tr>
      <td colspan="3"><h3><?php print $PALANG['pEdit_monitor_welcome']; ?></h3></td>
   </tr>

   <tr>
      <td><?php print $PALANG['pEdit_login_username'] . ":"; ?></td>
      <td><?php print $tUseremail; ?></td>
      <td>&nbsp;</td>
   </tr>

   <tr>
      <td><?php print $PALANG['pEdit_monitor_email'] . ":"; ?></td>
      <td><input class="flat" type="text" name="fMonitor_email" value="<?php print htmlspecialchars ($tMonitor_email,ENT_QUOTES); ?>" /></td>
      <td><?php print $pEdit_monitor_email_text; ?></td>
   </tr>

   <tr>
      <td colspan="3"><br /></td>
   </tr>

   <tr>
      <td colspan="3" class="hlp_center">
        <input class="button" type="submit" name="fChange" value="<?php print $PALANG['pEdit_monitor_set']; ?>" />
        <input class="button" type="submit" name="fBack" value="<?php print $PALANG['pEdit_monitor_remove']; ?>" />
        <input class="button" type="button" name="fCancel" value="<?php print $PALANG['exit']; ?>" onclick="newLocation()" />
      </td>
   </tr>

   <tr>
      <td colspan="3" class="standout"><?php print $tMessage; ?></td>
   </tr>
</table>
</form>
</div>
