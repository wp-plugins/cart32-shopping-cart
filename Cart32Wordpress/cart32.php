<?php
   /*
   Plugin Name: Cart32 Shopping Cart
   Plugin URI: http://wordpress.cart32.com
   Description: Add Cart32 to Wordpress
   Version: 2.0
   Author: Cart32 Dev Team, Lead by Bryan Whitaker
   Author URI: http://www.cart32.com
   License: GPL2
   */



// code to use cart32template in a different way so WP approves it.  This keeps from 
// having to use use an include
add_action( 'init', 'cart32_template_init_internal');
function cart32_template_init_internal() {add_rewrite_rule( 'my-api.php$', 'index.php?cart32_template_api=1', 'top' );}
add_filter( 'query_vars', 'cart32_template_query_vars' );
function cart32_template_query_vars( $query_vars ){
  $query_vars[] = 'cart32_template_api';
  return $query_vars;
}
add_action( 'parse_request', 'cart32_template_parse_request' );
function cart32_template_parse_request(&$wp){
  if ( array_key_exists( 'cart32_template_api', $wp->query_vars ) ) {
    include 'cart32template.php';
    exit();
  }
  return;
}
//finished code for cart32template.php

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Cart32 Wordpress Plugin';
	exit;
}

//SET GLOBAL VARIABLES
$sClientCode=get_option("cart32wp_client_code");
$sCart32URL=get_option("cart32wp_cart32_url");
$sC32WebURL=get_option("cart32wp_c32web_url");
$sAccountInfo=get_option("cart32wp_account_info");
$sCart32WordPressAccessCode=get_option('cart32wp_access_code');

if (get_option('cart32_wp_add_view_cart_to_menu')=='No') $blnAddViewCartToMenu=false;
else $blnAddViewCartToMenu=true;
$sViewCartText=get_option('cart32wp_view_cart_text');
if ($sViewCartText=='') $sViewCartText='View Shopping Cart';

//decide of showing the set up wizard.  Should be only first time loading plug in
$blnShowSetupWizard=false;
if ($sCart32WordPressAccessCode=='' || $sClientCode=='' || $sCart32URL=='' || $sC32WebURL=='' || $sAccountInfo=='') $blnShowSetupWizard=true;

if (array_key_exists('cart32wp_rerunwizard',$_POST)) {if ($_POST["cart32wp_rerunwizard"]=='Y') $blnShowSetupWizard=true;}

//In here temporarily.  Take out later
//$blnShowSetupWizard=true;

