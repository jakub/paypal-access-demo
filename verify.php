<?php

require_once "Auth/OpenID/Consumer.php";
require_once "Auth/OpenID/FileStore.php";
require_once "Auth/OpenID/AX.php";

session_start();
$store = new Auth_OpenID_FileStore('./tmp');
$consumer = new Auth_OpenID_Consumer($store);
$response = $consumer->complete('https://identity.jakub.me/openid/verify.php');

if ($response->status == Auth_OpenID_SUCCESS) {

    $ax = new Auth_OpenID_AX_FetchResponse();
    $obj = $ax->fromSuccessResponse($response);
	
	$_SESSION['openid'] = $obj->data;
	
	echo "<p>User has been authenticated!</p>";

} else {
	echo "<p>User has not been authenticated.</p>";
}

?>

<p><i>This window will be closed in 5 seconds.</i></p>

<script>
	window.opener.location.href = "index.php";
		
	window.setTimeout(function() {
		window.close();
	}, 5000);
</script>