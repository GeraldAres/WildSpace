<?php
include '../database/connection.php';

$result = pg_query($conn, "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");

if (!$result) {
    die("Query failed: " . pg_last_error($conn));
}

echo "<h2>Connected to Supabase!</h2>";

while ($row = pg_fetch_assoc($result)) {
    echo $row['table_name'] . "<br>";
}
?>