// * * * * * * * * * * * * * * * * * * * * * * * *
//  BUILD THE SETTINGS PAGE
// * * * * * * * * * * * * * * * * * * * * * * * *
function cart32wordpress_settings() {
   global $sCart32WordPressAccessCode,$sCart32URL,$sC32WebURL,$sClientCode,$dblStartTime,$blnShowSetupWizard,$sAccountInfo,$blnAddViewCartToMenu,$sViewCartText;

   if (isset($_POST['info_update'])) {
      if ($_POST["action"]=='save_view_cart_section') {
         update_option('cart32_wp_add_view_cart_to_menu', $_POST["cart32_wp_add_view_cart_to_menu"]);
         update_option('cart32wp_view_cart_text', $_POST["cart32wp_view_cart_text"]);
         if (get_option('cart32_wp_add_view_cart_to_menu')=='No') $blnAddViewCartToMenu=false;
         else $blnAddViewCartToMenu=true;
         $sViewCartText=get_option('cart32wp_view_cart_text');
         if ($sViewCartText=='') $sViewCartText='View Shopping Cart';
      } else {
         update_option('cart32wp_client_code', $_POST["cart32wp_client_code"]);
         update_option('cart32wp_cart32_url', $_POST["cart32wp_cart32_url"]);
         update_option('cart32wp_c32web_url', $_POST["cart32wp_c32web_url"]);
         update_option('cart32wp_access_code', $_POST["cart32wp_access_code"]);
         update_option('cart32wp_account_info', $_POST["cart32wp_account_info"]);
         //reset variables
         $sClientCode=get_option("cart32wp_client_code");
         $sCart32URL=get_option("cart32wp_cart32_url");
         $sC32WebURL=get_option("cart32wp_c32web_url");
         $sCart32WordPressAccessCode=get_option('cart32wp_access_code');
         $sAccountInfo=get_option("cart32wp_account_info");
         if (get_option('cart32_wp_add_view_cart_to_menu')=='No') $blnAddViewCartToMenu=false;
         else $blnAddViewCartToMenu=true;
         $sViewCartText=get_option('cart32wp_view_cart_text');
         if ($sViewCartText=='') $sViewCartText='View Shopping Cart';
         SendWordPressInfoToCart32(true,'');
         if ($sCart32WordPressAccessCode=='' || $sClientCode=='' || $sCart32URL=='' || $sC32WebURL=='' || $sAccountInfo=='') $blnShowSetupWizard=true;
      }
   }

   //if blank put in default
   if ($sCart32URL==''){$sCart32URL='https://dev8.cart32.com/cgi-bin/cart32.exe';update_option('cart32wp_cart32_url', $sCart32URL);}
   if ($sC32WebURL==''){$sC32WebURL='https://dev8.cart32.com/cgi-bin/c32web.exe';update_option('cart32wp_c32web_url', $sC32WebURL);}

//TEMP SECTION START
if (false) {
//if (true) {
   echo "<div class=postbox>";
   echo "<h3 style=\"padding:7px;\">Temp Section</h3>";
   echo "<div class=inside>";
   echo "BlogName=".get_option('blogname')."<br>";
   echo "Cart32 WordPress Access Code = $sCart32WordPressAccessCode<br>";
   echo "Client Code = $sClientCode<br>";
   echo "Cart32URL = $sCart32URL<br>";
   echo "C32WebURL = $sC32WebURL<br>";
   echo "AccountInfo = $sAccountInfo<br>";
   echo "plugin_dir_url() = ".plugin_dir_url('')."<br>";
   if ($blnShowSetupWizard) echo "Show Setup Wizard";
   else echo "Do Not Show Setup Wizard";
   echo "</div></div>";
}
//TEMP SECTION END

   echo "<img src=\"".plugin_dir_url('')."Cart32Wordpress/cart32_for_wordpress.png\" alt=\"Cart32 For Wordpress\">";
   echo "<H2>Cart32 Shopping Cart for WordPress</H2>";

   if (!$blnShowSetupWizard){
      echo "<div class=postbox>";
      echo "<h3 style=\"padding:7px;\">Adding Products To Your Website</h3>";
      echo "<div class=inside>";
      echo "All you need to do is click on the <a class=button href=\"javascript:alert('Press this button when adding a post or page to add a shopping cart button.');\">Insert Cart32 Shopping Cart Button</a> button when adding a post or page to add a product to your website.";
      echo "</div></div>";

      echo "<form method=\"post\" action=\"".$_SERVER["REQUEST_URI"]."\" style=\"margin-top:10px;\">";
      echo "<input type=hidden name=action value=save_view_cart_section>";
      echo "<div class=postbox>";
      echo "<h3 style=\"padding:7px;\">View Cart/Checkout Link On Your Menu</h3>";
      echo "<div class=inside>";
      echo "<table border=0 cellspacing=0 cellpadding=2>";
      echo "<tr><td>Add View Cart/Checkout link to your menu</td><td>";
      echo "<input type=radio name=\"cart32_wp_add_view_cart_to_menu\" id=\"cart32_wp_add_view_cart_to_menu_yes\" value=Yes ".ischecked($blnAddViewCartToMenu)."> Yes &nbsp;&nbsp;&nbsp;";
      echo "<input type=radio name=\"cart32_wp_add_view_cart_to_menu\" id=\"cart32_wp_add_view_cart_to_menu_no\" value=No ".ischecked(!$blnAddViewCartToMenu)."> No ";
      echo "</td></tr>";
      echo "<tr><td>View Shopping Cart/Checkout Text</td>";
      echo "<td><input type=text name=cart32wp_view_cart_text id=cart32wp_view_cart_text value=\"$sViewCartText\" size=40></td></tr>";
      echo "<tr><td></td><td><input type=\"submit\" name=\"info_update\" value=\"Save\" /></td></tr>";
      echo "</table>";
      echo "</div></div>";
      echo "</form>";
   }

   echo "<div class=postbox>";
   echo "<h3 style=\"padding:7px;\">Cart32 Shopping Cart Settings</h3>";
   echo "<div class=inside>";

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
// SET UP WIZARD
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
   echo "<form method=\"post\" action=\"".$_SERVER["REQUEST_URI"]."\" style=\"margin-top:10px;\">";
   echo "<input type=hidden name=action value=\"\">";
   echo "<input type=hidden name=cart32wp_access_code id=cart32wp_access_code value=\"\">";
   echo "<input type=hidden name=cart32wp_account_info id=cart32wp_account_info value=\"\">";
   echo "<div id=WizardStep1>";
   echo "  <h3>Setup Wizard Step 1.  Connect WordPress To A Cart32 Account</h3>";
   echo "  Choose One: <input type=radio name=HaveACart32Account value=Y id=HaveRadioYes onclick=\"WizardNextSlide(1)\"> <label for=HaveRadioYes>I already have a Cart32 Account</label>&nbsp;&nbsp;&nbsp;<input type=radio name=HaveACart32Account value=N id=HaveRadioNo onclick=\"WizardNextSlide(1)\"> <label for=HaveRadioNo>I do not have a Cart32 Account</label>";
   echo "  <br><br><a class=button href=\"javascript:WizardNextSlide(1)\">Next</a>";
   echo "</div>";

   echo "<div id=WizardStep2>";
   echo "  <div id=WizardStep2a>";
   echo "      <h3>Setup Wizard Step 2.  Enter Your Cart32 Account Information</h3>";
   echo "      Visit your Cart32 Web Administratration and go to the Store & Pages -> Wordpress tab to get the following fields:";
   echo "      <table border=0 cellspacing=0 cellpadding=2>";
   echo "      <tr><td>Cart32 Address</td><td><input type=\"text\" name=\"cart32wp_cart32_url\" id=\"cart32wp_cart32_url\" value=\"$sCart32URL\" maxlength=150 size=50 /></td></tr>";
   echo "      <tr><td>C32Web Address</td><td><input type=\"text\" name=\"cart32wp_c32web_url\" id=\"cart32wp_c32web_url\" value=\"$sC32WebURL\" maxlength=150 size=50 /></td></tr>";
   echo "      <tr><td>Cart32 Account</td><td><input type=\"text\" name=\"cart32wp_client_code\" id=\"cart32wp_client_code\" value=\"$sClientCode\" maxlength=20 size=20 /></td></tr>";
   echo "      <tr><td>Cart32 Password</td><td><input type=\"password\" name=\"cart32wp_client_password\" id=\"cart32wp_client_password\" value=\"\" maxlength=20 size=20 /></td></tr>";
//   echo "      <tr><td></td><td><input type=\"submit\" name=\"info_update\" value=\"Save\" /></td></tr>";
   echo "      </table>";
   echo "  </div>";
   echo "  <div id=WizardStep2b>";
   echo "      <h3>Setup Wizard Step 2. Get A Cart32 Account</h3>";
   echo "      Fill out this form to create a free Cart32 trial.<br>";
   echo "      <table border=0 cellspacing=0 cellpadding=2>";
   echo "      <tr><td>Site Name or Company Name</td><td><input type=\"text\" name=\"cart32wp_newaccount_name\" id=\"cart32wp_newaccount_name\" value=\"".get_option('blogname')."\" maxlength=30 size=30 /></td></tr>";
   echo "      <tr><td>First and Last Name</td><td><input type=\"text\" name=\"cart32wp_first_last_name\" id=\"cart32wp_first_last_name\" value=\"\" maxlength=50 size=30 /></td></tr>";
   echo "      <tr><td>Email Address</td><td><input type=\"text\" name=\"cart32wp_email\" id=\"cart32wp_email\" value=\"\" maxlength=50 size=30 /></td></tr>";
   echo "      </table>";
   echo "  </div>";
   echo "  <br><br><a class=button href=\"javascript:WizardPreviousSlide(2)\">Back</a>";
   echo "  <a class=button href=\"javascript:WizardNextSlide(2)\">Next</a>";
   echo "</div>";

   echo "<div id=WizardStep3>";
   echo "  <div id=WizardStep3a>";
   echo "    <h3>Setup Wizard Step 3. </h3>";
//check to see if they are on a high enough version and if set set up WP connection
   echo "      <div id=WizardStep3aContent>";  //JS will fill in this info
   echo "      </div>";
   echo "  </div>";
   echo "  <div id=WizardStep3b>";
   echo "    <h3>Setup Wizard Step 3. </h3>";
   echo "      <div id=WizardStep3bContent>WizardStep3bContent";  //JS will fill in this info
   echo "      </div>";
   echo "  </div>";
   echo "  <br><br><a class=button href=\"javascript:WizardPreviousSlide(3)\">Back</a>";
   //echo "  <a class=button href=\"javascript:WizardNextSlide(3)\">Next</a>";
   echo "</div>";

   echo "</div></form>";


// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
// MAIN SETTINGS SECTION
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
   echo "<div id=\"Cart32WordPressSettingsDiv\" style=\"padding:6px;\">";

   echo "<div id=TrialInfoDiv></div>";

   echo "  <div id=Cart32GettingStartedDiv>";
   echo "      This is where you set up all the options for your shopping cart including shipping, tax, discounts and much, much more.<br>";
   echo "      <br><a href=\"javascript:LaunchCart32WebAdmin()\" class=button>Launch Cart32 Web Administration</a><br>";
   echo "  </div>";

   echo "<br><br><br>If you need a new account or need to start over then <form method=\"post\" action=\"".$_SERVER["REQUEST_URI"]."\" style=\"margin-top:10px;\">";
   echo "<input type=hidden name=cart32wp_rerunwizard id=cart32wp_rerunwizard value=Y>";
   echo "<input type=submit class=button value=\"Re-run Setup Wizard\">";
   echo "</form>";
   echo "<form method=post action=\"$sC32WebURL\" target=\"C32WebWindow\" name=\"C32WebForm\" id=\"C32WebForm\">";
   echo "<input type=hidden name=client value=\"$sClientCode\">";
   echo "<input type=hidden name=wpcode value=\"$sCart32WordPressAccessCode\">";
   echo "<input type=hidden name=tabname value=\"Home\">";
   echo "</form>";

   echo "</div>";



   echo "</div>";  //close web admin area

   echo "<script language=\"javascript\">";
   echo "  function CheckSlide(iSlide) {";
   echo "     switch (iSlide) {";
   echo "        case 1:if(!document.getElementById('HaveRadioYes').checked && !document.getElementById('HaveRadioNo').checked){alert('Please make a choice.');return false;}else return true;break;";
   echo "        case 2:if(document.getElementById('HaveRadioNo').checked && document.getElementById('cart32wp_newaccount_name').value==''){alert('Please enter a site name or company name.');document.getElementById('cart32wp_newaccount_name').focus();return false;}";
   echo "           else if(document.getElementById('HaveRadioNo').checked && document.getElementById('cart32wp_first_last_name').value==''){alert('Please enter your first and last name.');document.getElementById('cart32wp_first_last_name').focus();return false;}";
   echo "           else if(document.getElementById('HaveRadioNo').checked && document.getElementById('cart32wp_email').value==''){alert('Please enter your email address.');document.getElementById('cart32wp_email').focus();return false;}";
   echo "           else if(document.getElementById('HaveRadioNo').checked){return true;}";
   echo "           else{if(document.getElementById('HaveRadioYes').checked){if(document.getElementById('cart32wp_cart32_url').value==''||document.getElementById('cart32wp_c32web_url').value==''||document.getElementById('cart32wp_client_password').value==''||document.getElementById('cart32wp_cart32_url').value==''){alert('Please enter values into all fields.');return false;}else{return true;}}}break;";
   echo "     }";
   echo "  }";
   echo "  function WizardNextSlide(iSlide) {if(CheckSlide(iSlide)){HandleSubSlides(iSlide+1);jQuery('#WizardStep'+iSlide).slideToggle('25');jQuery('#WizardStep'+(iSlide+1)).slideToggle('25');}}";
   echo "  function WizardPreviousSlide(iSlide) {HandleSubSlides(iSlide-1);jQuery('#WizardStep'+iSlide).slideToggle('25');jQuery('#WizardStep'+(iSlide-1)).slideToggle('25');}";
   echo "  function HandleSubSlides(iSlide) {";
   echo "     switch (iSlide) {";
   echo "        case 2:";
   echo "           if (document.getElementById('HaveRadioYes').checked){jQuery('#WizardStep2a').show();jQuery('#WizardStep2b').hide();}";
   echo "           else {jQuery('#WizardStep2b').show();jQuery('#WizardStep2a').hide();}";
   echo "           break;";
   echo "        case 3:";
   echo "           if (document.getElementById('HaveRadioYes').checked){jQuery('#WizardStep3a').show();jQuery('#WizardStep3b').hide();HandleSlideAction('3a');}";
   echo "           else {jQuery('#WizardStep3b').show();jQuery('#WizardStep3a').hide();HandleSlideAction('3b');}";
   echo "           break;";
   echo "     }";
   echo "  }";
   echo "  function HandleSlideAction(sSlide) {";
   echo "     switch (sSlide) {";
   echo "        case '3a':";
   echo "           jQuery('#WizardStep3aContent').html('<img src=\"../wp-includes/images/wpspin.gif\"> Contacting Cart32, please wait ...');";
   echo "           sCart32URL=document.getElementById('cart32wp_cart32_url').value;";
   echo "           sC32WebURL=document.getElementById('cart32wp_c32web_url').value;";
   echo "           sClientCode=document.getElementById('cart32wp_client_code').value;";
   echo "           sClientPassword=document.getElementById('cart32wp_client_password').value;";
   echo "           sQS='';";
   echo "           sVersion='';";
   echo "           iVersion=0;";
   echo "           sJSON='';";
   echo "           fReturn=function(blnSuccess,sReturn){";
   echo "              if(blnSuccess){";  //see if it's really succesful.  'Cart32' will be in it
   echo "                 if (sReturn.indexOf('Cart32')<0) blnSuccess=false;";
   echo "              }";
   echo "              if(blnSuccess){jQuery('#WizardStep3aContent').html(sReturn);";
   echo "                 var objHTML=document.createElement('div');";  //parse out the version from the HTML returned
   echo "                 objHTML.innerHTML=sReturn;";
   echo "                 ArrSpans=objHTML.getElementsByTagName('span');";  //put all spans in an array, then go through each one looking for id=MessageTableTitle.  That will have the version string
   echo "                 for(i=0;i<ArrSpans.length;i++) {";
   echo "                    if (ArrSpans[i].id=='MessageTableTitle') sVersion=ArrSpans[i].innerHTML;";
   echo "                    if (ArrSpans[i].id=='JSONResponse') sJSON=ArrSpans[i].innerHTML;";
   echo "                 }";
   echo "                 iMinVersion=8.03;";
   echo "                 iVersion=parseFloat(sVersion.replace('Cart32 v',''));";
   echo "                 if (iVersion < iMinVersion) {s='Your version of Cart32 does not support the Wordpress Plug-In.  You need to upgrade to Cart32 v'+iMinVersion+' or above.'}";
   echo "                 else {";
   echo "                    var objJSON = jQuery.parseJSON(sJSON);";
   echo "                    s='';";
   echo "                    if (objJSON.Success=='1') {";
   echo "                       document.getElementById('cart32wp_account_info').value='A';";
   echo "                       document.getElementById('cart32wp_access_code').value=objJSON.WordPressAccessCode;";
   echo "                       s+=objJSON.Message+' <input type=\"submit\" name=\"info_update\" value=\"Click Here To Complete The Wizard\" class=button />';";
   echo "                    } else s+=objJSON.Message;";
   echo "                 }";
   echo "                 jQuery('#WizardStep3aContent').html(s);";
   echo "              } else {jQuery('#WizardStep3aContent').html('There was an error connecting to Cart32.  Please double check your settings and try again.');}";
   echo "           };";
   echo "           CallCart32(sCart32URL,sC32WebURL,sClientCode,sClientPassword,fReturn);";
   echo "           break;";
   echo "        case '3b':";
   echo "           jQuery('#WizardStep3bContent').html('<img src=\"../wp-includes/images/wpspin.gif\"> Creating Cart32 Trial.  Please wait (this can take 30-60 seconds) ...');";
//make call to Cart32.com to create a free trial
//return from connecting WP to Cart32 after creating trial
   echo "           fReturnTrialConnect=function(blnSuccess,sReturn){";
   echo "              var objHTML=document.createElement('div');";  //parse out the version from the HTML returned
   echo "              objHTML.innerHTML=sReturn;";
   echo "              ArrSpans=objHTML.getElementsByTagName('span');";  //put all spans in an array, then go through each one looking for id=MessageTableTitle.  That will have the version string
   echo "              for(i=0;i<ArrSpans.length;i++) {";
   echo "                 if (ArrSpans[i].id=='MessageTableTitle') sVersion=ArrSpans[i].innerHTML;";
   echo "                 if (ArrSpans[i].id=='JSONResponse') sJSON=ArrSpans[i].innerHTML;";
   echo "              }";
   echo "              var objJSON = jQuery.parseJSON(sJSON);";
   echo "              s='';";
//   echo "              s='Success='+objJSON.Success+'<br>Message='+objJSON.Message+'<br>WPAccessCode='+objJSON.WordPressAccessCode+'<br><br>';";
   echo "              if (objJSON.Success=='1') {";
   echo "                 document.getElementById('cart32wp_access_code').value=objJSON.WordPressAccessCode;";
   echo "                 s+='Cart32 Account Successfully Created <input type=\"submit\" name=\"info_update\" value=\"Click Here To Complete The Wizard\" class=button />';";
   echo "              } else s+='Account not created successfully.  Please contact Cart32 Support: <a href=\"mailto:support@cart32.com\">support@cart32.com</a>';";
   echo "              jQuery('#WizardStep3bContent').html(s);";
   echo "           };";
//return function from creating trial.  Next connect WP to Cart32
   echo "           fReturnTrial=function(blnSuccess,sReturn){";
   echo "              var objJSON = jQuery.parseJSON(sReturn);";
   echo "                document.getElementById('cart32wp_cart32_url').value=objJSON.Cart32URL;";
   echo "                document.getElementById('cart32wp_c32web_url').value=objJSON.C32WebURL;";
   echo "                document.getElementById('cart32wp_client_code').value=objJSON.ClientCode;";
   echo "                document.getElementById('cart32wp_account_info').value='T';";
//call back to Cart32 to connect it to WP
   echo "                CallCart32(objJSON.Cart32URL,objJSON.C32WebURL,objJSON.ClientCode,objJSON.ClientPassword,fReturnTrialConnect);";
   echo "           };";
   echo "           CreateCart32Trial(document.getElementById('cart32wp_newaccount_name').value,document.getElementById('cart32wp_first_last_name').value,document.getElementById('cart32wp_email').value,fReturnTrial);";
   echo "           break;";
   echo "     }";
   echo "  }";
   echo "  function CreateCart32Trial(sCompanyName,sFirstLastName,sEmail,fReturn){";
   echo "     sURL='".plugin_dir_url('')."Cart32Wordpress/cart32popup.php';";
   echo "     sQS='wp=trial&company='+encodeURIComponent(sCompanyName)+'&firstlastname='+encodeURIComponent(sFirstLastName)+'&email='+encodeURIComponent(sEmail);";
   echo "     jQuery.ajax({type:'POST',data:sQS,url:sURL,success:function(data){fReturn(true,data);},error:function(XMLHttpRequest, textStatus, errorThrown){fReturn(false,'Error');}});";
   echo "  }";
   echo "  function CallCart32(sCart32URL,sC32WebURL,sClientCode,sClientPassword,fReturn){";
   echo "     sURL='".plugin_dir_url('')."Cart32Wordpress/cart32popup.php';";
   echo "     sQS='wp=y&carturl='+encodeURIComponent(sCart32URL)+'&c32weburl='+encodeURIComponent(sC32WebURL)+'&clientcode='+encodeURIComponent(sClientCode)+'&clientpassword='+encodeURIComponent(sClientPassword);";
   echo "     jQuery.ajax({type:'POST',data:sQS,url:sURL,success:function(data){fReturn(true,data);},error:function(XMLHttpRequest, textStatus, errorThrown){fReturn(false,'Error');}});";
   echo "  }";
   echo "  function CheckAccountInfo(sCart32URL,sClientCode,fReturn){";
   echo "     sURL='".plugin_dir_url('')."Cart32Wordpress/cart32popup.php';";
   echo "     sQS='wp=accountinfo&carturl='+encodeURIComponent(sCart32URL)+'&clientcode='+encodeURIComponent(sClientCode);";
   echo "     jQuery.ajax({type:'POST',data:sQS,url:sURL,success:function(data){fReturn(true,data);},error:function(XMLHttpRequest, textStatus, errorThrown){fReturn(false,'Error');}});";
   echo "  }";
   echo "  function LaunchCart32WebAdmin(){x=window.open('', 'C32WebWindow', 'width=1000,height=750,resizable=yes,scrollbars=yes');document.C32WebForm.submit();}";
   echo "  jQuery(function() {";
   echo "     for (i=1;i<=3;i++) jQuery('#WizardStep'+i).hide();";  //hide all steps of the wizard
   if ($blnShowSetupWizard) {
      echo "jQuery('#Cart32WordPressSettingsDiv').hide();jQuery('#WizardStep1').slideToggle('25');";
   }  //Show the set up wizard if necessary
   else {
      if ($sAccountInfo!='A') {
         echo "function fCheckAccountInfoReturn(blnSuccess,sReturn) {";
         //echo "   alert(sReturn);";
         echo "   var objJSON = jQuery.parseJSON(sReturn);";
         echo "   var s=objJSON.Message;";
         echo "   jQuery('#TrialInfoDiv').html('<span style=\"border:1px solid #333333;padding:6px;background-color:#5486c5;color:#ffffff;\">'+s+'</span><br><br><br>');";
         echo  "};";
         echo "   jQuery('#TrialInfoDiv').html('<img src=\"../wp-includes/images/wpspin.gif\"> Loading Trial Account Information ...<br><br>');";
         echo "CheckAccountInfo('$sCart32URL','$sClientCode',fCheckAccountInfoReturn);";
      }
   }
   echo "  });";
   echo "</script>";
}

