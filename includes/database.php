<?php
$DB = mysqli_connect('localhost', 'root', 'root', 'qodex');

if (!$DB) {
    die("DB CONNECTION FAILED: " . mysqli_connect_error());
}
return $DB;
