<?php
//Created with Script Smart (http://script-smart.com)
//
//ADJUSTED FOR USE OUTSIDE OF SCRIPTSMART FOR WARRIORS
//
//NOTE: ALL VARIABLES NEEDED TO CHANGE START ON LINE 118
$HTTP_POST_VARS = $_POST;
  function tep_sanitize_string($string) {
    $string = ereg_replace(' +', ' ', trim($string));

    return preg_replace("/[<>]/", '_', $string);
  }

  function tep_db_prepare_input($string) {
    if (is_string($string)) {
      return trim(tep_sanitize_string(stripslashes($string)));
    } elseif (is_array($string)) {
      reset($string);
      while (list($key, $value) = each($string)) {
        $string[$key] = tep_db_prepare_input($value);
      }
      return $string;
    } else {
      return $string;
    }
  }


/* name: input_check_mailinj()
 * sample usage: foreach($_POST as $value) input_check_mailinj($value);
 */

function input_check_mailinj($value, $sendto)
{
   # mail adress(ess) for reports...
   $report_to = $sendto;

   # array holding strings to check...
   $suspicious_str = array
   (
	   "content-type:"
	   ,"charset="
	   ,"mime-version:"
	   ,"multipart/mixed"
	   ,"bcc:"
   );

   // remove added slashes from $value...
   $value = stripslashes($value);

   //SETUP REPLY EMAIL ADDRESS
   $fifth = '-f ' . $report_to;

   //SETUP THE FROM ADDRESS
   $headers  = "From: <" . $report_to . ">\n";
   $headers .= "MIME-Version: 1.0\n";
   $headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";


   foreach($suspicious_str as $suspect)
   {
	   # checks if $value contains $suspect...
	   if(eregi($suspect, strtolower($value)))
	   {
		   // replace this with your own get_ip function...
		   $ip = (empty($_SERVER['REMOTE_ADDR'])) ? 'empty'
			   : $_SERVER['REMOTE_ADDR'];
		   $rf = (empty($_SERVER['HTTP_REFERER'])) ? 'empty'
			   : $_SERVER['HTTP_REFERER'];
		   $ua = (empty($_SERVER['HTTP_USER_AGENT'])) ? 'empty'
			   : $_SERVER['HTTP_USER_AGENT'];
		   $ru = (empty($_SERVER['REQUEST_URI'])) ? 'empty'
			   : $_SERVER['REQUEST_URI'];
		   $rm = (empty($_SERVER['REQUEST_METHOD'])) ? 'empty'
			   : $_SERVER['REQUEST_METHOD'];

		   # if so, file a report...
		   if(isset($report_to) && !empty($report_to))
		   {
			   @mail
			   (
					 $report_to
				   ,"[ABUSE] mailinjection @ " .
				   $_SERVER['HTTP_HOST'] . " by " . $ip
				   ,"Stopped possible mail-injection @ " .
				   $_SERVER['HTTP_HOST'] . " by " . $ip .
				   " (" . date('d/m/Y H:i:s') . ")\r\n\r\n" .
					 "*** IP/HOST\r\n" . $ip . "\r\n\r\n" .
					 "*** USER AGENT\r\n" . $ua . "\r\n\r\n" .
					 "*** REFERER\r\n" . $rf . "\r\n\r\n" .
					 "*** REQUEST URI\r\n" . $ru . "\r\n\r\n" .
					 "*** REQUEST METHOD\r\n" . $rm . "\r\n\r\n" .
					 "*** SUSPECT\r\n--\r\n" . $value . "\r\n--"
					 , $headers, $fifth
			   );
		   }

		   # ... and kill the script.
		   die
		   (
			   '<p>Script processing cancelled: string
			   (`<em>'.$value.'</em>`) contains text portions that
			   are potentially harmful to this server. <em>Your input
			   has not been sent!</em> Please try
			   rephrasing your input.</p>'
		   );
	   }
   }
}


//Variables straight from the form
$formfriendsname = tep_db_prepare_input($HTTP_POST_VARS['friendsname']);
$formfriendsemail = tep_db_prepare_input($HTTP_POST_VARS['friendsemail']);
$formusersname = tep_db_prepare_input($HTTP_POST_VARS['usersname']);
$formusersemail = tep_db_prepare_input($HTTP_POST_VARS['usersemail']);

//Variables usually set from Script Smart
//*******************
// EDIT THESE VARIABLES TO GET THE SCRIPT TO THE WAY YOU WANT IT
//
// "KEEP TO THE CODE" and you won't get errors.

//Enter your own name here in between the quotes
$ownersname = tep_db_prepare_input("Prasad Konde");

//Enter your own email here in between the quotes
$ownersemail = tep_db_prepare_input("prasad@sixsteps.org.in");

//Enter an email subject line that you would like on the emails that come to you after each use of the form
$ownerssubject = tep_db_prepare_input("Rtc is using");

//Enter an email subject line that you want the sender to get when they fill the form out
$usersemailsubject = tep_db_prepare_input("Thank you for spreading the word about script-smart.com");

//Enter an email message that you want the sender to get when they fill the form out
//NOTE: \r\n in your text emails equals a hard return (or enter) so \r\n\r\n equals 2 of them :)

