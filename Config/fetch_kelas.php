<?php
session_start();
require 'db.php';

if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit();
}

$userData = $_SESSION['user_data'];
$username = $userData['username'];

$conn = getDbConnection($userData['host'], $userData['userdb'], $userData['passdb'], $userData['dbname']);

if (isset($_POST['sekolah'])) {
    $sekolah = $_POST['sekolah'];

    // Kolom dinamis dari user_data
    $jenjang = $userData['jenjang'];
    $tables = $userData['siswa'];
    $sekolahColumn = $userData['sekolah'];
    $kelasColumn = $userData['kelas'];

    // Membuat query dinamis berdasarkan kondisi jenjang dan kelas
    if (!empty($jenjang) && !empty($kelasColumn)) {
        // Jika jenjang dan kelas keduanya ada, gabungkan keduanya
        $query = "
            SELECT DISTINCT 
                CONCAT($jenjang, ' ', $kelasColumn) AS kelas_display,
                $kelasColumn AS kelas_value
            FROM $tables 
            WHERE $sekolahColumn = ? 
            ORDER BY kelas_display DESC
        ";
    } elseif (!empty($jenjang)) {
        // Jika hanya jenjang yang ada
        $query = "
            SELECT DISTINCT 
                $jenjang AS kelas_display,
                $jenjang AS kelas_value
            FROM $tables 
            WHERE $sekolahColumn = ? 
            ORDER BY kelas_display DESC
        ";
    } elseif (!empty($kelasColumn)) {
        // Jika hanya kelas yang ada
        $query = "
            SELECT DISTINCT 
                $kelasColumn AS kelas_display,
                $kelasColumn AS kelas_value
            FROM $tables 
            WHERE $sekolahColumn = ? 
            ORDER BY kelas_display DESC
        ";
    } else {
        // Jika keduanya tidak ada
        echo '<option value="">Tidak ada kelas tersedia</option>';
        exit();
    }

    // Siapkan statement dan jalankan query
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $sekolah);
    $stmt->execute();
    $result = $stmt->get_result();

    // Output opsi kelas
    echo '<option value="">Pilih Kelas</option>';
    while ($row = $result->fetch_assoc()) {
        // Tampilkan kombinasi jenjang dan kelas
        // Kirim hanya nilai kelas
        echo '<option value="' . htmlspecialchars($row['kelas_value']) . '">' 
             . htmlspecialchars($row['kelas_display']) . '</option>';
    }

    $stmt->close();
}
?>