<?php

require_once "Auth/OpenID/Consumer.php";
require_once "Auth/OpenID/Store/FileStore.php";
require_once "Auth/OpenID/Extension/AX.php";

session_start();
$store = new Auth_OpenID_Store_FileStore('./tmp');
$consumer = new Auth_OpenID_Consumer($store);
$scriptPath = implode("/", (explode('/', $_SERVER["REQUEST_URI"], -1)));
$response = $consumer->complete('https://' . $_SERVER["SERVER_NAME"] . $scriptPath . '/verify.php');
$authenticated = false;

if ($response->status == Auth_OpenID_SUCCESS) {

    $ax = new Auth_OpenID_Extension_AX_FetchResponse();
    $obj = $ax->fromSuccessResponse($response);
	
	$_SESSION['openid'] = $obj->data;
	$authenticated = true;
	
	echo "<p>User has been authenticated!</p>";

} else {
	
	echo "<p>User has not been authenticated.</p>";
}

if (isset($_GET['popup'])) {

?>

<p><i>This window will be closed in 5 seconds.</i></p>

<script>
	window.opener.location.href = "index.php";
		
	window.setTimeout(function() {
		window.close();
	}, 5000);
</script>

<?php

} else {

	header('Location: index.php');

}