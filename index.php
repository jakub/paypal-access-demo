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
	<title>PayPal Identity PHP Demo</title>
</head>

<body>

<header>
	<h1>PayPal Identity Demo - PHP with Janrain library</h1>
</header>

<section id="login">
	<form action="rp.php" method="post">
		<input type="image" src="https://www.paypal.com/en_US/i/btn/login-with-paypal-button.png" name="submitBtn" id="submitBtn" alt="Log in with PayPal" />
	</form>
</section>

<section id="user">

<?php if (isset($_SESSION['openid'])) { ?>

	<h2>Welcome, <?php echo $_SESSION['openid']['http://axschema.org/namePerson/first'][0] ?></h2>	
	<p><a href="index.php?session=delete">Delete session data</a></p>
	<pre><?php print_r($_SESSION['openid']); ?></pre>
	
<?php } ?>	

</section>

<script src="https://www.paypalobjects.com/js/external/dg.js"></script>

<script>
    var dg = new PAYPAL.apps.DGFlow({ trigger: "submitBtn", expType: "mini" }); 
</script>

</body>
</html>