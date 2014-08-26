<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "prasad@sixsteps.org.in" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "f4169b" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'F08A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGVqRxQIaGEMYHR2mOqCIsbayNgQEBKCIiTQ6Ojo6iCC5LzRq2sqs0JVZ05Dch6YOLubaEBgagmFHIJo6kFvQ9YLczIgiNlDhR0WIxX0AQPzMJkNFOjkAAAAASUVORK5CYII=',
			'119E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGUMDkMRYHRgDGB0dHZDViTqwBrA2BDqg60USAztpZdaqqJWZkaFZSO4D2xGCqZcBi3mM2MTQ3RLCGoru5oEKPypCLO4DALG+xKWRc0DIAAAAAElFTkSuQmCC',
			'D1E7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgMYAlhDHUNDkMQCpjAGsAJpEWSxVlYsYgxgsQAk90UtBaLQVSuzkNwHVdfKgKl3ChaxABSxKSAxRgdUN7OGAt2MIjZQ4UdFiMV9AGqQyoSdXvBjAAAAAElFTkSuQmCC',
			'BA7E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgMYAlhDA0MDkMQCpjCGMDQEOiCrC2hlbcUQmyLS6NDoCBMDOyk0atrKrKUrQ7OQ3AdWN4URzTzRUIcAdDERoGmMGHa4NqCKhQaAxVDcPFDhR0WIxX0AVdLMWMBvv2UAAAAASUVORK5CYII=',
			'2215' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2QsRGAIAxFk4INcJ9Q2KcgDdNAwQaRDSxkSrHLoaXemd+9+8m9C/TbZPhTPvFzjBEUhQ3z6ipEJNvj6kuYGFQopLiS9Wt97+1Iyfox6Ej2Zndc4pm5iyqSZX7Q0WPrJ7JIENroB/97MQ9+J4fIykKX54k5AAAAAElFTkSuQmCC',
			'5C28' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QkMYQxlCGaY6IIkFNLA2Ojo6BASgiIk0uDYEOoggiQUGgHgBMHVgJ4VNm7Zq1cqsqVnI7msFqmtlQDEPLDaFEcW8AKCYQwCqmMgUoFscUPWyBjCGsoYGoLh5oMKPihCL+wDsTsyOzH7x7wAAAABJRU5ErkJggg==',
			'D4D7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgMYWllDGUNDkMQCpjBMZW10aBBBFmtlCGVtCEATY3QFiQUguS9qKRCsilqZheS+gFaRVlaQCSh6RUNdQTah2gFSF8CA6pZW1kZHByxuRhEbqPCjIsTiPgC9PM4IT6Ic6gAAAABJRU5ErkJggg==',
			'28D5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGUMDkMREprC2sjY6OiCrC2gVaXRtCEQRY2gFqmsIdHVAdt+0lWFLV0VGRSG7LwCkLqBBBEkvowPIPFQx1gaIHchiIg0gtzgEILsvNBTkZoapDoMg/KgIsbgPAJj1y+vSrwh5AAAAAElFTkSuQmCC',
			'8EFA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA1qRxUSmiDSwNjBMdUASC2gFiwUEYKhjdBBBct/SqKlhS0NXZk1Dch+aOiTzGENDMMVQ1GHTC3YzmthAhR8VIRb3AQB82cqgzm8PMQAAAABJRU5ErkJggg==',
			'F176' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkMZAlhDA6Y6IIkFNDAGAMmAABQxVqBYoIMAihhDAEOjowOy+0KjVkWtWroyNQvJfWB1UxjRzAOKBTA6iKCJMTpgirECMZpbQoFiKG4eqPCjIsTiPgCsdcrQecwXkgAAAABJRU5ErkJggg==',
			'F850' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDHVqRxQIaWFtZGximOqCIiTS6NjAEBKCrm8roIILkvtColWFLMzOzpiG5D6SOoSEQpg5ungMWMdeGAAw7GB0d0NzCGMIQyoDi5oEKPypCLO4DAJvlzWh7if1XAAAAAElFTkSuQmCC',
			'EE06' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkNEQxmmMEx1QBILaBBpYAhlCAhAE2N0dHQQQBNjbQh0QHZfaNTUsKWrIlOzkNwHVYdhHkivCBY70MXQ3YLNzQMVflSEWNwHAFcCzFr/TMSwAAAAAElFTkSuQmCC',
			'B482' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgMYWhlCGaY6IIkFTGGYyujoEBCALAZUxdoQ6CCCoo7RFaiuQQTJfaFRS5euCl21KgrJfQFTRFqB6hpR7GgVDXUFmYpqRysryHZUt4D0BmC6mTE0ZBCEHxUhFvcBAHF6zTSQeveEAAAAAElFTkSuQmCC',
			'892B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMdkMREprC2Mjo6OgQgiQW0ijS6NgQ6iKCoE2l0AIoFILlvadTSpVkrM0OzkNwnMoUx0KGVEc08hkaHKYwo5gW0sjQ6BDCi2QF0iwOqXpCbWUMDUdw8UOFHRYjFfQA5NctXKA1cbAAAAABJRU5ErkJggg==',
			'6EB8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGaY6IImJTBFpYG10CAhAEgtoAYo1BDqIIIs1oKgDOykyamrY0tBVU7OQ3BeCzbxWLOZhEcPmFmxuHqjwoyLE4j4APlTNEHAlehYAAAAASUVORK5CYII=',
			'C5D7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WENEQ1lDGUNDkMREWkUaWBsdGkSQxAIagWINAahiDSIhILEAJPdFrZq6dOmqqJVZSO4Dyje6NgS0MqDoBYtNYUC1AyQWwIDiFtZW1kZHB1Q3M4YA3YwiNlDhR0WIxX0A6cnNYSo0DWMAAAAASUVORK5CYII=',
			'15D6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDGaY6IImxOog0sDY6BAQgiYmCxBoCHQRQ9IqEgMSQ3bcya+rSpasiU7OQ3MfowNDo2hCIYh5UDGgqinlYxFhbMdwSwhiC7uaBCj8qQizuAwBAgcn4MXR07QAAAABJRU5ErkJggg==',
			'D5EC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDHaYGIIkFTBFpYG1gCBBBFmsFiTE6sKCKhYDEkN0XtXTq0qWhK7OQ3RfQytDoilCHR0wELIZixxTWVnS3hAYwhqC7eaDCj4oQi/sAYjbMXLtne9kAAAAASUVORK5CYII=',
			'007A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YAlhDA1qRxVgDGEMYGgKmOiCJiUxhBaoJCAhAEgtoFWl0aHR0EEFyX9TSaSuzlq7MmobkPrC6KYwwdQixAMbQEDQ7GB1Q1YHcwtqAKgZ2M5rYQIUfFSEW9wEAuZnKoMokWRYAAAAASUVORK5CYII=',
			'6471' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYWllDA1qRxUSmMExlaAiYiiwW0MIQCiRDUcQaGF0ZGh1gesFOioxaunQVCCK5L2SKSCvDFAYUOwJaRUMdAtDFGFoZHRjQ3dLK2oAqBnZzA0NowCAIPypCLO4DAFYIzC4FEibeAAAAAElFTkSuQmCC',
			'F7CC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNFQx1CHaYGIIkB2Y2ODgEBImhirg2CDiyoYq2sDYwOyO4LjVo1bemqlVnI7gOqC0BSBxVjdMAUYwVCdDtEgKrQ3QLkobl5oMKPihCL+wBskMxDX0JJdgAAAABJRU5ErkJggg==',
			'3216' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7RAMYQximMEx1QBILmMLayhDCEBCArLJVpNExhNFBAFlsCkOjwxRGB2T3rYxatXTVtJWpWcjumwKCjGjmMQQAxRxEUMSAZqGJAd0C0o+iVzRANNQx1AHFzQMVflSEWNwHAH6LyuVogGI7AAAAAElFTkSuQmCC',
			'58D5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDGUMDkMQCGlhbWRsdHRhQxEQaXRsCUcQCA4DqGgJdHZDcFzZtZdjSVZFRUcjuawWpA5qAbHMryDxUsYBWiB3IYiJTQG5xCEB2H2sAyM0MUx0GQfhREWJxHwC62syyTPS6fwAAAABJRU5ErkJggg==',
			'1028' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGaY6IImxOjCGMDo6BAQgiYk6sLayNgQ6iKDoFWl0aAiAqQM7aWXWtJVZK7OmZiG5D6yulQHFPLDYFEY081iBqtDFgG5xQNUrGsIQwBoagOLmgQo/KkIs7gMAkLbIeC6T2cIAAAAASUVORK5CYII=',
			'B9D7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGUNDkMQCprC2sjY6NIggi7WKNLo2BKCKTYGIBSC5LzRq6dLUVVErs5DcFzCFMRCorpUBxTwGkN4pqGIsILEABgy3ODpgcTOK2ECFHxUhFvcBAHd6zo+xCkpDAAAAAElFTkSuQmCC',
			'8B27' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANEQxhCGUNDkMREpoi0Mjo6NIggiQW0ijS6NgSgiIHUAWWAEOG+pVFTw1atzFqZheQ+sDoQRDPPYQrDFAyxAIYABnS3ODA6oLuZNTQQRWygwo+KEIv7ACPLy+CGSyw/AAAAAElFTkSuQmCC',
			'0487' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YWhlCGUNDkMRYAximMjo6NIggiYlMYQhlbQhAEQtoZXQFqQtAcl/U0qVLV4WuWpmF5L6AVpFWoLpWBhS9oqGuDQFTGFDtaAXaEcCA6hagXkcHLG5GERuo8KMixOI+AFkBypBxeBHJAAAAAElFTkSuQmCC',
			'417D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37pjAEsIYGhjogi4UwBjA0BDoEIIkxhrCCxUSQxFiBehkaHWFiYCdNm7YqatXSlVnTkNwXAFI3hRFFb2goUCwAVQzkFkYHTDHWBkYUtzBMYQ0FiqG6eaDCj3oQi/sA6j3IpOtunF4AAAAASUVORK5CYII=',
			'D900' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgMYQximMLQiiwVMYW1lCGWY6oAs1irS6OjoEBCAJubaEOggguS+qKVLl6auisyahuS+gFbGQCR1UDGGRkwxFkw7sLgFm5sHKvyoCLG4DwB3C83/ak/gcwAAAABJRU5ErkJggg==',
			'8CE7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WAMYQ1lDHUNDkMREprA2uoJoJLGAVpEGdDGRKSINrCA5JPctjZq2amnoqpVZSO6DqmtlQDMPKDYFXQxoRwBDA7pbGB2wuBlFbKDCj4oQi/sATUzMCkAW99YAAAAASUVORK5CYII=',
			'2564' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nM3QMQ6AIAyF4TJwg3ofGNxrQhdP8xy4AXoDF04pY1FHjbbbnzT5UqqXAf1pX/F5GZSUIKZxYbgYFtskMzxCto0yJw8qYn3buu9rnWfrE1rGGIO9daE1TJqsBdya9Bb43CxdU3XpbP7qfw/uje8Aly7NZVVAaNAAAAAASUVORK5CYII=',
			'B7AB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QgNEQx2mMIY6IIkFTGFodAhldAhAFmtlaHR0dHQQQVXXytoQCFMHdlJo1KppS1dFhmYhuQ+oLgBJHdQ8RgfW0EBU84CmgdSh2iHSgK43NAAshuLmgQo/KkIs7gMA1YnNlo9KyFgAAAAASUVORK5CYII=',
			'63EA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANYQ1hDHVqRxUSmiLSyNjBMdUASC2hhaHRtYAgIQBZrYACqY3QQQXJfZNSqsKWhK7OmIbkvZAqKOojeVpB5jKEhmGIo6iBuQRWDuNkRRWygwo+KEIv7ABBPyyZsZm4PAAAAAElFTkSuQmCC',
			'42BD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpI37pjCGsIYyhjogi4WwtrI2OjoEIIkxhog0ujYEOoggibFOYWh0BaoTQXLftGmrli4NXZk1Dcl9AVMYprAi1IFhaChDACuaeUC3OGCKsTagu4VhimioK7qbByr8qAexuA8AVSfLlOVLnLEAAAAASUVORK5CYII=',
			'F004' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkMZAhimMDQEIIkFNDCGMIQyNKKKsbYyOjq0ooqJNLo2BEwJQHJfaNS0lamroqKikNwHURfogKk3MDQE0w5sbkETw3TzQIUfFSEW9wEAPybOt9pDDqYAAAAASUVORK5CYII=',
			'05DC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDGaYGIImxBog0sDY6AEmEmMgUoFhDoAMLklhAq0gISAzZfVFLpy5duioyC9l9Aa0Mja4IdTjFgHaAxZDtYA1gbUV3C6MDYwi6mwcq/KgIsbgPAICsy7P5JDY6AAAAAElFTkSuQmCC',
			'421D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpI37pjCGAHGoA7JYCGsrQwijQwCSGGOISKMjUEwESYx1CkOjwxS4GNhJ06atWrpq2sqsaUjuC5jCAISoekNDGQLQxUB8TDFWsHgAiphoqCMQorh5oMKPehCL+wCeUcpGJto4AQAAAABJRU5ErkJggg==',
			'6F92' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WANEQx1CGaY6IImJTBFpYHR0CAhAEgtoEWlgbQh0EEEWawCJgUiE+yKjpoatzIxaFYXkvhCgeQwhAY3IdgS0ioBJBjQxxoaAKQxY3ILqZqDeUMbQkEEQflSEWNwHANENzKIrknJQAAAAAElFTkSuQmCC',
			'E97D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDA0MdkMQCGlhbGRoCHQJQxEQaHYBiIuhijY4wMbCTQqOWLs1aujJrGpL7AhoYAx2mMKLpZWh0CEAXYwGahi7G2srawIjiFrCbGxhR3DxQ4UdFiMV9ACFZzK+JNucNAAAAAElFTkSuQmCC',
			'2174' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nM3QsRGAIAyF4aTIBg7ECK8wDdNAwQacG9hkSkMX1FJPSfddOP6D7HIK/Wle6RMQRFEQbOkMKqjR0GRYi0aNQDV1xL7Nsu2Wc+zzN6hzinc5DWVdY4tvus8tblJmUxU921f/9+Dc9B0DBMsG3JAnYgAAAABJRU5ErkJggg==',
			'90F6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA6Y6IImJTGEMYW1gCAhAEgtoZW1lbWB0EEARE2l0BYohu2/a1GkrU0NXpmYhuY/VFawOxTwGqF4RJDEBqB0iBNwCdnMDA4qbByr8qAixuA8ADTLKZWfZv0MAAAAASUVORK5CYII=',
			'0A8C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGaYGIImxBjCGMDo6BIggiYlMYW1lbQh0YEESC2gVaXR0dHRAdl/U0mkrs0JXZiG7D00dVEw01BVoHgOKHSKNrmh2sAaA9KK6hdFBpNEBzc0DFX5UhFjcBwBgjssS2Kd14gAAAABJRU5ErkJggg==',
			'8274' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nM2QsQ3AIAwE7cIbkH3cpP8CGqYxBRtkBRqmjEsgKRMlfsnF6WWdTP0yRn/KK34CjpJgGFg4pJKhjAw1FPU996ho0QODX8u9eXIe/LznYZ3vEQic4sRYWWl1MbGZCba0L+yr/z2YG78T4tHOF1AvIL8AAAAASUVORK5CYII=',
			'02B3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGUIdkMRYA1hbWRsdHQKQxESmiDS6NgQ0iCCJBbQyNLo2OjQEILkvaumqpUtDVy3NQnIfUN0UVoQ6mFgAK5p5IlMYHdDFgG5pQHcLo4NoqCuamwcq/KgIsbgPADsxzQPU4tIUAAAAAElFTkSuQmCC',
			'2D5A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7WANEQ1hDHVqRxUSmiLSyNjBMdUASC2gVaXRtYAgIQNYNEpvK6CCC7L5p01amZmZmTUN2X4BIo0NDIEwdGAJ1gcRCQ5Dd0gCyA1WdSINIK6OjI4pYaKhoCEMoI4rYQIUfFSEW9wEAxuvLsOBfyfcAAAAASUVORK5CYII=',
			'9CA0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WAMYQxmmMLQii4lMYW10CGWY6oAkFtAq0uDo6BAQgCbG2hDoIILkvmlTp61auioyaxqS+1hdUdRBIEhvKKqYAFDMtSEAxQ6QW4BiKG4BuZkVaPtgCD8qQizuAwBDr80zHpsF8wAAAABJRU5ErkJggg==',
			'0DBD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGUMdkMRYA0RaWRsdHQKQxESmiDS6NgQ6iCCJBbQCxYDqRJDcF7V02srU0JVZ05Dch6YOIYZmHjY7sLkFm5sHKvyoCLG4DwBTksxWAQKB9QAAAABJRU5ErkJggg==',
			'FB01' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkNFQximMLQiiwU0iLQyhDJMRRNrdHR0CEVXx9oQANMLdlJo1NSwpauiliK7D00d3DxXLGJAO7C5BU0M7ObQgEEQflSEWNwHAPjkzb6TI9dUAAAAAElFTkSuQmCC',
			'DD84' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgNEQxhCGRoCkMQCpoi0Mjo6NKKItYo0ugJJdDFHR4cpAUjui1o6bWVW6KqoKCT3QdQ5OmCaFxgagmkHNregiGFz80CFHxUhFvcBADsY0ESHFn4IAAAAAElFTkSuQmCC',
			'4557' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpI37poiGsoY6hoYgi4WINLACaREkMUYsYqxTREJYpzI0BCC5b9q0qUuXZmatzEJyX8AUhkaHhoBWZHtDQ8FiU1DdItLo2hAQgCrG2sro6OiAKsYYwhDKiCo2UOFHPYjFfQCdN8ujzMyu6QAAAABJRU5ErkJggg==',
			'8DC8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WANEQxhCHaY6IImJTBFpZXQICAhAEgtoFWl0bRB0EEFVBxRjgKkDO2lp1LSVqatWTc1Cch+aOiTzGFHMw2EHhluwuXmgwo+KEIv7ALDazWV6/EokAAAAAElFTkSuQmCC',
			'F6D8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDGaY6IIkFNLC2sjY6BASgiIk0sjYEOoigijWwNgTA1IGdFBo1LWzpqqipWUjuC2gQbUVSBzfPFdM8LGLY3ILp5oEKPypCLO4DAOFnznUbes0mAAAAAElFTkSuQmCC',
			'CFEC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7WENEQ11DHaYGIImJtIo0sDYwBIggiQU0gsQYHViQxRogYsjui1o1NWxp6MosZPehqcMthsUObG5hDQGKobl5oMKPihCL+wCfY8rnme854gAAAABJRU5ErkJggg==',
			'44EC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpI37pjC0soY6TA1AFgthmMrawBAggiTGGMIQytrA6MCCJMY6hdEVJIbsvmnTli5dGroyC9l9AVNEWpHUgWFoqGioK5oY2C1odkDEUN2C1c0DFX7Ug1jcBwARHMnVhV2ehQAAAABJRU5ErkJggg==',
			'AF07' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB1EQx2mMIaGIImxBog0MIQyNIggiYlMEWlgdHRAEQtoFWlgbQgAQoT7opZODVu6KmplFpL7oOpake0NDQWLTWFAMw9oRwC6GEMoowOG2BRUsYEKPypCLO4DACTkzC65HvrAAAAAAElFTkSuQmCC',
			'9F6C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WANEQx1CGaYGIImJTBFpYHR0CBBBEgtoFWlgbXB0YMEQY3RAdt+0qVPDlk5dmYXsPlZXoDpHRwcUm8F6A1HEBKBiyHZgcwsriIfm5oEKPypCLO4DAGnEyrH5ihKoAAAAAElFTkSuQmCC',
			'00B1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB0YAlhDGVqRxVgDGENYGx2mIouJTGFtZW0ICEUWC2gVaXRtdIDpBTspaum0lamhq5Yiuw9NHUIMSGKxA5tbUMSgbg4NGAThR0WIxX0A5MvMDLdOp0AAAAAASUVORK5CYII=',
			'AC34' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB0YQxlDGRoCkMRYA1gbXRsdGpHFRKaINDg0BLQiiwW0ijQwNDpMCUByX9TSaatWTV0VFYXkPog6RwdkvaGhQLGGwNAQNPOAdjSg2gF2C5oYppsHKvyoCLG4DwCV6NADayvfZgAAAABJRU5ErkJggg==',
			'0636' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YQxhDGaY6IImxBrC2sjY6BAQgiYlMEWlkaAh0EEASC2gVaWBodHRAdl/U0mlhq6auTM1Ccl9Aq2grUB2KeUC9jQ5A80TQ7EAXw+YWbG4eqPCjIsTiPgDSZMwERvT9EAAAAABJRU5ErkJggg==',
			'91F5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA0MDkMREpjAGsDYwOiCrC2hlxSLGABJzdUBy37Spq6KWhq6MikJyH6srSB3QXGSbWzHFBCDmOSCLiUwBqwtAdh/QJaFAsakOgyD8qAixuA8AGa7IK236ursAAAAASUVORK5CYII=',
			'353E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7RANEQxmBMABJLGCKSANro6MDispWESAZiCo2RSSEAaEO7KSVUVOXrpq6MjQL2X1TGBodMMwDiqGb1yqCIRYwhbUV3S2iAYwh6G4eqPCjIsTiPgCGOsrl6IjkygAAAABJRU5ErkJggg==',
			'0BDE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGUMDkMRYA0RaWRsdHZDViUwRaXRtCEQRC2gFqkOIgZ0UtXRq2NJVkaFZSO5DUwcTwzAPmx3Y3ILNzQMVflSEWNwHAGBXysj1c0aJAAAAAElFTkSuQmCC',
			'F8AC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkMZQximMEwNQBILaGBtZQhlCBBBERNpdHR0dGBBU8faEOiA7L7QqJVhS1dFZiG7D00d3DzXUCxiQHWYdgSguYUxBCiG4uaBCj8qQizuAwA3Qc1NzP7pdQAAAABJRU5ErkJggg==',
			'2F0D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WANEQx2mMIY6IImJTBFpYAhldAhAEgtoFWlgdHR0EEHWDRRjbQiEiUHcNG1q2NJVkVnTkN0XgKIODBkdMMVYGzDtEGnAdEtoKFAMzc0DFX5UhFjcBwDxrspfxVQRpgAAAABJRU5ErkJggg==',
			'DCB1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgMYQ1lDGVqRxQKmsDa6NjpMRRFrFWlwbQgIRRdjbXSA6QU7KWrptFVLQ1ctRXYfmjqEGJDEYgc2t6CIQd0cGjAIwo+KEIv7AANIz0zXQBCdAAAAAElFTkSuQmCC',
			'E8BE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAATklEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDGUMDkMQCGlhbWRsdHRhQxEQaXRsC0cRQ1IGdFBq1Mmxp6MrQLCT3EW8eQTtwunmgwo+KEIv7APbfzAwvzTyrAAAAAElFTkSuQmCC',
			'C2D4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nM2QsRHAIAhFsWADs4+NPbnDxmmgcANXsHHKmA5NyuQSfvf+cbwD+mUE/pRX/JAdYwIhw3zBghrUMlKvUahMTOBklYxf7r21nnM2fqOvKHtYdmmwxNMNF3A0i4sMl4khbykuzl/978Hc+B3rGs8oLxm40QAAAABJRU5ErkJggg==',
			'9A5F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHUNDkMREpjCGsDYwOiCrC2hlbcUUE2l0nQoXAztp2tRpK1MzM0OzkNzH6irS6NAQiKKXoVU0FF1MAGQempjIFJFGR0dHFDHWAKB5oahuGajwoyLE4j4AREvJ8SKIe6AAAAAASUVORK5CYII=',
			'87E5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WANEQ11DHUMDkMREpjA0ujYwOiCrC2jFFAOqa2VtYHR1QHLf0qhV05aGroyKQnIfUF0AK4hGMY/RAVOMtQFonoMIih0iQDGGAGT3sQYAxUIdpjoMgvCjIsTiPgC6pMsQIwAb/gAAAABJRU5ErkJggg==',
			'E18F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGUNDkMQCGhgDGB0dHRhQxFgDWBsC0cQYkNWBnRQatSpqVejK0Cwk96Gpg4thM4+AHVA3s4YC3YwiNlDhR0WIxX0ARWjIKoI7ovkAAAAASUVORK5CYII=',
			'0743' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7GB1EQx0aHUIdkMRYAxgaHVodHQKQxESmAMWmOjSIIIkFtDK0MgQ6NAQguS9q6appKzOzlmYhuQ+oLoC1Ea4OKsbowBoagGKeyBTWBqAtKGKsAUBeI6pbGB1AYqhuHqjwoyLE4j4ArRnNRj+Sg4MAAAAASUVORK5CYII=',
			'950A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeUlEQVR4nGNYhQEaGAYTpIn7WANEQxmmMLQii4lMEWlgCGWY6oAkFtAq0sDo6BAQgCoWwtoQ6CCC5L5pU6cuXboqMmsakvtYXRkaXRHqILAVLBYagiQm0CrS6OjoiKJOZAprK0MoI4oYawBjCMMUVLGBCj8qQizuAwA0Q8sy0AB14AAAAABJRU5ErkJggg==',
			'1C48' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB0YQxkaHaY6IImxOrA2OrQ6BAQgiYk6iDQ4THUEksh6gbxAuDqwk1ZmTVu1MjNrahaS+0DqgCaimAcWCw3EMM+hEd0OoE40vaIhmG4eqPCjIsTiPgCthssGlQhi0wAAAABJRU5ErkJggg==',
			'2489' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WAMYWhlCGaY6IImJTGGYyujoEBCAJBYAVMXaEOgggqy7ldGV0dERJgZx07SlS1eFrooKQ3ZfgEgr0LypyHoZHURDXRsCGpDFWIEmsjYEoNghArIFzS2hoZhuHqjwoyLE4j4AMZzKtnVFWDYAAAAASUVORK5CYII=',
			'DF25' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgNEQx1CGUMDkMQCpog0MDo6OiCrC2gVaWBtCMQQY2gIdHVAcl/U0qlhq1ZmRkUhuQ+srpWhQQRd7xQsYgGMDihiILc4MAQguy80AOiW0ICpDoMg/KgIsbgPAKCOzJqj5xJ8AAAAAElFTkSuQmCC',
			'CC4C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WEMYQxkaHaYGIImJtLI2OrQ6BIggiQU0ijQ4THV0YEEWawCqCHR0QHZf1Kppq1ZmZmYhuw+kjrURrg4hFhqIKgayoxHVDrBbGlHdgs3NAxV+VIRY3AcAbjLNLlYBIlsAAAAASUVORK5CYII=',
			'AE22' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB1EQxlCGaY6IImxBog0MDo6BAQgiYlMEWlgbQh0EEESC2gF8QIaRJDcF7V0atiqlVmropDcB1bXytCIbEdoKFBsClAU3bwAoCiaGKMDUBRFTDSUNTQwNGQQhB8VIRb3AQClhMvpiL0VhAAAAABJRU5ErkJggg==',
			'1A75' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YAlhDA0MDkMRYHRhDGBoCHZDViTqwtqKLMTqINDo0Oro6ILlvZda0lVlLV0ZFIbkPrG4KQ4MIil7RUIcAdDGRRkcHIIkm5trAEIDsPtEQsNhUh0EQflSEWNwHAGgHyW4B2k3rAAAAAElFTkSuQmCC',
			'733E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkNZQxhDGUMDkEVbRVpZGx0dUFS2MjQ6NASiik0BicLVQdwUtSps1dSVoVlI7mN0QFEHhqwNmOaJYBELaMB0S0ADFjcPUPhREWJxHwAb28qmrOBaLgAAAABJRU5ErkJggg==',
			'ADB3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGUIdkMRYA0RaWRsdHQKQxESmiDS6NgQ0iCCJBbQCxRodGgKQ3Be1dNrK1NBVS7OQ3IemDgxDQ3GYhymG4ZaAVkw3D1T4URFicR8AhxnPLenZz0kAAAAASUVORK5CYII=',
			'084E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHUMDkMRYA1hbGVodHZDViUwRaXSYiioW0ApUFwgXAzspaunKsJWZmaFZSO4DqWNtRNcr0ugaGohpB5o6sFvQxLC5eaDCj4oQi/sABwHKgfVlV4cAAAAASUVORK5CYII=',
			'2321' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WANYQxhCGVqRxUSmiLQyOjpMRRYLaGVodG0ICEXR3QrSFwDTC3HTtFVhq1ZmLUVxXwBYJYodjA4MjQ5TUMVYG4BiAWhuaQC6xQFVLDSUNYQ1NCA0YBCEHxUhFvcBAKEkyvBIkcxqAAAAAElFTkSuQmCC',
			'5F02' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkNEQx2mMEx1QBILaBBpYAhlCAhAE2N0dHQQQRILDBBpYIWohrsvbNrUsKWrooAQyX2tYHWNyHZAxVqR3RLQCrID6BokMZEpELcgi7EC7WWYwhgaMgjCj4oQi/sAtsvMZosFNiwAAAAASUVORK5CYII=',
			'6EA2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WANEQxmmMEx1QBITmSLSwBDKEBCAJBbQItLA6OjoIIIs1iDSwAomEe6LjJoatnRVFBAi3BcyBayuEdmOgFagWGhAKwO6WEPAFAY0twDFAtDdzNoQGBoyCMKPihCL+wATa80WOn5KsAAAAABJRU5ErkJggg==',
			'ED03' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QkNEQximMIQ6IIkFNIi0MoQyOgSgijU6Ojo0iKCJuQLJACT3hUZNW5m6KmppFpL70NShiKGbh8UODLdgc/NAhR8VIRb3AQCAr87dbBVXzAAAAABJRU5ErkJggg==',
			'5EF1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDA1qRxQIaRBpYGximYhELRRYLDACLwfSCnRQ2bWrY0tBVS1Hc14qiDqdYABYxkSmYYqwBQDcD3RIwCMKPihCL+wAlJ8s606C3LAAAAABJRU5ErkJggg==',
			'7EC1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNFQxlCHVpRRFtFGhgdAqaii7E2CISiiE0BiTHA9ELcFDU1bOmqVUuR3cfogKIODFkbMMVEwGICKGIBDWC3oImB3RwaMAjCj4oQi/sA8RbLVgsHihUAAAAASUVORK5CYII=',
			'16FE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA0MDkMRYHVhbWYEyyOpEHUQa0cUYHUQakMTATlqZNS1saejK0Cwk9zE6iGKYB9Tb6EqUGBa3hADd3MCI4uaBCj8qQizuAwC3XMZFMEN0iAAAAABJRU5ErkJggg==',
			'BEEC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVUlEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDHaYGIIkFTBFpYG1gCBBBFmsFiTE6sGCoY3RAdl9o1NSwpaErs5Ddh6YOxTxsYph2oLoFm5sHKvyoCLG4DwBmcsuLTUcmEQAAAABJRU5ErkJggg==',
			'8F88' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVUlEQVR4nGNYhQEaGAYTpIn7WANEQx1CGaY6IImJTBFpYHR0CAhAEgtoFWlgbQh0EMGtDuykpVFTw1aFrpqaheQ+Ys0jwg6om4Eq0Nw8UOFHRYjFfQBLBcxKKtpqegAAAABJRU5ErkJggg==',
			'67FD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANEQ11DA0MdkMREpjA0ujYwOgQgiQW0QMREkMUaGFpZEWJgJ0VGrZq2NHRl1jQk94VMYQhgRdfbyuiAKcbagC4mMkUELIbsFtYAsBiKmwcq/KgIsbgPAKTTyslVKDSYAAAAAElFTkSuQmCC',
			'9ECF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WANEQxlCHUNDkMREpog0MDoEOiCrC2gVaWBtEMQixggTAztp2tSpYUtXrQzNQnIfqyuKOghsxRQTwGIHNrdA3Yxq3gCFHxUhFvcBAB3pyLfcRMimAAAAAElFTkSuQmCC',
			'F4B0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMZWlmBGFksoIFhKmujw1QHVLFQ1oaAgAAUMUZX1kZHBxEk94VGLV26NHRl1jQk9wU0iLQiqYOKiYa6NgSiiQHdgmEHUAzTLRhuHqjwoyLE4j4AG1PN1llk1M8AAAAASUVORK5CYII=',
			'63D6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WANYQ1hDGaY6IImJTBFpZW10CAhAEgtoYWh0bQh0EEAWa2BoZQWKIbsvMmpV2NJVkalZSO4LmQJWh2peK8Q8EQJi2NyCzc0DFX5UhFjcBwAhBs0GH49P+AAAAABJRU5ErkJggg==',
			'DFF1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUElEQVR4nGNYhQEaGAYTpIn7QgNEQ11DA1qRxQKmiDSwNjBMRRFrBYuFYhGD6QU7KWrp1LCloauWIrsPTR1pYlMwxUIDIG4JGAThR0WIxX0AdEnNLGndq/0AAAAASUVORK5CYII=',
			'A186' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGaY6IImxBjAGMDo6BAQgiYlMYQ1gbQh0EEASC2hlAKpzdEB2X9TSVVGrQlemZiG5D6oOxbzQUAaweSJo5mETQ3dLQCtrKLqbByr8qAixuA8AnPPJrkSZ17kAAAAASUVORK5CYII=',
			'773C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkNFQx1DGaYGIIu2MjS6NjoEiKCJOTQEOrAgi00BiTo6oLgvatW0VVNXZiG7j9GBIQBJHRiygkSB5iGLiQBFGdDsCACJorkFJMaI7uYBCj8qQizuAwC1iMvFnjyPAgAAAABJRU5ErkJggg==',
			'BE1F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QgNEQxmmMIaGIIkFTBFpYAhhdEBWF9Aq0sCILgZSNwUuBnZSaNTUsFXTVoZmIbkPTR3cPKLEsOgFuZkx1BFFbKDCj4oQi/sAvcrKL1AsYkcAAAAASUVORK5CYII=',
			'4421' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpI37pjC0MoQCMbJYCMNURkeHqchijCEMoawNAaHIYqxTGF0ZGgJgesFOmjZt6dJVK7OWIrsvYIpIK0Mrqh2hoaKhDlPQ7AXxAzDFGB0wxVhDA0IDBkP4UQ9icR8AdjvK8WlHNpwAAAAASUVORK5CYII=',
			'1B86' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGaY6IImxOoi0Mjo6BAQgiYk6iDS6NgQ6CKDoBalzdEB238qsqWGrQlemZiG5D6oOxTxGqHkihMUw3RKC6eaBCj8qQizuAwD9T8j2tSi9JQAAAABJRU5ErkJggg=='        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>