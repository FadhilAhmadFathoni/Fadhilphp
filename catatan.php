<?php
// Konfigurasi database
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'catatan_db';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Inisialisasi variabel $catatan untuk form
$catatan = ['id' => '', 'judul' => '', 'deskripsi' => '', 'link' => ''];

// Jika sedang mengedit, ambil data yang sesuai
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM catatan WHERE id = $id");
    if ($result->num_rows > 0) {
        $catatan = $result->fetch_assoc();
    }
}

// Tambah catatan
if (isset($_POST['tambah'])) {
    $judul = $conn->real_escape_string($_POST['judul']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $link = !empty($_POST['link']) ? $conn->real_escape_string($_POST['link']) : NULL;
    $conn->query("INSERT INTO catatan (judul, deskripsi, link) VALUES ('$judul', '$deskripsi', '$link')");
    header("Location: index.php");
    exit;
}

// Edit catatan
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $judul = $conn->real_escape_string($_POST['judul']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $link = !empty($_POST['link']) ? $conn->real_escape_string($_POST['link']) : NULL;
    $conn->query("UPDATE catatan SET judul='$judul', deskripsi='$deskripsi', link='$link' WHERE id=$id");
    header("Location: index.php");
    exit;
}

// Hapus catatan
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM catatan WHERE id=$id");
    header("Location: index.php");
    exit;
}

// Ambil semua catatan
$result = $conn->query("SELECT * FROM catatan");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan Saya</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            text-align: center;
            margin: 20px;
        }
        .container {
            width: 80%;
            margin: auto;
        }
        .card {
            background: #ffffff;
            padding: 20px;
            margin: 10px auto;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: left;
        }
        .card a {
            color: #007bff;
            text-decoration: none;
        }
        .card a:hover {
            text-decoration: underline;
        }
        .form-container {
            background: #ffffff;
            padding: 20px;
            margin: auto;
            width: 80%;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        input, textarea, button {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Catatan Saya</h2>
    <div class="form-container">
        <form method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($catatan['id']) ?>">
            <input type="text" name="judul" placeholder="Judul" value="<?= htmlspecialchars($catatan['judul']) ?>" required>
            <textarea name="deskripsi" placeholder="Deskripsi" required><?= htmlspecialchars($catatan['deskripsi']) ?></textarea>
            <input type="text" name="link" placeholder="Link (Opsional)" value="<?= htmlspecialchars($catatan['link']) ?>">
            <button type="submit" name="<?= $catatan['id'] ? 'edit' : 'tambah' ?>">Simpan</button>
        </form>
    </div>
    <div class="container">
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">
            <h3><?= htmlspecialchars($row['judul']) ?></h3>
            <p><?= htmlspecialchars($row['deskripsi']) ?></p>
            <?php if ($row['link']): ?>
                <p><a href="<?= htmlspecialchars($row['link']) ?>" target="_blank">Buka Link</a></p>
            <?php endif; ?>
            <p>
                <a href="?edit=<?= $row['id'] ?>">Edit</a> |
                <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus?');">Hapus</a>
            </p>
        </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
