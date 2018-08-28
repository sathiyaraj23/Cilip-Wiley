<?php
$url = 'https://onlinelibrary.wiley.com/login-proxy-tps?targetURL=https://onlinelibrary.wiley.com/resolve/journal/doi?DOI=10.1111/(ISSN)1471-1842&domain=CILIPHILJ&debug=true';

$curlsession = curl_init($url);
curl_setopt($curlsession, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curlsession, CURLOPT_CONNECTTIMEOUT, 20); // 20 for timeout
$result = curl_exec($curlsession);
if (!$result) {
   $error = curl_error($curlsession);
   die ("CURL error: $error");
}
curl_close($curlsession);
$url = 'Location: ' . $result;
header($url);
 
?>
