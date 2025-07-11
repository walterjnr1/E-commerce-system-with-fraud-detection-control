<?php
include('../inc/config.php');

$ref = isset($_GET['reference']) ? $_GET['reference'] : '';
$amount = isset($_GET['amount']) ? $_GET['amount'] : '';

if ($ref == "") {
    header("Location:javascript://history.go(-1)");
    //exit;
}

$curl = curl_init();

$secretkey = htmlspecialchars($row_website['paystack_secret_key']);

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/". rawurlencode($ref),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer {$secretkey}",
        "Cache-Control: no-cache",
    ),
));

$response = curl_exec($curl);

curl_close($curl);
//echo $response;
$json_output = json_decode($response, true);

  
  //start creating event
  if ($json_output['status'] == true && $json_output['data']['status'] == 'success') {
            $stmt = $conn->prepare("INSERT INTO events (manager_id,category_id , paid) VALUES ( ?,1, 1)");
            $stmt->bind_param("i", $manager_id);
            if ($stmt->execute()) {
                $event_id = $stmt->insert_id;


                   // Save payment details (PDO)
                    $sql = 'INSERT INTO event_subscription(manager_id, event_id, amount,description, payment_reference, payment_method,payment_status) VALUES(:manager_id, :event_id, :amount,:description, :payment_reference, :payment_method,:payment_status)';
                    $statement = $dbh->prepare($sql);
                    $statement->execute([
                        ':manager_id'   => $manager_id,
                        ':event_id'   => $event_id,
                        ':amount'   => $amount,
                        ':description'   => 'Event subscription',
                        ':payment_reference'   => $ref,
                        ':payment_method'   => 'paystack',
                        ':payment_status'   => $json_output['data']['status']

                    ]);
                }

                //activity log
                $operation = "paid for an event on $current_date";
                log_activity($conn, $manager_id, $role, $operation, $ip_address);
                header("Location: add-event?event_id=$event_id&ref=$ref");
            }
      
        else {
            $error = "Payment verification failed. Please try again.";
        }

?>