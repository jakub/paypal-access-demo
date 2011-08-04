<?php

/*
	Janrain AX example

	modified from http://stackoverflow.com/questions/1183788/example-usage-of-ax-in-php-openid
	originally by http://stackoverflow.com/users/52888/glen
*/

$oid_identifier = 'https://www.paypal.com/webapps/auth/server';

require_once "Auth/OpenID/Consumer.php";
require_once "Auth/OpenID/Store/FileStore.php";
require_once "Auth/OpenID/Extension/AX.php";

session_start();

// Create file storage area for OpenID data
// The Janrain library also supports databases, memcache, etc.
$store = new Auth_OpenID_Store_FileStore('./tmp');

$consumer = new Auth_OpenID_Consumer($store);
$auth = $consumer->begin($oid_identifier);

// Required AX attributes to request
// PayPal will not return attributes marked as optional

$attribute[] = Auth_OpenID_Extension_AX_AttrInfo::make('http://axschema.org/namePerson/first', 1, 1);
$attribute[] = Auth_OpenID_Extension_AX_AttrInfo::make('http://axschema.org/namePerson/last', 1, 1);
$attribute[] = Auth_OpenID_Extension_AX_AttrInfo::make('http://schema.openid.net/contact/fullname', 1, 1);

$attribute[] = Auth_OpenID_Extension_AX_AttrInfo::make('http://axschema.org/contact/email', 1, 1);
$attribute[] = Auth_OpenID_Extension_AX_AttrInfo::make('http://axschema.org/contact/phone/default', 1, 1);

// default billing address
$attribute[] = Auth_OpenID_Extension_AX_AttrInfo::make('http://schema.openid.net/contact/street1', 1, 1);
$attribute[] = Auth_OpenID_Extension_AX_AttrInfo::make('http://schema.openid.net/contact/street2', 1, 1);
$attribute[] = Auth_OpenID_Extension_AX_AttrInfo::make('http://axschema.org/contact/city/home', 1, 1);
$attribute[] = Auth_OpenID_Extension_AX_AttrInfo::make('http://axschema.org/contact/state/home', 1, 1);
$attribute[] = Auth_OpenID_Extension_AX_AttrInfo::make('http://axschema.org/contact/postalCode/home', 1, 1);
$attribute[] = Auth_OpenID_Extension_AX_AttrInfo::make('http://axschema.org/contact/country/home', 1, 1);

// e.g. en_GB
$attribute[] = Auth_OpenID_Extension_AX_AttrInfo::make('http://axschema.org/pref/language', 1, 1);

// e.g. Europe/London
$attribute[] = Auth_OpenID_Extension_AX_AttrInfo::make('http://axschema.org/pref/timezone', 1, 1);

// PayPal specific attributes
$attribute[] = Auth_OpenID_Extension_AX_AttrInfo::make('https://www.paypal.com/webapps/auth/schema/verifiedAccount', 1, 1);
$attribute[] = Auth_OpenID_Extension_AX_AttrInfo::make('https://www.paypal.com/webapps/auth/schema/payerID', 1, 1);

$ax = new Auth_OpenID_Extension_AX_FetchRequest;

foreach($attribute as $attr){
    $ax->add($attr);
}

$auth->addExtension($ax);

$scriptPath = implode("/", (explode('/', $_SERVER["REQUEST_URI"], -1)));

if (isset($_GET['popup'])) {
	$returnScript = $scriptPath . '/verify.php?popup=true';
} else {
	$returnScript = $scriptPath . '/verify.php';
}

$url = $auth->redirectURL('https://' . $_SERVER["SERVER_NAME"], 'https://' . $_SERVER["SERVER_NAME"] . $returnScript);
header('Location: ' . $url);