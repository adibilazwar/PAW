<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'crud_books';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}
?>