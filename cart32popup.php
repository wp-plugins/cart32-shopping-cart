<?php
if (request('carturl')) {
   $URL=request('carturl');
} else {
   $URL='';
}

if (request('wp')=='y') {
   $URL=$URL.'/'.request('clientcode').'-wordpressconnect';
} else if (request('wp')=='trial') {
   $URL='https://www.cart32.com/cart32hosting/createwordpresstrial.asp';
} else if (request('wp')=='accountinfo') {
   $URL='https://www.cart32.com/cart32hosting/accountinfo.asp';
} else {
  if (request('action')=='deleteitem') $URL=$URL.'edititem';
  else $URL=$URL.'additem';
}

$sBody='';
$i=0;
foreach($_POST as $name => $value) {
  if ($i>0) $sBody.='&';
  $sBody.=$name.'='.urlencode($value);
  $i++;
}
//echo "URL=$URL, sBody=$sBody";
echo do_post_request($URL,$sBody);

function do_post_request($url, $postdata)  {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
  curl_setopt($ch, CURLOPT_HEADER, 0 );
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata );
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
  curl_setopt($ch,CURLOPT_USERAGENT,'Cart32WordPress');
  $page = curl_exec($ch);
  curl_close($ch);
  return $page;
}
function request($s) {  if (isset($_REQUEST[$s])) { return $_REQUEST[$s]; }  else if (isset($_SERVER[strtoupper($s)])) { return $_SERVER[strtoupper($s)]; }  else { return ''; }}
?>