function handle_cart32_shortcode($ArrParams) {
   global $sCart32URL,$sClientCode;
   $sButtonText=$ArrParams['buttontext'];
   if ($ArrParams['type']=="view") {
     if ($sButtonText=='') $sButtonText='View Shopping Cart';
     $s='<form method=post action="'.$sCart32URL.'/'.$sClientCode.'-additem">';
     $s.='<input type=submit value="'.$sButtonText.'" class=button>';
     $s.='</form>';
   } else {
     if ($sButtonText=='') $sButtonText='Add To Shopping Cart';
     $s='<form method=post action="'.$sCart32URL.'/'.$sClientCode.'-additem" style="margin:0;">';
     $s.='<input type=hidden name=item value="'.$ArrParams['item'].'">';
     $s.='<input type=hidden name=partno value="'.$ArrParams['partno'].'">';
     $s.='<input type=hidden name=price value="'.$ArrParams['price'].'">';
     if (array_key_exists('weight',$ArrParams)) $s.='<input type=hidden name=weight value="'.$ArrParams['weight'].'">';
     if (array_key_exists('options',$ArrParams)) {
        $s.='<table border=1 cellspacing=0 cellpadding=2 style="width:0;padding:1px;margin:1px;">';
        $ArrOptions=explode("||",$ArrParams['options']);
        $iOption=0;
        for ($i=0;$i<count($ArrOptions);$i++){
           $iOption++;
           $ArrOption=explode("~",$ArrOptions[$i]);
           $s.='<tr><td>'.$ArrOption[0].'</td>';
           if ($ArrOption[1]=='Drop Down List') {
              $s.='<td><select name=p'.$iOption.'>';
              $ArrOptionChoices=explode(';',$ArrOption[2]);
              for ($j=0;$j<count($ArrOptionChoices);$j++){
                $s.='<option value=\"'.$ArrOptionChoices[$j].'\">'.$ArrOptionChoices[$j].'</option>';
              }
              $s.='</select><input type=hidden name=t'.$iOption.' value="d-'.$ArrOption[0].';'.$ArrOption[2].'"></td>';
           } else if ($ArrOption[1]=='Text Box') {
              $s.='<td><input type=text size=15 name=p'.$iOption.'><input type=hidden name=t'.$iOption.' value="t-'.$ArrOption[0].'"></td>';
           }
           $s.='</tr>';
        }
        $s.='</table>';
     }
     $s.='<input type=submit value="'.$sButtonText.'" class=button>';
     $s.='</form>';
   }
   return $s;
}


