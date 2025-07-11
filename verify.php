<?php
/*****************************************************************
 *  Paystack post‑payment verification
 *  ------------------------------------------------------------
 *  –   ?reference=xxxxx   (passed from Paystack callback)
 *  –   Verifies via Paystack API
 *  –   On success:
 *          • payments.status   → captured
 *          • orders.status     → paid
 *          • carts.status      → converted
 *          • product stock     – qty
 *          • e‑mail receipt
 *****************************************************************/
include('inc/email_landing_page.php'); 
include('config.php');

$reference = $_GET['reference'] ?? '';
if (!$reference) { http_response_code(400); exit('Missing reference'); }

/* ---------- 1. verify with Paystack ---------- */
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => "https://api.paystack.co/transaction/verify/{$reference}",
    CURLOPT_HTTPHEADER     => ["Authorization: Bearer {$paystack_secret_key}"],
    CURLOPT_RETURNTRANSFER => true,
]);
$reply = curl_exec($ch);
if (curl_errno($ch)) { exit('Curl error'); }
$data = json_decode($reply, true);
if (!$data['status'] || $data['data']['status'] !== 'success') {
    exit('Transaction not successful');
}

/* ---------- 2. locate payment row ---------- */
$stmt = $conn->prepare(
    "SELECT payments.id, payments.order_id, orders.user_id, orders.total, users.email, users.full_name
     FROM payments 
     JOIN orders ON orders.id = payments.order_id 
     JOIN users  ON users.id  = orders.user_id
     WHERE payments.provider_txn_id = ? LIMIT 1"
);
$stmt->bind_param('s', $reference);
$stmt->execute();
$stmt->store_result();
if (!$stmt->num_rows) { exit('Payment row not found'); }
$stmt->bind_result($payment_id,$order_id,$user_id,$amount,$cust_email,$cust_name);
$stmt->fetch();

/* ---------- 3. update DB in one transaction ---------- */
$conn->begin_transaction();

$conn->query("UPDATE payments SET status='captured' WHERE id={$payment_id}");
$conn->query("UPDATE orders   SET status='paid'     WHERE id={$order_id}");
$conn->query("UPDATE carts    SET status='converted'
               WHERE id = (SELECT cart_id FROM orders WHERE id={$order_id})");

/* deduct stock */
$conn->query("
    UPDATE products p
    JOIN cart_items ci ON ci.product_id = p.id
    JOIN orders o      ON o.cart_id = ci.cart_id
    SET p.stock_qty = p.stock_qty - ci.qty
    WHERE o.id = {$order_id}
");

$conn->commit();

/* ---------- 4. e‑mail receipt ---------- */
$subject = "TechParts – Order #{$order_id} confirmed";
$message = "Hi {$cust_name},\n\nYour payment was successful. "
         . "Reference: {$reference}\nAmount: ₦" . number_format($amount/100,2) . "\n\n"
         . "We’ll ship your items shortly.";
$headers = "From: no‑reply@techparts.test";
mail($cust_email, $subject, $message, $headers);

/* ---------- 5. show thank‑you page ---------- */
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Payment complete</title></head>
<body style="font-family:sans-serif;text-align:center;margin-top:3rem">
    <h1>✅ Payment successful!</h1>
    <p>Thanks for shopping with TechParts.<br>
       Your order number is <strong>#<?= $order_id; ?></strong>.</p>
    <p><a href="index.php">Back to shop</a></p>
</body>
</html>
