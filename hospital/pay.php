<?php
$paystackPublicKey = "pk_test_3f58bc8fc39d9fb757b796b969c0653ecd3e016f"; // Load your public key from environment or config
$email = "kvngjohnny10@gmail.com"; // Set the user email dynamically if needed
$amount = 5000; // Set the amount dynamically if needed
?>

<form id="paymentForm">
  <input type="email" id="email-address" value="<?php echo $email; ?>" placeholder="Email" required readonly />
  <input type="number" id="amount" value="<?php echo $amount; ?>" placeholder="Amount" required readonly />
  <button type="button" onclick="payWithPaystack()">Pay Now</button>
</form>

<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
  function payWithPaystack() {
    var handler = PaystackPop.setup({
      key: '<?php echo $paystackPublicKey; ?>', // Use PHP to output the public key
      email: '<?php echo $email; ?>', // Use PHP to output the email
      amount: <?php echo $amount * 100; ?>, // Amount in kobo (PHP to JS)
      currency: 'NGN',
      callback: function(response) {
        // Redirect to PHP verification script with reference
        window.location.href = 'verify-payment.php?reference=' + response.reference;
      },
      onClose: function() {
        alert('Transaction was not completed.');
      }
    });
    handler.openIframe();
  }
</script>
