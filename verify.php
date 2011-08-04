<?php

require_once "Auth/OpenID/Consumer.php";
require_once "Auth/OpenID/Store/FileStore.php";
require_once "Auth/OpenID/Extension/AX.php";
require_once "Auth/OpenID/Extension/PAPE.php";

session_start();
$store = new Auth_OpenID_Store_FileStore('./tmp');
$consumer = new Auth_OpenID_Consumer($store);
$scriptPath = implode("/", (explode('/', $_SERVER["REQUEST_URI"], -1)));
$response = $consumer->complete('https://' . $_SERVER["SERVER_NAME"] . $scriptPath . '/verify.php');
$authenticated = false;

if ($response->status == Auth_OpenID_SUCCESS) {
    $ax = new Auth_OpenID_Extension_AX_FetchResponse();
    $obj = $ax->fromSuccessResponse($response);	
	$_SESSION['openid_ax'] = $obj->data;	
	$pape = Auth_OpenID_PAPE_Response::fromSuccessResponse($response);	
	if ($pape) {
		$_SESSION['openid_pape'] = $pape;	
	}
	$msg = "User has been authenticated!";
} elseif ($response->status == Auth_OpenID_CANCEL) {	
	$msg = "User cancelled authentication.";
} else {	
	$msg = "User has not been authenticated.";
}

if (isset($_GET['popup'])) {

?>

<h1><?php echo $msg; ?></h1>
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