$usersemailtext =	"Hi =usersname=,\r\n\r\n" .
					"Thank you for telling a friend about my site.\r\n\r\n" .
					"I appreciate it, and in return I'd like to give you a special bonus gift.\r\n\r\n" .
					"You can download it here http://script-smart.com\r\n\r\n" .
					"Best wishes,\r\n" .
					"=ownersname=";

//Enter an email subject line that you want the friend to get when they fill the form out
$friendsemailsubject = tep_db_prepare_input("Check This Private Broadcast");

//Enter an email message that you want the friend to get when they fill the form out
//NOTE: \r\n in your text emails equals a hard return (or enter) so \r\n\r\n equals 2 of them :)

$friendsemailtext =	"Hi =friendsname=,\r\n\r\n" .
					"Check out this great site that I just found.\r\n\r\n" .
					"http://script-smart.com\r\n\r\n" .
					"It's got scripts that I know you can use on your \r\n" .
					"website to make you more money from your sales.\r\n\r\n" .
					"Cheers\r\n" .
					"=usersname=";

//Enter in the thankyou URL that the form will direct the sender to after the form is processed.
$thankyouURL = "thankyou.html";

//END OF VARIABLES
//*************************


//CHECK TO SEE IF THE FORM WAS SUBMITTED
if (isset($formusersemail)) {

	//CHECK FOR MAIL INJECTION SCAMMERS
	foreach($_POST as $value) input_check_mailinj($value, $ownersemail);

	//IF OK IT WILL RUN THE MAIL SCRIPT BELOW

	//SETUP REPLY EMAIL ADDRESS
	$fifth = '-f ' . $ownersemail;

	//SETUP THE FROM ADDRESS
	$headers  = "From: \"" . $formusersname . "\" <" . $formusersemail . ">\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";

	   $ip = (empty($_SERVER['REMOTE_ADDR'])) ? 'empty'
		   : $_SERVER['REMOTE_ADDR'];
	   $rf = (empty($_SERVER['HTTP_REFERER'])) ? 'empty'
		   : $_SERVER['HTTP_REFERER'];
	   $ua = (empty($_SERVER['HTTP_USER_AGENT'])) ? 'empty'
		   : $_SERVER['HTTP_USER_AGENT'];
	   $ru = (empty($_SERVER['REQUEST_URI'])) ? 'empty'
		   : $_SERVER['REQUEST_URI'];
	   $rm = (empty($_SERVER['REQUEST_METHOD'])) ? 'empty'
		   : $_SERVER['REQUEST_METHOD'];

//Create and Send a message to the owner that the Tell A Friend was triggered
	$ownermessage = "Users Name = " . $formusersname . "\n\n";
	$ownermessage .= "Users Email = " . $formusersemail . "\n\n";
	$ownermessage .= "Friend Name = " . $formfriendsname . "\n\n";
	$ownermessage .= "Friend Email = " . $formfriendsemail . "\n\n";
	$ownermessage .= "*** IP/HOST\r\n" . $ip . "\r\n\r\n" .
				 "*** USER AGENT\r\n" . $ua . "\r\n\r\n" .
				 "*** REFERER\r\n" . $rf . "\r\n\r\n" .
				 "*** REQUEST URI\r\n" . $ru . "\r\n\r\n" .
				 "*** REQUEST METHOD\r\n" . $rm . "\r\n\r\n";

	mail($ownersemail,$ownerssubject,$ownermessage,$headers,$fifth);
//-----------

//Create and Send a message to the user that triggered the Tell A Friend
	//SETUP THE FROM ADDRESS
	$headers  = "From: \"" . $ownersname . "\" <" . $ownersemail . ">\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";

//TODO : needs to parse the usersemailtext with names etc
	$usersmessage = str_replace("=usersname=", $formusersname, $usersemailtext);
	$usersmessage = str_replace("=friendsname=", $formfriendsname, $usersmessage);
	$usersmessage = str_replace("=ownersname=", $ownersname, $usersmessage);

	mail($formusersname . ' <' . $formusersemail . '>',$usersemailsubject,$usersmessage,$headers,$fifth);
//-----------

//Create and Send a message to the Friend listed in the Tell A Friend
	//SETUP THE FROM ADDRESS
	$headers  = "From: \"" . $formusersname . "\" <" . $formusersemail . ">\n";
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";

//TODO: needs to parse the usersemailtext with names etc
	$friendsmessage = str_replace("=usersname=", $formusersname, $friendsemailtext);
	$friendsmessage = str_replace("=friendsname=", $formfriendsname, $friendsmessage);
	$friendsmessage = str_replace("=ownersname=", $ownersname, $friendsmessage);

	mail($formfriendsname . ' <' . $formfriendsemail . '>',$friendsemailsubject,$friendsmessage,$headers,$fifth);
//-----------

}

?>


<?php

//IF FORM WAS SUBMITTED THEN THANKYOU
if (isset($formusersemail)) {

?>
	<script language="JavaScript" type="text/javascript">
		window.location.href = '<?php echo $thankyouURL; ?>';
	</script>

<?php

}

?>

