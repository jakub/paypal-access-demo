<?php

require_once "Auth/OpenID/Extension/PAPE.php";

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
	<title>PayPal Access Demo - PHP with Janrain library</title>
	<link rel="stylesheet" href="style.css">
</head>

<body>

<h1>PayPal Access Demo</h1>

<div class="aside">
	<p>A <a href="https://github.com/jakub/paypal-access-demo">PHP example</a> using the <a href="http://www.janrain.com/openid-enabled">open-source Janrain library</a>.</p>
	<p>Note: you must have JavaScript enabled.</p>
</div>

<div id="login">
	<form action="rp.php?popup=true" method="post">
		<label for="submit_with_popup">Use popup flow</label>
		<input type="image" src="https://www.paypal.com/en_US/i/btn/login-with-paypal-button.png" name="submit_with_popup" id="submit_with_popup" alt="Log in with PayPal" />
	</form>

	<form action="rp.php" method="post" id="form_without_popup">
		<label for="submit_without_popup">Use inline flow</label>
		<input type="image" src="https://www.paypal.com/en_US/i/btn/login-with-paypal-button.png" name="submit_without_popup" id="submit_without_popup" alt="Log in with PayPal" />
	</form>
</div>

<?php if (isset($_SESSION['openid_ax'])) { ?>

	<div id="user">
	<h2>Welcome, <?php echo htmlentities($_SESSION['openid_ax']['http://axschema.org/namePerson/first'][0]); ?></h2>	
	<p class="aside"><a href="index.php?session=delete">Delete session data</a></p>
	
	<table>
	
	<?php 	
		$c = 0;
		foreach ($_SESSION['openid_ax'] as $key => $value) {
			if ($value) {
				echo "<tr class='" . (($c++%2==1) ? 'odd' : 'even') . "'><td>" . htmlentities($key) . "</td><td>" . htmlentities($value[0]) . "</td></tr>";
			} else {
				echo "<tr class='" . (($c++%2==1) ? 'odd' : 'even') . "'><td>" . htmlentities($key) . "</td><td></td></tr>";
			}
		}
		
		
	?>
	</table>
	<?php
		
	// check PAPE as well
	
	if (isset($_SESSION['openid_pape'])) {	
		$pape = $_SESSION['openid_pape'];
		
		// check specific policies
		if ($pape->auth_policies) {
			$c = 0;
			echo "<h3>PAPE policies used</h3><table>";
			foreach ($pape->auth_policies as $uri) {
				$escaped_uri = htmlentities($uri);
				echo "<tr class='" . (($c++%2==1) ? 'odd' : 'even') . "'><td colspan='2'>$escaped_uri</td></tr>";
			}
			echo "</table>";
		} else {
			echo "<tr><td colspan='2'>No PAPE policies were applied during authentication.</td></tr>";
		}
		
		if ($pape->auth_time) {
			$age = htmlentities($pape->auth_time);
			echo "<h3>Last authentication time</h3><table>";
			echo"<tr><td colspan='2'>The authentication age returned by the server is: $age</td></tr>";
			echo "</table>";
		}		
	
	}
	
	?>		
	
	</table>
	</div>
	
<?php } ?>	


<script src="https://www.paypalobjects.com/js/external/identity.js"></script>

<script>	
	var identity  = new PAYPAL.apps.IdentityFlow({ trigger: "submit_with_popup" });
</script>

</body>
</html>