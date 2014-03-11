<?php  defined('C5_EXECUTE') or die(_("Access Denied."));


$body .= "========== ORDER INFORMATION ==========\n";
$body .= "Order #: ".$invoiceNumber."\n";
$body .= "Total: ".$totalAmount."\n";
$body .= "Payment Method: ".$paymentMethod."\n";
foreach($products as $i => $product) {
	$keyValues = array();
	foreach($product['attributes'] as $key => $attr) {
		$keyValues[] = "{$key}: {$attr}";
	}
	
	$body .="Product #".($i+1).": {$product['name']}";
	if($keyValues) {
		$body .= ' ('.implode(',', $keyValues).')';
	}
	$body .= "/{$product['quantity']} @ {$product['price']}\n";
}
for ($i = 0; $i < count($adjustments); $i++) {
	$body .= "Adjustment #" . ($i+1) . ": " . $adjustments[$i]['name'] . "/" . $adjustments[$i]['total'] . "\n";
}
$body .= "\n";

if (isset($shipping) || isset($shipping_attrs)) {
    $body .= "========= BILLING INFORMATION =========\n";
} else {
    $body .= "==== BILLING/SHIPPING INFORMATION =====\n";
}
$body .= "First Name: " . $billing['first_name'] . "\n";
$body .= "Last Name: " . $billing['last_name'] . "\n";
$body .= "Email: " . $billing['email'] . "\n";
$body .= "Address1: " . $billing['address1'] . "\n";
$body .= "Address2: " . $billing['address2'] . "\n";
$body .= "City: " . $billing['city'] . "\n";
$body .= "State/Province: " . $billing['state'] . "\n";
$body .= "Zip/Postal Code: " . $billing['zip'] . "\n";
$body .= "Country: " . $billing['country'] . "\n";
$body .= "Phone: " . $billing['phone'] . "\n";
if (isset($billing_attrs)) {
	foreach ($billing_attrs as $key => $val) {
		$body .= "$key: $val\n";
	}
}
$body .= "\n";

if (isset($shipping) || isset($shipping_attrs)) {
	if (isset($shipping)) {
    	$body .= "======== SHIPPING INFORMATION =========\n";
		$body .= "First Name: " . $shipping['first_name'] . "\n";
		$body .= "Last Name: " . $shipping['last_name'] . "\n";
		$body .= "Email: " . $shipping['email'] . "\n";
		$body .= "Address1: " . $shipping['address1'] . "\n";
		$body .= "Address2: " . $shipping['address2'] . "\n";
		$body .= "City: " . $shipping['city'] . "\n";
		$body .= "State/Province: " . $shipping['state'] . "\n";
		$body .= "Zip/Postal Code: " . $shipping['zip'] . "\n";
		$body .= "Country: " . $shipping['country'] . "\n";
		$body .= "Phone: " . $shipping['phone'] . "\n";
	}
	if (isset($shipping_attrs)) {
		foreach ($shipping_attrs as $key => $val) {
			$body .= "$key: $val\n";
		}
	}
}
