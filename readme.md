Trustico API Wrapper for PHP
============================

Introduction
------------

There's nothing overly complex about this package - it's just a wrapper around the API. Example usage code below.

The following functions are available on the \Trustico\API class:

* test() - Checks to see if the API is working
* hello($message) - A wrapper around the API's Hello method
* order_status($order_id) - A wrapper around the API's GetStatus method
* resend_approver_email($order_id) - A wrapper around the API's ResendEmail method with a type of Approver
* resend_fulfilment_email($order_id) - A wrapper around the API's ResendEmail method with a type of Fulfillmnent
* change_approver_email($order_id, $email) - A wrapper around the API's ChangeApproverEmail method
* reissue($order_id, $email, $csr) - A wrapper around the API's Reissue method
* get_user_agreement($product_name) - A wrapper around the API's GetUserAgreement method
* get_approver_list($domain) - A wrapper around the API's GetApproverList. Returns a \Trustico\Response\XML\ApproverList
* process_order_type_1($data) - A wrapper around the API's ProcessType1 method
* process_order_type_2($data) - A wrapper around the API's ProcessType2 method

Installation
------------

Supports both Composer installation and regular file inclusion. For Composer, add the following to your composer.json:

```Javascript
"require": {
	"phil-lavin/trustico": "dev-master"
}
```

For Composer, the library's classes will autoload if you use Composer's autoloader.

If you don't use Composer, just include src/autoloader.php into your app.

Usage example
-------------

```php
<?php
$api = \Trustico\API::forge('user', 'pass');

$response = $api->hello("This is a test message");

if (!$response->is_success()) {
	die("Error...\n".$response->get_errors_string()."\n");
}

foreach ($response->get_data() as $k=>$v) {
        var_dump("$k => $v");
}
```

Docs
----

It's largely self documenting cause it's just a wrapper.

See the \Trustico\API class for the methods you can call. They're an exact mapping to the API methods documented at
https://resellers.trustico.com/geodirect/admin/api-overview.php. You need to be logged into a Reseller account to see the
Trustico docs.

All the methods should be self explanitory. process_order_type_1() and process_order_type_2() take an array of data keyed
by the field names described in the Trustico API docs.

All methods will return a \Trustico\Response\XML\General except get_approver_list() which returns a \Trustico\Response\XML\ApproverList.
The ApproverList response implements a get_emails() generator method (for you to foreach over) which parses the slightly silly
Trustico API response into a list of e-mail addresses.
