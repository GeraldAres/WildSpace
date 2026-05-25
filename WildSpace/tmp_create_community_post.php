<?php
include __DIR__ . '/database/connection.php';
$sql = "CREATE TABLE IF NOT EXISTS public.tblcommunity_post (
    post_id BIGSERIAL PRIMARY KEY,
    student_id BIGINT NOT NULL REFERENCES tblstudent(student_id) ON DELETE CASCADE,
    reservation_id BIGINT NOT NULL REFERENCES tblreservation(reservation_id) ON DELETE CASCADE,
    rating INTEGER NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP WITHOUT TIME ZONE DEFAULT NOW()
);";
$result = pg_query($conn, $sql);
if (!$result) {
    echo 'ERROR: ' . pg_last_error($conn) . PHP_EOL;
    exit(1);
}
echo 'Created tblcommunity_post successfully.' . PHP_EOL;