// Display The Options Page
function cart32wordpress_settings_page () {
   add_options_page("Cart32 Wordpress Plugin Options", "Cart32 Shopping Cart", 'manage_options', "Cart32Wordpress", 'cart32wordpress_settings');
}
function add_cart32wordpress_plugin_settings_link($links, $file) {
   if ($file == plugin_basename(__FILE__)){
      $settings_link = '<a href="options-general.php?page=Cart32Wordpress">'.(__("Settings", "Cart32Wordpress")).'</a>';
      array_unshift($links, $settings_link);
   }
   return $links;
}



function handle_get_footer() {SendWordPressInfoToCart32(false,'handle_get_footer');}
function handle_in_admin_footer() {SendWordPressInfoToCart32(false,'handle_in_admin_footer');}
function handle_theme_activation() {SendWordPressInfoToCart32(true,'handle_theme_activation');}
function handle_theme_customize_save() {SendWordPressInfoToCart32(true,'handle_theme_customize_save');}

function SendWordPressInfoToCart32($blnDoUpdate=false,$sCaller=''){
   global $sCart32WordPressAccessCode,$sCart32URL,$sC32WebURL,$sClientCode;

   if (get_option('last_cart32_update') != date("Y-m-d")) $blnDoUpdate=true;

$dblStartTime=get_option('last_cart32_call');
$dblEndTime=microtime(true);
$t=$dblEndTime-$dblStartTime;

//SendTestEmail('In SendWP... From '.$sCaller.', t='.$t,'Hello');
   if ($blnDoUpdate && $t > 25){
//SendTestEmail('In SendWP... Running Update','Hello');
      update_option('last_cart32_call',microtime(true));
      update_option('last_cart32_update',date("Y-m-d"));
      $sQS='client='.urlencode($sClientCode);
      $sQS.='&wpaccesscode='.urlencode($sCart32WordPressAccessCode);
      $s=site_url('');
      $s=str_replace('http://','',$s);
      $s=str_replace('https://','',$s);
      $sQS.='&wpurl='.urlencode($s.'/index.php?cart32_template_api=1');
      $sURL=$sCart32URL.'/'.$sClientCode.'-UpdateWPInfo';
//echo "sURL=$sURL<br>";
      $sRet=do_post_request($sURL,$sQS);
//echo "sRet=$sRet<br>";
   }
}

