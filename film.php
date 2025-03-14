<?php
// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "film_db";

$conn = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Buat tabel jika belum ada
$conn->query("CREATE TABLE IF NOT EXISTS films (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    download_link VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Tambah film baru
if (isset($_POST['add'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $download_link = $_POST['download_link'];

    $stmt = $conn->prepare("INSERT INTO films (title, description, download_link) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $description, $download_link);
    $stmt->execute();
    header("Location: ".$_SERVER['PHP_SELF']);
}

// Hapus film
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM films WHERE id=$id");
    header("Location: ".$_SERVER['PHP_SELF']);
}

// Ambil data film untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM films WHERE id=$id");
    $edit_data = $result->fetch_assoc();
}

// Update film
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $download_link = $_POST['download_link'];

    $stmt = $conn->prepare("UPDATE films SET title=?, description=?, download_link=? WHERE id=?");
    $stmt->bind_param("sssi", $title, $description, $download_link, $id);
    $stmt->execute();
    header("Location: ".$_SERVER['PHP_SELF']);
}

// Ambil semua film
$films = $conn->query("SELECT * FROM films ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Download Film</title>
    <style>
       /* Global Styles */
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background-color: #f4f4f4;
}
.container {
    max-width: 800px;
    margin: auto;
}

/* Form Styling */
.form-container {
    background: #ffffff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}
input, textarea, button {
    width: calc(100% - 24px);
    padding: 12px;
    margin: 8px 0;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 16px;
    box-sizing: border-box;
}
button {
    background: #007bff;
    color: white;
    border: none;
    cursor: pointer;
    transition: background 0.3s;
}
button:hover {
    background: #0056b3;
}

/* Card Layout */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin-top: 20px;
}
.card {
    background: #ffffff;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: transform 0.3s;
    overflow: hidden;
    word-wrap: break-word;
}
.card:hover {
    transform: scale(1.05);
}
.card h3 {
    margin: 0;
    font-size: 18px;
    color: #333;
    word-wrap: break-word;
    overflow-wrap: break-word;
}
.card p {
    font-size: 14px;
    color: #555;
    margin-bottom: 10px;
    word-wrap: break-word;
    overflow-wrap: break-word;
}
.card a {
    text-decoration: none;
    color: white;
    background: #28a745;
    padding: 8px 12px;
    border-radius: 5px;
    display: inline-block;
    margin-top: 10px;
    transition: background 0.3s;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.card a:hover {
    background: #218838;
}

/* Action Buttons */
.actions {
    margin-top: 10px;
}
.actions a {
    margin-right: 10px;
    text-decoration: none;
    font-weight: bold;
    padding: 6px 10px;
    border-radius: 5px;
    transition: background 0.3s;
}
.actions .edit {
    color: white;
    background: #ffc107;
}
.actions .edit:hover {
    background: #e0a800;
}
.actions .delete {
    color: white;
    background: #dc3545;
}
.actions .delete:hover {
    background: #c82333;
}

/* Responsive Design */
@media (max-width: 600px) {
    .container {
        padding: 10px;
    }
    input, textarea, button {
        width: 100%;
    }
}
    </style>
</head>
<body>

<div class="container">
    <h2>CRUD Download Film</h2>

    <!-- Form Tambah & Edit Film -->
    <div class="form-container">
        <form method="POST">
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
            <input type="text" name="title" placeholder="Judul Film" value="<?= $edit_data['title'] ?? '' ?>" required>
            <textarea name="description" placeholder="Deskripsi"><?= $edit_data['description'] ?? '' ?></textarea>
            <input type="text" name="download_link" placeholder="Link Download" value="<?= $edit_data['download_link'] ?? '' ?>" required>
            <?php if ($edit_data): ?>
                <button type="submit" name="update">Update</button>
            <?php else: ?>
                <button type="submit" name="add">Tambah</button>
            <?php endif; ?>
        </form>
    </div>

    <!-- Card Film -->
    <div class="cards">
        <?php while ($film = $films->fetch_assoc()): ?>
        <div class="card">
            <h3><?= $film['title'] ?></h3>
            <p><?= $film['description'] ?></p>
            <a href="<?= $film['download_link'] ?>" target="_blank">Download</a>
            <div class="actions">
                <a href="?edit=<?= $film['id'] ?>">✏ Edit</a>
                <a href="?delete=<?= $film['id'] ?>" onclick="return confirm('Hapus film ini?')">🗑 Hapus</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
