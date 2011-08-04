# paypal-identity-demo

A simple demo application that uses the JanRain PHP OpenID library to authenticate against PayPal's Identity service.

## Installation

This application doesn't have many requirements, merely pitfalls and gotchas.

* PHP 5.3 with extensions:

 - GMP (preferred) or BCMath
 - CURL
 - DOM
 - A suitable backing store for associations (mysqli, memcached, etc. If not, disk will do.)

By default, the application is configured for file storage, using the `tmp` directory. Your web server should be allowed to write to this directory.

*(I didn't realise until later that the JanRain PHP library isn't maintained anymore. So it's been updated to use https://github.com/mouns/php-openid by https://github.com/nathanaelle instead.)*

## URL Whitelisting

You'll need to obtain a whitelisted URL by submitting an application request at https://www.x.com/create-appvetting-app!input.jspa

* Your openid.realm must be in the form of https://www.domain.com - don't include a trailing slash, port number, path, or anything else

* HTTPS realms are recommended, and required for advanced information. HTTP realms are only allowed to request basic information (firstname, lastname, email)

## Windows

If using CURL to make HTTP requests, please ensure your system knows about common certificate roots. When testing with WAMP, the CURL library doesn't understand PayPal's SSL certificate and chokes. If CURL is disabled, it'll use `fsockopen` or something equally terrible.

There's also this delightful modification to `Auth/OpenID/CryptUtil.php`, as php-openid will also complain about a lack of `/dev/urandom`.

```php
if (strpos(strtoupper(php_uname('s')), 'WIN') !== false) {
    define('Auth_OpenID_RAND_SOURCE',  null);
} else {
    define('Auth_OpenID_RAND_SOURCE', '/dev/urandom');
}
```