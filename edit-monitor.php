<?php
/**
 * Postfix Admin
 *
 * LICENSE
 * This source file is subject to the GPL license that is bundled with
 * this package in the file LICENSE.TXT.
 *
 * Further details on the project are available at :
 *     http://www.postfixadmin.com or http://postfixadmin.sf.net
 *
 * @version $Id: edit-monitor.php 1324 2012-11-20 22:15:23Z amir_lohi $
 * @license GNU GPL v2 or later.
 *
 * File: edit-monitor.php
 * Responsible for allowing admin to setup email monitor (send/recieve_bcc)
 *
 * Template File: edit-monitor.php
 *
 * Template Variables:
 *
 * tUseremail
 * tMonitor_email
 *
 * Form POST \ GET Variables:
 *
 * fUsername
 * fDomain
 * fCanceltarget
 * fChange
 * fBack
 * fActive
 */

require_once('common.php');

if($CONF['monitor'] == 'YES') {
   header("Location: " . $CONF['postfix_admin_url'] . "/list-virtual.php");
   exit(0);
}

$SESSID_USERNAME = authentication_get_username();
$tmp = preg_split ('/@/', $SESSID_USERNAME);
$USERID_DOMAIN = $tmp[1];
$tMessage = "";
$error = 0;

// only allow admins to change someone else's 'stuff'
if(authentication_has_role('admin')) {
   if (isset($_GET['username'])) $fUsername = escape_string ($_GET['username']);
   if (isset($_GET['domain'])) $fDomain = escape_string ($_GET['domain']);
}
else {
   $fUsername = $SESSID_USERNAME;
   $fDomain = $USERID_DOMAIN;
}
$domain = "";

$fCanceltarget = $CONF['postfix_admin_url'] . "/list-virtual.php?domain=" . urlencode($fDomain);

if ($_SERVER['REQUEST_METHOD'] == "GET")
{

   $result = db_query("SELECT * FROM $table_monitor WHERE email='$fUsername'");
   if ($result['rows'] == 1)
   {
      $row = db_array($result['result']);
      $tMonitor_email = $row['monitor_email'];
   }

   $tUseremail = $fUsername;
   $tDomain = $fDomain;

}

if ($_SERVER['REQUEST_METHOD'] == "POST")
{

   $tMonitor_email   = safepost('fMonitor_email');
   $fMonitor_email   = escape_string ($tMonitor_email);
   $fChange    = escape_string (safepost('fChange'));
   $fBack      = escape_string (safepost('fBack'));

   list (/*NULL*/, $domain) = explode('@', $fMonitor_email);

   if(authentication_has_role('admin') && isset($_GET['domain'])) {
      $fDomain = escape_string ($_GET['domain']);
   }
   else {
      $fDomain = $USERID_DOMAIN;
   }
   if(authentication_has_role('admin') && isset ($_GET['username'])) {
      $fUsername = escape_string($_GET['username']);
   }
   else {
      $fUsername = authentication_get_username();
   }

   $tUseremail = $fUsername;

  if (strtolower($domain) !== strtolower($fDomain)) {
    $tMessage = $PALANG['pMonitor_error_domain'];
    $error = 1;
  }

  if (strtolower($fUsername) === strtolower($fMonitor_email)) {
    $tMessage = $PALANG['pMonitor_error_identical'];
    $error = 1;
  }

  // Make sure email and and monitor email exist and are active.
  if ($error == 0 && !empty($fMonitor_email)) {
    $Active = db_get_boolean(True);
    $result = db_query("SELECT * FROM $table_alias WHERE address = '$fMonitor_email' AND active = '$Active'");
    if ($result['rows'] != 1) {
      $tMessage = $PALANG['pMonitor_error_monitor'];
      $error = 1;
    }
  }


  if ($error == 0) {
    if (!empty($fMonitor_email) && !empty ($fChange)) {
      //Set the mail data for $fUsername
      $Active = db_get_boolean(True);
      $notActive = db_get_boolean(False);

      // I don't think we need to care if the monitor entry is inactive or active.. as long as we don't try and
      // insert a duplicate
      $result = db_query("SELECT * FROM $table_monitor WHERE email = '$fUsername'");
      if($result['rows'] == 1) {
          $result = db_query("UPDATE $table_monitor SET active = '$Active', monitor_email = '$fMonitor_email', created = NOW(), created_by = '$SESSID_USERNAME' WHERE email = '$fUsername'");
      }
      else {
          $result = db_query ("INSERT INTO $table_monitor (email,monitor_email,domain,created,created_by,active) VALUES ('$fUsername','$fMonitor_email','$fDomain',NOW(),'$SESSID_USERNAME','$Active')");
      }

      if ($result['rows'] != 1)
      {
         $PALANG['pCreate_alias_domain_error3'];
         $error = 1;
      }
      db_log($SESSID_USERNAME, $domain, 'edit_monitor', "$fUsername -> $fMonitor_email");
    } else if ((!empty ($fBack) || !empty ($fChange))) {
      //if change, remove old one, then perhaps set new one
      //if we find an existing monitor entry, disable it
      $result = db_query("SELECT * FROM $table_monitor WHERE email='$fUsername'");
      if ($result['rows'] == 1) {
         $db_false = db_get_boolean(false);
         // retain monitor email
         $result = db_query ("UPDATE $table_monitor SET active = '$db_false' WHERE email='$fUsername'");

         db_log($SESSID_USERNAME, $domain, 'edit_monitor', "$fUsername -> inactive");
      }
    } else {
      $error = 1;
      $tMessage = $PALANG['pMonitor_result_error'];
    }
  }
}

if($error == 0) {
   if(!empty ($fBack)) {
      $tMessage = $PALANG['pMonitor_result_removed'];
   }

   if(!empty($fChange) && !empty($fMonitor_email)) {
      $tMessage = $PALANG['pMonitor_result_enabled'];
   }
}

include ("templates/header.php");
include ("templates/menu.php");
include ("templates/edit-monitor.php");
include ("templates/footer.php");
/* vim: set expandtab softtabstop=3 tabstop=3 shiftwidth=3: */
?>
