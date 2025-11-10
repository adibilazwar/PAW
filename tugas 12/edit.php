<?php
include 'config.php';

$id = $_GET['id'] ?? ($_POST['id'] ?? die('ID tidak ditemukan.'));
$id = (int)$id;

// --- PROSES UPDATE (Ketika form disubmit) ---
if (isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $price = (float)$_POST['price'];
    $old_image = $_POST['old_image'];
    $new_image = $old_image; // Default image is the old one

    // --- LOGIC FILE REPLACEMENT ---
    if ($_FILES['cover_image']['error'] === 0 && $_FILES['cover_image']['name'] !== '') {
        // New file uploaded, process it
        $new_image = $_FILES['cover_image']['name'];
        $target = 'uploads/' . basename($new_image);
        
        if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $target)) {
            // Delete old file if upload successful
            if ($old_image && file_exists('uploads/' . $old_image)) {
                unlink('uploads/' . $old_image);
            }
        } else {
            echo 'File upload failed for new image!';
            exit;
        }
    }
    
    // --- UPDATE DATA ---
    $sql = "UPDATE books SET title = ?, author = ?, price = ?, image = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssdsi", $title, $author, $price, $new_image, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        header('Location: index.php');
        exit;
    } else {
        echo 'Error: ' . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// --- AMBIL DATA LAMA UNTUK FORM ---
$sql = "SELECT * FROM books WHERE id = $id";
$result = mysqli_query($conn, $sql);
$book = mysqli_fetch_assoc($result);

if (!$book) {
    die("Data buku tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Buku</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f7f9fc; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .form-container { max-width: 500px; width: 90%; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
        h2 { color: #ff9800; border-bottom: 2px solid #e0e0e0; padding-bottom: 15px; margin-bottom: 25px; font-size: 1.8em; font-weight: 600; text-align: center; }
        
        label { display: block; margin-bottom: 5px; font-weight: 500; color: #555; }
        input[type="text"], input[type="number"], input[type="file"] { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        
        .current-cover { margin-bottom: 15px; border: 1px solid #ddd; padding: 10px; border-radius: 5px; text-align: center; }
        
        .btn { display: inline-block; padding: 10px 15px; border: none; border-radius: 5px; font-weight: 600; cursor: pointer; text-decoration: none; margin-right: 10px; color: white; }
        .btn-update { background-color: #007bff; }
        .btn-update:hover { background-color: #0056b3; }
        .btn-cancel { background-color: #6c757d; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Buku: <?php echo htmlspecialchars($book['title']); ?></h2>
        <form method='POST' enctype='multipart/form-data'>
            <input type="hidden" name="id" value="<?php echo $book['id']; ?>">
            <input type="hidden" name="old_image" value="<?php echo $book['image']; ?>">

            <label>Judul:</label>
            <input type='text' name='title' value="<?php echo htmlspecialchars($book['title']); ?>" required><br>
            
            <label>Penulis:</label>
            <input type='text' name='author' value="<?php echo htmlspecialchars($book['author']); ?>" required><br>
            
            <label>Harga:</label>
            <input type='number' step='0.01' name='price' value="<?php echo htmlspecialchars($book['price']); ?>" required><br>
            
            <div class="current-cover">
                Cover Saat Ini: <br><img src='uploads/<?php echo $book['image']; ?>' width='80'>
            </div>
            
            <label>Ganti Cover (Optional):</label>
            <input type='file' name='cover_image'><br>
            
            <button type='submit' name='submit' class="btn btn-update">Update</button>
            <a href="index.php" class="btn btn-cancel">Batal</a>
        </form>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>