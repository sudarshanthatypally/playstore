<?php
header('Content-Type: application/json');

$user_id = $_GET['u_id'];

    $status_url = 'https://epikoregalapi.com/EPIKO/playstore/rest.php?applicationKey=12345&methodName=user.fbUnlinkAccount&id='.$user_id; // URL to track the deletion
    $confirmation_code = $user_id; // unique code for the deletion request
    $data = array(
      'url' => $status_url,
      'confirmation_code' => $confirmation_code
    );
    echo json_encode($data);
?>
