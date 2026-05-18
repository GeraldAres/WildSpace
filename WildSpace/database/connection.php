<?php
$host = "aws-1-ap-northeast-1.pooler.supabase.com";
$port = "5432";
$database = "postgres";
$user = "postgres.boatsdexavegockgigvp";
$password = "Wildspace2026!!";

$conn = pg_connect("host=$host port=$port dbname=$database user=$user password=$password sslmode=require");

if (!$conn) {
    die("Supabase database connection failed.");
}
?>
