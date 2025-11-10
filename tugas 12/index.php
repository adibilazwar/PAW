<?php
include 'config.php';

// --- PENGATURAN SEARCH ---
$search = isset($_GET['search']) ? $_GET['search'] : '';

// --- PENGATURAN PAGINATION ---
$limit = 5; // Batas data per halaman
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit; // Hitung offset untuk LIMIT

// 1. Hitung total records (dengan mempertimbangkan filter search)
$countSql = "SELECT COUNT(*) AS total FROM books WHERE title LIKE '%$search%' OR author LIKE '%$search%'";
$countResult = mysqli_query($conn, $countSql);
$countRow = mysqli_fetch_assoc($countResult);
$total = $countRow['total'];
$pages = ceil($total / $limit);

// 2. Ambil data dengan LIMIT dan OFFSET
$sql = "SELECT * FROM books WHERE title LIKE '%$search%' OR author LIKE '%$search%' ORDER BY created_at DESC LIMIT $start, $limit";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Daftar Buku</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f7f9fc; margin: 0; padding: 20px; line-height: 1.6; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
        h2 { color: #1a73e8; border-bottom: 2px solid #e0e0e0; padding-bottom: 15px; margin-bottom: 25px; font-size: 2em; font-weight: 600; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        
        /* Buttons & Search */
        input[type="text"] { padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: 280px; }
        .btn { display: inline-block; padding: 8px 15px; border: none; border-radius: 5px; text-decoration: none; font-weight: 600; cursor: pointer; transition: background-color 0.3s ease; text-align: center; color: white; margin-left: 10px; }
        .btn-search { background-color: #1a73e8; }
        .btn-search:hover { background-color: #155cb7; }
        .btn-add { background-color: #28a745; }
        .btn-add:hover { background-color: #218838; }

        /* Table Styles */
        table { width: 100%; border-collapse: collapse; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #007bff; color: white; font-weight: 600; font-size: 0.9em; text-transform: uppercase; }
        tr:nth-child(even) { background-color: #fcfcfc; }
        tr:hover td { background-color: #eef2f8; transition: background-color 0.3s ease; }
        
        /* Action Links */
        .actions a { display: inline-block; padding: 4px 8px; border-radius: 4px; text-decoration: none; font-size: 0.9em; margin-right: 5px; font-weight: 500; }
        .actions .edit { background-color: #ffc107; color: black; }
        .actions .delete { background-color: #dc3545; color: white; }
        
        /* Pagination */
        .pagination a { padding: 5px 10px; border: 1px solid #ccc; text-decoration: none; margin: 0 2px; border-radius: 4px; color: #333; }
        .pagination a.active { background-color: #1a73e8; color: white; border-color: #1a73e8; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Daftar Buku</h2>
        
        <div class="topbar">
            <form method='GET'>
                <input type='text' name='search' placeholder='Cari judul atau penulis...' value='<?php echo htmlspecialchars($search); ?>'>
                <button type='submit' class='btn btn-search'>Search</button>
            </form>
            <a href='add.php' class='btn btn-add'>+ Tambah Buku Baru</a>
        </div>
        

        <table border='1' cellpadding='10'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Harga</th>
                    <th>Cover</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($result) == 0): ?>
                <tr><td colspan="6">Tidak ada data ditemukan.</td></tr>
            <?php else: ?>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                    <td><?php echo number_format($row['price'], 2); ?></td>
                    <td><img src='uploads/<?php echo $row['image']; ?>' width='80'></td>
                    <td class="actions">
                        <a href='edit.php?id=<?php echo $row['id']; ?>' class="edit">Edit</a>
                        <a href='delete.php?id=<?php echo $row['id']; ?>' class="delete" onclick="return confirm('Yakin ingin menghapus data ini?')">Delete</a>
                    </td>
                </tr>
                <?php } ?>
            <?php endif; ?>
            </tbody>
        </table>

        <div class="pagination" style="margin-top: 15px; text-align: center;">
        <?php for ($i = 1; $i <= $pages; $i++) { ?>
            <a href='?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>' class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php } ?>
        </div>
    </div>

</body>
</html>
<?php mysqli_close($conn); ?>