function SendTestEmail($sSubject,$message='hello') {
   $to      = 'bryan@cart32.com';
   $subject = $sSubject;
   $headers = 'From: bryan@cart32.com' . "\r\n" .
    'Reply-To: bryan@cart32.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
   mail($to, $subject, $message, $headers);
}

//action to add a custom button to the content editor
function add_cart_button_to_post($context) {
  $context .= "<a class='button thickbox' title='Add Cart32 Shopping Cart Button' href='#TB_inline?width=600&height=600&inlineId=cart32_popup_container'>Insert Cart32 Shopping Cart Button</a>";
  $context .= "<script language=\"javascript\">";
  $context .= "var iTemp=1;";
  $context .= "function CreateCart32ShortCodeAddToCart(){var s='';";
  $context .= "  s+=document.getElementById('Cart32Item').value+' ('+document.getElementById('Cart32PartNo').value+')<br>';";
  $context .= "  s+=formatCurrency(document.getElementById('Cart32Price').value,2,',','.','$','')+'<br>';";
  $context .= "  s+='[cart32 type=\"add\" item=\"'+document.getElementById('Cart32Item').value+'\" ';";
  $context .= "  s+='partno=\"'+document.getElementById('Cart32PartNo').value+'\" ';";
  $context .= "  s+='price=\"'+document.getElementById('Cart32Price').value+'\" ';";
  $context .= "  if (document.getElementById('Cart32Weight').value!='') s+='weight=\"'+document.getElementById('Cart32Weight').value+'\" ';";
  //options
  $context .= "   var blnHasOptions=false;";
  $context .= "   jQuery('#Cart32OptionsTable > tbody  > tr').each(function() {";
  $context .= "      if (!blnHasOptions) s+='options=\"';";
  $context .= "      else s+='||';";
  $context .= "      sID=this.id.replace('Cart32OptionsRow','');";
  $context .= "      s+=jQuery('#Cart32OptionName'+sID).val()+'~'+jQuery('#Cart32OptionType'+sID).val()+'~'+jQuery('#Cart32OptionDetail'+sID).val();";
  $context .= "      blnHasOptions=true;";
  $context .= "   });";
  $context .= "   if (blnHasOptions) s+='\" ';";
  $context .= "  s+='buttontext=\"'+document.getElementById('AddToCartButtonText').value+'\"';";
  $context .= "  s+=']';";
  $context .= "  if (AddToCartFormOK()) InsertIntoPost(s);";
  $context .= "}";
  $context .= "function CreateCart32ShortCodeViewCart(){var s='';";
  $context .= "  s+='[cart32 type=\"view\" buttontext=\"'+document.getElementById('ViewCartButtonText').value+'\"]';";
  $context .= "  InsertIntoPost(s);";
  $context .= "}";
  $context .= "function InsertIntoPost(s){window.send_to_editor(s);}";
  $context .= "function AddToCartFormOK(){";
  $context .= "   x=document.getElementById('Cart32Item');if(x.value==''){alert('Please insert an item name.');x.focus();return false;}";
  $context .= "   x=document.getElementById('Cart32PartNo');if(x.value==''){alert('Please insert a part number or SKU.');x.focus();return false;}";
  $context .= "   x=document.getElementById('Cart32Price');if(x.value==''){alert('Please insert a price.');x.focus();return false;}";
  $context .= "   return true;";
  $context .= "}";
  $context .= "function ShowAddToCartButton(){";
  $context .= "  x=document.getElementById('AddToCartButtonTab');x.style.display='block';x.style.visibility='visible';";
  $context .= "  x=document.getElementById('ViewCartButtonTab');x.style.display='none';x.style.visibility='hidden';";
  $context .= "  x=document.getElementById('ViewCartButtonCell');x.style.border='1px solid #aaaaaa';x.style.backgroundColor='';";
  $context .= "  x=document.getElementById('AddToCartButtonCell');x.style.borderLeft='1px solid #aaaaaa';x.style.borderTop='1px solid #aaaaaa';x.style.borderRight='1px solid #aaaaaa';x.style.borderBottom='';x.style.backgroundColor='#eeeeee';";
  $context .= "}";
  $context .= "function ShowViewCartButton(){";
  $context .= "  x=document.getElementById('ViewCartButtonTab');x.style.display='block';x.style.visibility='visible';";
  $context .= "  x=document.getElementById('AddToCartButtonTab');x.style.display='none';x.style.visibility='hidden';";
  $context .= "  x=document.getElementById('AddToCartButtonCell');x.style.border='1px solid #aaaaaa';x.style.backgroundColor='';";
  $context .= "  x=document.getElementById('ViewCartButtonCell');x.style.borderLeft='1px solid #aaaaaa';x.style.borderTop='1px solid #aaaaaa';x.style.borderRight='1px solid #aaaaaa';x.style.borderBottom='';x.style.backgroundColor='#eeeeee';";
  $context .= "}";
  $context .= "function formatCurrency(num,dec,sep,decChar,pre,post) {";
  $context .= "  var n = num.toString().split(decChar);";
  $context .= "  return (pre || '') + n[0].replace(/(d)(?=(ddd)+(?!d))/g, \"$1\" + sep) + (n.length > 1 ? decChar + n[1].substr(0,dec) : '') + (post || '');";
  $context .= "}";
  $context .= "function AddCart32Option() {";
  $context .= "   iTemp++;";
  $context .= "   var sNewRow = jQuery('<tr style=\"cursor:move;\" id=\"Cart32OptionsRow'+iTemp+'\"><td><input type=text size=10 name=Cart32OptionName'+iTemp+' id=Cart32OptionName'+iTemp+' placeholder=\"Option name\"></td><td><select onclick=\"ShowHideCart32OptionDetail()\" onchange=\"ShowHideCart32OptionDetail()\" onkeyup=\"ShowHideCart32OptionDetail()\" name=Cart32OptionType'+iTemp+' id=Cart32OptionType'+iTemp+'><option value=\"Drop Down List\">Drop Down List</option><option value=\"Text Box\">Text Box</option></select></td><td></td><td><input type=text name=Cart32OptionDetail'+iTemp+' id=Cart32OptionDetail'+iTemp+' placeholder=\"Ex - Red;Blue;Green\"></td><td><a href=\"javascript:RemoveCart32Option(\'Cart32OptionsRow'+iTemp+'\');\">X</a></td></tr>');";
  $context .= "   jQuery('#Cart32OptionsTable').append(sNewRow);";
  $context .= "   jQuery('#Cart32OptionsTable tbody').sortable();";
  $context .= "   ShowHideCart32OptionDetail();";
  $context .= "}";
  $context .= "function RemoveCart32Option(sID) {jQuery('#'+sID).remove()}";
  $context .= "function ShowHideCart32OptionDetail() {";
  $context .= "   jQuery('#Cart32OptionsTable > tbody  > tr').each(function() {";
  $context .= "      sID=this.id.replace('Cart32OptionsRow','');";
  $context .= "      if (jQuery('#Cart32OptionType'+sID).val()=='Drop Down List') jQuery('#Cart32OptionDetail'+sID).show();";
  $context .= "      else jQuery('#Cart32OptionDetail'+sID).hide();";
  $context .= "   });";
  $context .= "}";
  $context .= "</script>";
  return $context;
}

