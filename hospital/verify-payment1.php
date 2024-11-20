<?php
$reference = $_GET['reference'];
if(!$reference) {
  die("No reference supplied");
}

$secretKey = "sk_test_88bdcabb6b395c12d8272a61020c0e95280028db"; // Replace with your secret key
$url = "https://api.paystack.co/transaction/verify/" . rawurlencode($reference);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $secretKey]);

$response = curl_exec($ch);
if(curl_errno($ch)) {
  die("Curl returned an error: " . curl_error($ch));
}
curl_close($ch);

$result = json_decode($response, true);

if($result && $result["status"] && $result["data"]["status"] == "success") {
  // Payment was successful
  echo "Payment successful!";
  
} else {
  // Payment failed
  echo "Payment failed.";
}
?>
