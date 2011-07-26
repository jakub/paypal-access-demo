<?php

session_start();

if (isset($_GET['session']) && $_GET['session'] === 'delete') {
	$_SESSION = array();

	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		);
	}

	session_destroy();	
	header("Location: index.php");
}

?>
<!doctype html>
<html>

<head>
	<meta charset="utf-8">
	<title>PayPal Identity Demo - PHP with Janrain library</title>
	<link rel="stylesheet" href="style.css">
</head>

<body>

<h1>PayPal Identity Demo</h1>
<p id="subtitle">A <a href="https://github.com/jakub/paypal-identity-demo">PHP example</a> using the <a href="http://www.janrain.com/openid-enabled">open-source Janrain library</a>.</p>

<div id="login">
	<form action="rp.php" method="post">
		<input type="image" src="https://www.paypal.com/en_US/i/btn/login-with-paypal-button.png" name="submitBtn" id="submitBtn" alt="Log in with PayPal" />
	</form>
</div>

<?php if (isset($_SESSION['openid'])) { ?>

	<div id="user">
	<h2>Welcome, <?php echo $_SESSION['openid']['http://axschema.org/namePerson/first'][0] ?></h2>	
	<p><a href="index.php?session=delete">Delete session data</a></p>
	
	<table>
	
	<?php 	
		$c = 0;
		foreach ($_SESSION['openid'] as $key => $value) {
			if ($value) {
				echo "<tr class='" . (($c++%2==1) ? 'odd' : 'even') . "'><td>" . $key . "</td><td>" . $value[0] . "</td></tr>";
			} else {
				echo "<tr class='" . (($c++%2==1) ? 'odd' : 'even') . "'><td>" . $key . "</td><td></td></tr>";
			}
		}	
	?>	
	</table>
	</div>
	
<?php } ?>	



<script src="https://www.paypalobjects.com/js/external/identity.js"></script>

<script>
    var identity = new PAYPAL.apps.IdentityFlow({ trigger: "submitBtn" }); 
</script>

</body>
</html>