function add_inline_popup_content() {
   echo "<div id=\"cart32_popup_container\" style=\"border:1px solid #ff0000;display:none;\">";
   echo "<h3>Create Cart32 Shopping Cart Buttons</h3>";
   echo "<form>";
   echo "<table border=0 cellspacing=0 cellpadding=6>";
   echo "<tr><td id=\"AddToCartButtonCell\" style=\"background-color:#eeeeee;border-left:1px solid #aaaaaa;border-top:1px solid #aaaaaa;border-right:1px solid #aaaaaa;\"><a class=button href=\"javascript:ShowAddToCartButton();\">Add To Cart Button</a></td>";
   echo "<td style=\"border-bottom:1px solid #aaaaaa;\">&nbsp;</td>";
   echo "<td id=\"ViewCartButtonCell\" style=\"border:1px solid #aaaaaa;\"><a class=button href=\"javascript:ShowViewCartButton();\">View Cart/Checkout Button</a></td>";
   echo "<td style=\"border-bottom:1px solid #aaaaaa;\">&nbsp;</td></tr>";
   echo "<tr><td colspan=4 style=\"background-color:#eeeeee;border-left:1px solid #aaaaaa;border-bottom:1px solid #aaaaaa;border-right:1px solid #aaaaaa;\">";
   echo "<div id=\"AddToCartButtonTab\" style=\"\">";
   echo "  <table border=0 cellspacing=0 cellpadding=3>";
   echo "  <tr><td>Item Name</td><td><input type=text size=30 name=Cart32Item id=Cart32Item></td></tr>";
   echo "  <tr><td>Part Number/SKU</td><td><input type=text name=Cart32PartNo id=Cart32PartNo></td></tr>";
   echo "  <tr><td>Price</td><td><input type=text size=10 name=Cart32Price id=Cart32Price></td></tr>";
   echo "  <tr><td>Button Text</td><td><input type=text name=AddToCartButtonText id=AddToCartButtonText value=\"Add To Shopping Cart\"></td></tr>";
   echo "  <tr><td>Weight</td><td><input type=text size=10 name=Cart32Weight id=Cart32Weight></td></tr>";
   echo "  <tr><td valign=top>Options<br><span style=\"font-size:7pt;\">(Size, Color, etc)</span></td><td valign=top><a href=\"javascript:AddCart32Option()\">Add An Option</a><br>";
   echo "  <table id=\"Cart32OptionsTable\" border=0 cellspacing=0 cellpadding=2><tbody></tbody></table></td></tr>";
//   echo "  <tr><td></td><td>More ...</td></tr>";
   echo "  <tr><td colspan=2><a class=button href=\"javascript:CreateCart32ShortCodeAddToCart()\">Insert into Post/Page</a> </td></tr>";
//use this to give the option to add or edit what's already on the page/post
//<a href=\"javascript:alert(document.getElementById('content_ifr').contentWindow.document.body.innerHTML);\">Test</a>
   echo "  </table>";
   echo "</div>";
   echo "<div id=\"ViewCartButtonTab\" style=\"display:none;visibility:hidden;\">";
   echo "  <table border=0 cellspacing=0 cellpadding=3>";
   echo "  <tr><td>Button Text</td><td><input type=text name=ViewCartButtonText id=ViewCartButtonText value=\"View Shopping Cart\"></td></tr>";
   echo "  <tr><td><a class=button href=\"javascript:CreateCart32ShortCodeViewCart()\">Insert into Post/Page</a></td></tr>";
   echo "  </table>";
   echo "</div>";
   echo "</form>";
   echo "</table>";
   echo "</div>";
}

