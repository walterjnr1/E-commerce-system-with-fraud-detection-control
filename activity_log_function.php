<?php
function log_activity($conn,$user_id, $role,$operation) {
   
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id,role, operation, ip_address) VALUES (?,?,? ,?)");
    $stmt->bind_param("ssss", $user_id, $role, $operation, $ip_address);
    $stmt->execute();
    $stmt->close();
    
}
?>