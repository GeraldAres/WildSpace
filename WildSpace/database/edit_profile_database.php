<?php
$sql = "SELECT 
            user_id,
            firstname,
            lastname,
            email,
            mobile_number,
            gender
        FROM tbluser
        WHERE user_id = $1";

$result = pg_query_params($conn, $sql, [$user_id]);

if (!$result) {
    die("Query failed: " . pg_last_error($conn));
}

if (pg_num_rows($result) == 0) {
    echo "Profile not found.";
    exit();
}

$user = pg_fetch_assoc($result);
?>