function add_view_cart_link( $nav, $args ) {
   global $blnAddViewCartToMenu,$sViewCartText,$sClientCode,$sCart32URL;

   if ($blnAddViewCartToMenu)
      return $nav.'<li><a href="'.$sCart32URL.'/'.$sClientCode.'-additem">'.$sViewCartText.'</a></li>';
   else
      return $nav;
}

function do_post_request($url, $postdata)  {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
  curl_setopt($ch, CURLOPT_HEADER, 0 );
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata );
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
  $page = curl_exec($ch);
  curl_close($ch);
  return $page;
}
function isselected($x) {
   if ($x) return "SELECTED";
   else return "";
}
function ischecked($x) {
   if ($x) return "CHECKED";
   else return "";
}

//actions
add_action('admin_menu','cart32wordpress_settings_page');  //add Cart32 under settings menu.  This is set up calling the function cart32wordpress_settings_page
add_shortcode('cart32', 'handle_cart32_shortcode');  //add [cart32] shortcode
add_action('media_buttons_context', 'add_cart_button_to_post');  //add a button to the content editor, next to the media button this button will show a popup that contains inline content
add_action('admin_footer', 'add_inline_popup_content');  //add some content to the bottom of the page.  This will be shown in the inline modal
add_action('get_footer','handle_get_footer');
add_action('in_admin_footer','handle_in_admin_footer');
add_action('after_switch_theme', 'handle_theme_activation');  //code to run when a theme is changed
add_action('customize_save', 'handle_theme_customize_save');  //code to run when a custom theme is saved
//filters
add_filter('plugin_action_links', 'add_cart32wordpress_plugin_settings_link', 10, 2 );
add_filter('wp_nav_menu_items','add_view_cart_link', 10, 2);
?>
