<?php
//Form Tambah Data
include 'config.php';

if (isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $price = (float)$_POST['price'];

    // --- FILE UPLOAD LOGIC ---
    $image = $_FILES['cover_image']['name'];
    $target = 'uploads/' . basename($image);

    // Pastikan folder 'uploads/' sudah ada dan bisa ditulisi (writable)
    if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $target)) {
        
        // --- INSERT DATA ---
        $sql = "INSERT INTO books (title, author, price, image) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssds", $title, $author, $price, $image);
        
        if (mysqli_stmt_execute($stmt)) {
            header('Location: index.php');
            exit;
        } else {
            echo 'Error: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);

    } else {
        echo 'File upload failed!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Buku</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f7f9fc; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .form-container { max-width: 500px; width: 90%; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
        h2 { color: #28a745; border-bottom: 2px solid #e0e0e0; padding-bottom: 15px; margin-bottom: 25px; font-size: 1.8em; font-weight: 600; text-align: center; }
        
        label { display: block; margin-bottom: 5px; font-weight: 500; color: #555; }
        input[type="text"], input[type="number"], input[type="file"] { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        
        .btn { display: inline-block; padding: 10px 15px; border: none; border-radius: 5px; font-weight: 600; cursor: pointer; text-decoration: none; margin-right: 10px; color: white; }
        .btn-save { background-color: #28a745; }
        .btn-save:hover { background-color: #218838; }
        .btn-cancel { background-color: #6c757d; color: white; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Tambah Buku Baru</h2>
        <form method='POST' enctype='multipart/form-data'>
            <label>Judul:</label>
            <input type='text' name='title' required><br>
            
            <label>Penulis:</label>
            <input type='text' name='author' required><br>
            
            <label>Harga:</label>
            <input type='number' step='0.01' name='price' required><br>
            
            <label>Cover (Image):</label>
            <input type='file' name='cover_image' required><br>
            
            <button type='submit' name='submit' class="btn btn-save">Simpan</button>
            <a href="index.php" class="btn btn-cancel">Batal</a>
        </form>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>