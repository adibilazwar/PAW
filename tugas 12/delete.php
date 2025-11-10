<?php
include 'config.php';

$id = $_GET['id'] ?? die('ID tidak ditemukan.');
$id = (int)$id;

// 1. Ambil nama file yang akan dihapus dari server
$sql_select = "SELECT image FROM books WHERE id = $id";
$result = mysqli_query($conn, $sql_select);
$row = mysqli_fetch_assoc($result);
$filename = $row['image'] ?? null;

// 2. Hapus record dari database
$sql_delete = "DELETE FROM books WHERE id = $id";
if (mysqli_query($conn, $sql_delete)) {
    // 3. Hapus file dari folder 'uploads'
    if ($filename && file_exists('uploads/' . $filename)) {
        unlink('uploads/' . $filename);
    }
    header('Location: index.php');
} else {
    echo "Error menghapus record: " . mysqli_error($conn);
}

mysqli_close($conn);
exit;
?>