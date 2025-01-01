<?php
session_start();
require_once '../../Config/db.php';
require_once '../../Config/wa.php';
require_once '../../Config/messages.php';

$current_page = basename($_SERVER['PHP_SELF']);

// if (!isset($_SESSION['logged_in'])) {
//     header('Location: login.php');
//     exit();
// }


// $userData = $_SESSION['user_data'];
// $username = $userData['username'];
// $conn = getDbConnection($userData['host'], $userData['userdb'], $userData['passdb'], $userData['dbname']);
// $mainConn = getDbConnection('localhost', 'root', '', 'apiwa');


// $userData = $_SESSION['user_data'];
// $username = $userData['username'];
$tbname = $userData['tbname'];
$tagihan = $userData['tagihan'];
$tgl_tagihan = $userData['tanggal_tagihan'];
$tgl_lunas = $userData['tanggal_lunas'];
$lunas = $userData['lunas'];

$recordsPerPage = 6; // Number of records to display per page
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1; // Get the current page from the URL
$offset = ($currentPage - 1) * $recordsPerPage;


$queryMaster = "SELECT * FROM master_setting ORDER BY id DESC LIMIT $offset, $recordsPerPage";
$resultMaster = $mainConn->query($queryMaster);

// Fetch the total number of records
$totalRecordsQuery = "SELECT COUNT(*) AS total_records FROM master_setting";
$totalRecordsResult = $mainConn->query($totalRecordsQuery);
$totalRecords = $totalRecordsResult->fetch_assoc()['total_records'];

// Calculate the total number of pages
$totalPages = ceil($totalRecords / $recordsPerPage);

$queryRoles = "SELECT DISTINCT roles FROM master_setting";
$resultRoles = $mainConn->query($queryRoles);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tambahdata'])) {
    $roles = trim($_POST['roles']);
    $usernamelog = trim($_POST['username']);
    $pass = trim($_POST['password']);
    $hashed_password = password_hash($pass, PASSWORD_BCRYPT);
    $projectName = trim($_POST['project-name']);
    $userdb = trim($_POST['userdb']);
    $passdb = trim($_POST['passdb']);
    $dbName = trim($_POST['dbname']);
    $host = trim($_POST['host']);
    $siswa = isset($_POST['siswa']) ? trim($_POST['siswa']) : '';
    $kelas = isset($_POST['kelas']) ? trim($_POST['kelas']) : '';
    $angkatanAd = isset($_POST['angkatan']) ? trim($_POST['angkatan']) : '';
    $phoneColumn = isset($_POST['phone-column']) ? trim($_POST['phone-column']) : '';
    $namaSiswacolumn = isset($_POST['namasiswa-column']) ? trim($_POST['namasiswa-column']) : '';
    $idSiswa = isset($_POST['idsiswa']) ? trim($_POST['idsiswa']) : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $nowa = isset($_POST['nowa']) ? trim($_POST['nowa']) : '';
    $jenjang = isset($_POST['jenjang']) ? trim($_POST['jenjang']) : '';
    $tanggalTagihan = isset($_POST['tanggal-tagihan']) ? trim($_POST['tanggal-tagihan']) : '';
    $tagihanAd = isset($_POST['tagihan']) ? trim($_POST['tagihan']) : '';
    $tanggalLunas = isset($_POST['tanggal-lunas']) ? trim($_POST['tanggal-lunas']) : '';
    $tLunas = isset($_POST['lunas']) ? trim($_POST['lunas']) : '';
    $tTabName = isset($_POST['tagihantabname']) ? trim($_POST['tagihantabname']) : '';
    $idTagihan = isset($_POST['idtagihan']) ? trim($_POST['idtagihan']) : '';
    $idSiswaTagihan = isset($_POST['idsiswatagihan']) ? trim($_POST['idsiswatagihan']) : '';
    $nameTagihan = isset($_POST['nametagihan']) ? trim($_POST['nametagihan']) : '';
    $sekolahAd = isset($_POST['sekolah']) ? trim($_POST['sekolah']) : '';
    $waApiAd = isset($_POST['waapi']) ? trim($_POST['waapi']) : '';
    $waNumberAd = isset($_POST['wanumber']) ? trim($_POST['wanumber']) : '';




    $queryInsert = "INSERT INTO master_setting (roles, username, password, project_name, userdb, passdb, dbname, host, siswa, kelas, angkatan, phone_column,nama_siswa_column, id_siswa, name, nowa, jenjang, tanggal_tagihan, tagihan,tanggal_lunas, lunas, tbname, id_tagihan, id_siswa_tagihan, name_tagihan, sekolah, wa_apikey, wa_numberkey) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $mainConn->prepare($queryInsert);

    $stmt->bind_param(
        "ssssssssssssssssssssssssssss",
        $roles,
        $usernamelog,
        $hashed_password,
        $projectName,
        $userdb,
        $passdb,
        $dbName,
        $host,
        $siswa,
        $kelas,
        $angkatanAd,
        $phoneColumn,
        $namaSiswacolumn,
        $idSiswa,
        $name,
        $nowa,
        $jenjang,
        $tanggalTagihan,
        $tagihanAd,
        $tanggalLunas,
        $tLunas,
        $tTabName,
        $idTagihan,
        $idSiswaTagihan,
        $nameTagihan,
        $sekolahAd,
        $waApiAd,
        $waNumberAd
    );

    if ($stmt->execute()) {
        $_SESSION['toast_message'] = "Data berhasil ditambahkan!";
        $_SESSION['toast_type'] = "success";

        header("Location: admin.php?page=1");
        exit();
    } else {
        $_SESSION['toast_message'] = "Data gagal ditambahkan!";
        $_SESSION['toast_type'] = "failed";

        $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'admin.php';
        header("Location: " . $redirect);

        error_log("Error updating record: " . $conn->error);
    }
}

//edit
$queryMasterEdit = "SELECT * FROM master_setting ORDER BY id DESC LIMIT $offset, $recordsPerPage";
$resultMasterEdit = $mainConn->query($queryMasterEdit);
if ($resultMasterEdit) { // Memastikan query berhasil
    if ($resultMasterEdit->num_rows > 0) {
        $currentRow = $resultMasterEdit->fetch_assoc();

        // Menampilkan semua opsi dari master_setting
        $queryRolesEdit = "SELECT DISTINCT roles FROM master_setting";
        $resultRolesEdit = $mainConn->query($queryRolesEdit);
    }
} else {
    // Tangani kesalahan jika query gagal
    echo "Error: " . $mainConn->error; // Menampilkan pesan kesalahan
}



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ubahdata'])) {
    $roles = trim($_POST['rolesedit']);
    $usernamelog = trim($_POST['usernameedit']);
    $projectName = trim($_POST['project-nameedit']);
    $id_mstr = isset($_POST['id_mstr']) ? trim($_POST['id_mstr']) : '';
     // Ambil password lama dari database
     $query = "SELECT password FROM master_setting WHERE id = ?";
     $stmt = $mainConn->prepare($query);
     $stmt->bind_param("i", $id_mstr);
     $stmt->execute();
     $result = $stmt->get_result();
     $row = $result->fetch_assoc();
     
     // Ambil password lama
     $hashed_passwordEd= $row['password']; // Password lama
 
     // Cek apakah passwordedit diisi
     if (isset($_POST['passwordedit']) && !empty(trim($_POST['passwordedit']))) {
         // Jika diisi, enkripsi password baru
         $hashed_passwordEd = password_hash(trim($_POST['passwordedit']), PASSWORD_BCRYPT);
     }
    $userdb = trim($_POST['userdbedit']);
    $passdb = trim($_POST['passdbedit']);
    $dbName = trim($_POST['dbnameedit']);
    $host = trim($_POST['hostedit']);
    $siswa = isset($_POST['siswaedit']) ? trim($_POST['siswaedit']) : '';
    $kelas = isset($_POST['kelasedit']) ? trim($_POST['kelasedit']) : '';
    $angkatanAd = isset($_POST['angkatanedit']) ? trim($_POST['angkatanedit']) : '';
    $phoneColumn = isset($_POST['phone-columnedit']) ? trim($_POST['phone-columnedit']) : '';
    $namaSiswacolumn = isset($_POST['namasiswa-columnedit']) ? trim($_POST['namasiswa-columnedit']) : '';
    $idSiswa = isset($_POST['idsiswaedit']) ? trim($_POST['idsiswaedit']) : '';
    $name = isset($_POST['nameedit']) ? trim($_POST['nameedit']) : '';
    $nowa = isset($_POST['nowaedit']) ? trim($_POST['nowaedit']) : '';
    $jenjang = isset($_POST['jenjangedit']) ? trim($_POST['jenjangedit']) : '';
    $tanggalTagihan = isset($_POST['tanggal-tagihanedit']) ? trim($_POST['tanggal-tagihanedit']) : '';
    $tagihanAd = isset($_POST['tagihanedit']) ? trim($_POST['tagihanedit']) : '';
    $tanggalLunas = isset($_POST['tanggal-lunasedit']) ? trim($_POST['tanggal-lunasedit']) : '';
    $tLunas = isset($_POST['lunasedit']) ? trim($_POST['lunasedit']) : '';
    $tTabName = isset($_POST['tagihantabnameedit']) ? trim($_POST['tagihantabnameedit']) : '';
    $idTagihan = isset($_POST['idtagihanedit']) ? trim($_POST['idtagihanedit']) : '';
    $idSiswaTagihan = isset($_POST['idsiswatagihanedit']) ? trim($_POST['idsiswatagihanedit']) : '';
    $nameTagihan = isset($_POST['nametagihanedit']) ? trim($_POST['nametagihanedit']) : '';
    $sekolahAd = isset($_POST['sekolahedit']) ? trim($_POST['sekolahedit']) : '';
    $waApiAd = isset($_POST['waapiedit']) ? trim($_POST['waapiedit']) : '';
    $waNumberAd = isset($_POST['wanumberedit']) ? trim($_POST['wanumberedit']) : '';




    $queryUpdate = "UPDATE master_setting SET
                roles = ?, 
                username = ?, 
                password = ?, 
                project_name = ?,
                userdb = ?,
                passdb = ?,
                dbname = ?,
                host = ?, 
                siswa = ?, 
                kelas = ?, 
                angkatan = ?, 
                phone_column = ?, 
                nama_siswa_column = ?, 
                id_siswa = ?,
                name = ?, 
                nowa = ?, 
                jenjang = ?, 
                tanggal_tagihan = ?, 
                tagihan = ?, 
                tanggal_lunas = ?, 
                lunas = ?, 
                tbname = ?, 
                id_tagihan = ?, 
                id_siswa_tagihan = ?, 
                name_tagihan = ?, 
                sekolah = ?,
                wa_apikey = ?,
                wa_numberkey = ?
            WHERE id = ?";

    $stmt = $mainConn->prepare($queryUpdate);

    $stmt->bind_param(
        "sssssssssssssssssssssssssssss",
        $roles,
        $usernamelog,
        $hashed_passwordEd,
        $projectName,
        $userdb,
        $passdb,
        $dbName,
        $host,
        $siswa,
        $kelas,
        $angkatanAd,
        $phoneColumn,
        $namaSiswacolumn,
        $idSiswa,
        $name,
        $nowa,
        $jenjang,
        $tanggalTagihan,
        $tagihanAd,
        $tanggalLunas,
        $tLunas,
        $tTabName,
        $idTagihan,
        $idSiswaTagihan,
        $nameTagihan,
        $sekolahAd,
        $waApiAd,
        $waNumberAd,
        $id_mstr
    );

    if ($stmt->execute()) {
        $_SESSION['toast_message'] = "Data berhasil diupdate!";
        $_SESSION['toast_type'] = "success";

        header("Location: admin.php");
        exit();
        // Reset form
        $_POST = array();
    } else {
        $_SESSION['toast_message'] = "Data gagal diupdate!";
        $_SESSION['toast_type'] = "failed";

        $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'admin.php';
        header("Location: " . $redirect);
    }
}



$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/img/Logo_512.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link href="../src/output.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>SendWa</title>
</head>

<body class="bg-gray-100">
    <div class="flex">
        <!-- asidebar -->
        <aside id="sidebar" class="fixed top-0 left-0 w-14 min-h-screen bg-repeat-y transition-all transform ease-in-out duration-300 z-40 sm:w-60" style="background-image: url('../../assets/img/2151554909.jpg');">
            <div class="p-4 text-white bg-sky-900">
                <div class="flex flex-col items-center sm:flex-row">
                    <img class="w-6 h-6 sm:w-16 sm:h-16 rounded-full" src="../../assets/img/1.png" alt="Profile Picture">
                    <div class="mt-2 text-center sm:ml-4">
                        <h4 class="block w-11/12 font-semibold text-xs truncate text-wrap sm:text-base sm:text-start"><?php echo htmlspecialchars($username); ?> (admin)</h4>
                        <div class="flex items-center space-x-2">
                            <div class="hidden sm:block sm:w-2 sm:h-2 sm:bg-green-500 sm:rounded-full"></div>
                            <p class="hidden sm:block sm:text-green-500 ">Online</p>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <nav class="mt-10 sm:mt-20">
                <div class="flex items-center justify-between p-3 cursor-pointer" id="menu-button">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class=" hidden sm:block w-7 h-7" fill="#ffffff" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>

                        <span class="hidden sm:block ml-2 text-cyan-50 from-neutral-300">Master Setting</span>
                    </div>
                </div>
                <ul class="ml-2 text-gray-300">
                    <li class="relative group px-3 sm:ml-12 py-2 mt-4 mb-4 hover:bg-white hover:text-black <?php echo ($current_page == 'admin.php') ? 'bg-white underline underline-offset-8 rounded-l text-sky-800' : ''; ?>">
                        <a href="admin.php" class="flex items-center transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="sm:w-7 sm:h-7 w-5 h-5 mr-2" fill="#ffffff" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>

                            <span class="hidden sm:block">Setting</span>
                            <!-- Tooltip -->
                        </a>
                        <div class="sm:hidden absolute left-full top-1/2 ml-3 -translate-y-1/2 px-2 py-1 text-sm text-white bg-black rounded-md opacity-0 group-hover:opacity-100 transition-opacity">
                            Setting
                        </div>
                    </li>

                </ul>
                <div class="mt-12 sm:mt-2">
                    <a href="../logout.php" class="relative group flex items-center px-4 py-2 text-white hover:bg-white hover:text-black">
                        <svg class="w-5 h-5 sm:w-4 sm:h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12h-6m6 0l-3-3m3 3l-3 3m-6-6h7.5M5.25 6h-2.25C2.56 6 1.875 6.621 1.875 7.5v9c0 .879.684 1.5 1.125 1.5h2.25" />
                        </svg>
                        <span class="hidden sm:block">Logout</span>
                        <div class="sm:hidden absolute left-full top-1/2 ml-3 -translate-y-1/2 px-2 py-1 text-sm text-white bg-black rounded-md opacity-0 group-hover:opacity-100 transition-opacity">
                            Logout
                        </div>
                    </a>
                </div>
            </nav>
        </aside>

        <div id="main-content" class="flex-1 transition-all duration-500 ml-14 sm:ml-60">
            <!-- Top -->
            <div id="top-bar" class="flex justify-between items-center mb-1 bg-sky-800 min-h-14 shadow-sm shadow-slate-400 fixed top-0 left-0 right-0 z-20 sm:ml-60">
                <div class="flex items-center justify-center space-x-3 ml-14 sm:ml-0">
                    <svg id='hamburger' class="hidden sm:block ml-2 w-6 h-6 text-gray-300 cursor-pointer" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <img class="h-12 w-auto" src="../../assets/img/Logo_512.png" alt="Your Company">
                    <span class="text-lg font-bold text-white ml-4 sm:ml-0">Send WhatsApp - Admin</span>
                </div>
            </div>
            <!-- header -->
            <header id="header-bar" class="flex justify-between items-center mb-5 min-h-14 bg-slate-50 shadow-sm shadow-slate-400 fixed top-14 left-0 right-0 z-20 sm:ml-60">
                <h1 id="header-title" class="ml-20 sm:ml-10 text-lg font-semibold">Setting</h1>
            </header>

            <!-- main -->
            <main class="container mx-auto px-4 py-4 mt-28">
                <div class="grid grid-cols-1 sm:grid-cols-1 gap-6">
                    <!-- Form Card 1 -->
                    <div class="bg-white h-dvh shadow-md rounded-lg p-6 overflow-auto">
                        <div class="sm:text-xl text-lg font-bold mb-4 flex items-center px-2 py-2 rounded-md shadow-md text-white bg-green-700 w-7/12 sm:w-2/12 cursor-pointer" id="open-modal">
                            <p class="ml-4">Tambah Data</p>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 ml-2 hover:text-green-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <!-- Modal -->
                        <div id="modal" class="fixed inset-0 z-10 flex items-center ml-64 mt-12 h-dvh mx-2 justify-center opacity-0 transition-opacity duration-300 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                            <div class="relative transform overflow-hidden rounded-lg w-3/5 h-4/5 bg-white text-left shadow-xl transition-transform duration-300 scale-95 overflow-y-auto">
                                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                            <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Tambah Data</h3>
                                            <form action="" method="post" id="tambahdataform">
                                                <div class="mt-2">
                                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-20">
                                                        <div class="flex-row">
                                                            <label for="roles" class="block text-gray-700 font-medium mb-2 after:content-['*'] after:text-pink-500">Pilih roles :</label>
                                                            <select name="roles" id="roles" class="block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-200">
                                                                <option value="">Pilih Roles</option>';
                                                                <?php
                                                                if ($resultRoles->num_rows > 0) {
                                                                    while ($row = $resultRoles->fetch_assoc()) {
                                                                        echo ' <option value="' . $row["roles"] . '">' . $row["roles"] . '</option>';
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="username" class="block text-gray-700 font-medium mb-2 after:content-['*'] after:text-pink-500">Username :</label>
                                                            <div class="mt-2">
                                                                <input id="username" name="username" required type="text" autocomplete="username" placeholder="username..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                <p id="username-error" class="mt-1 text-sm text-red-600 hidden">Username telah digunakan</p>
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <div class="flex items-center justify-between">
                                                                <label for="password" class="block text-sm font-medium leading-6 text-gray-900 after:content-['*'] after:text-pink-500">Password :</label>
                                                            </div>
                                                            <div class="relative mt-2">
                                                                <input id="password" name="password" required type="password" placeholder="***" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-w00 sm:text-sm sm:leading-6">
                                                                <span class="toggle-password absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" data-target="password">
                                                                    <svg class="eye-open h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                                    </svg>
                                                                    <svg class="eye-closed h-5 w-5 text-gray-500 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="project-name" class="block text-gray-700 font-medium mb-2 after:content-['*'] after:text-pink-500">Project name (Format : KotaNasabah_NamaNasabah):</label>
                                                            <div class="mt-2">
                                                                <input id="project-name" name="project-name" required type="text" placeholder="projectname...." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                <p id="projectname-error" class="mt-1 text-sm text-red-600 hidden">Project name telah digunakan!</p>
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="userdb" class="block text-gray-700 font-medium mb-2 after:content-['*'] after:text-pink-500">User db :</label>
                                                            <div class="mt-2">
                                                                <input id="userdb" name="userdb" required type="text" placeholder="userdb..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <div class="flex items-center justify-between">
                                                                <label for="passdb" class="block text-sm font-medium leading-6 text-gray-900">Pass db :</label>
                                                            </div>
                                                            <div class="relative mt-2">
                                                                <input id="passdb" name="passdb" type="password" placeholder="passdb..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-w00 sm:text-sm sm:leading-6">
                                                                <span class="toggle-password absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" data-target="passdb">
                                                                    <svg class="eye-open h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                                    </svg>
                                                                    <svg class="eye-closed h-5 w-5 text-gray-500 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                                    </svg>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <div class="flex items-center justify-between">
                                                                <label for="dbname" class="block text-sm font-medium leading-6 text-gray-900 after:content-['*'] after:text-pink-500">Db name :</label>
                                                            </div>
                                                            <div class="mt-2">
                                                                <input id="dbname" name="dbname" required type="text" placeholder="dbname..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-w00 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="host" class="block text-gray-700 font-medium mb-2 after:content-['*'] after:text-pink-500">Host :</label>
                                                            <div class="mt-2">
                                                                <input id="host" name="host" required type="text" placeholder="host..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="siswa" class="block text-gray-700 font-medium mb-2">Siswa :</label>
                                                            <div class="mt-2">
                                                                <input id="siswa" name="siswa" type="text" placeholder="siswa..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="kelas" class="block text-gray-700 font-medium mb-2">Kelas :</label>
                                                            <div class="mt-2">
                                                                <input id="kelas" name="kelas" type="text" placeholder="kelas..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="angkatan" class="block text-gray-700 font-medium mb-2">Angkatan :</label>
                                                            <div class="mt-2">
                                                                <input id="angkatan" name="angkatan" type="text" placeholder="angkatan..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="phone-column" class="block text-gray-700 font-medium mb-2">Phone column :</label>
                                                            <div class="mt-2">
                                                                <input id="phone-column" name="phone-column" type="text" placeholder="phonecolumn..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="namasiswa-column" class="block text-gray-700 font-medium mb-2">Nama siswa column :</label>
                                                            <div class="mt-2">
                                                                <input id="namasiswa-column" name="namasiswa-column" type="text" placeholder="namasiswacolumn..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="idsiswa" class="block text-gray-700 font-medium mb-2">Id siswa :</label>
                                                            <div class="mt-2">
                                                                <input id="idsiswa" name="idsiswa" type="text" placeholder="idsiswa..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="name" class="block text-gray-700 font-medium mb-2">Name :</label>
                                                            <div class="mt-2">
                                                                <input id="name" name="name" type="text" placeholder="name..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="nowa" class="block text-gray-700 font-medium mb-2">Nowa :</label>
                                                            <div class="mt-2">
                                                                <input id="nowa" name="nowa" type="number" placeholder="089122232..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="jenjang" class="block text-gray-700 font-medium mb-2">Jenjang :</label>
                                                            <div class="mt-2">
                                                                <input id="jenjang" name="jenjang" type="text" placeholder="jenjang..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="tanggal-tagihan" class="block text-gray-700 font-medium mb-2">Tanggal tagihan :</label>
                                                            <div class="mt-2">
                                                                <input id="tanggal-tagihan" name="tanggal-tagihan" type="text" placeholder="tanggaltagihan..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="tagihan" class="block text-gray-700 font-medium mb-2">Tagihan :</label>
                                                            <div class="mt-2">
                                                                <input id="tagihan" name="tagihan" type="text" placeholder="tagihan..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="tanggal-lunas" class="block text-gray-700 font-medium mb-2">Tanggal lunas :</label>
                                                            <div class="mt-2">
                                                                <input id="tanggal-lunas" name="tanggal-lunas" type="text" placeholder="tanggallunas..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="lunas" class="block text-gray-700 font-medium mb-2">Lunas :</label>
                                                            <div class="mt-2">
                                                                <input id="lunas" name="lunas" type="text" placeholder="lunas..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="tagihantabname" class="block text-gray-700 font-medium mb-2">Tagihantab name :</label>
                                                            <div class="mt-2">
                                                                <input id="tagihantabname" name="tagihantabname" type="text" placeholder="tagihantabname..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="idtagihan" class="block text-gray-700 font-medium mb-2">Id tagihan :</label>
                                                            <div class="mt-2">
                                                                <input id="idtagihan" name="idtagihan" type="text" placeholder="idtagihan..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="idsiswatagihan" class="block text-gray-700 font-medium mb-2">Id siswa tagihan :</label>
                                                            <div class="mt-2">
                                                                <input id="idsiswatagihan" name="idsiswatagihan" type="text" placeholder="idsiswatagihan..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="nametagihan" class="block text-gray-700 font-medium mb-2">Name tagihan :</label>
                                                            <div class="mt-2">
                                                                <input id="nametagihan" name="nametagihan" type="text" placeholder="nametagihan..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="sekolah" class="block text-gray-700 font-medium mb-2">Sekolah :</label>
                                                            <div class="mt-2">
                                                                <input id="sekolah" name="sekolah" type="text" placeholder="sekolah..." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="waapi" class="block text-gray-700 font-medium mb-2">wa api key :</label>
                                                            <div class="mt-2">
                                                                <input id="waapi" name="waapi" type="text" placeholder="waapi.." class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                        <div class="flex-row">
                                                            <label for="wanumber" class="block text-gray-700 font-medium mb-2">wa number key :</label>
                                                            <div class="mt-2">
                                                                <input id="wanumber" name="wanumber" type="text" placeholder="wanumber" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-10">
                                                    <p class="text-sm text-gray-500">Apakah Anda yakin ingin menambahkan data ini?</p>
                                                </div>
                                                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                                    <button type="submit" class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:ml-3 sm:w-auto" name="tambahdata">Tambah</button>
                                                    <button type="button" id="close-modal" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <p class="mb-4 italic">Noted*: Pastikan semua kolom diisi!</p>
                        <div class="mb-1 flex justify-between items-center">
                            <button id="prevPage" class="bg-gray-300 text-gray-800 py-2 px-4 rounded disabled:opacity-50" <?php echo $currentPage == 1 ? 'disabled' : ''; ?>>Previous</button>
                            <span id="pageInfo" class="text-gray-800">Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></span>
                            <button id="nextPage" class="bg-gray-300 text-gray-800 py-2 px-4 rounded disabled:opacity-50" <?php echo $currentPage == $totalPages ? 'disabled' : ''; ?>>Next</button>
                        </div>
                        <div class="mb-4">
                            <div class="block w-full p-2 max-h-screen border border-gray-300 rounded-sm overflow-y-auto">
                                <table id="masterTable" class="min-w-full bg-white">
                                    <thead>
                                        <tr class="bg-teal-800 text-white sticky top-0">
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold"></th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">No.</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Roles</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Username</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Password</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Project_name</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Userdb</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Passdb</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Dbname</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Host</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Siswa</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Kelas</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Angkatan</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Phone_column</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">NamaSiswa_column</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">IdSiswa</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Name</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Nowa</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Jenjang</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">TanggalTagihan</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Tagihan</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">TanggalLunas</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Lunas</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Tagihantabname</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">idTagihan</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">idSiswaTagihan</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">NamaTagihan</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Sekolah</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">WA_api key</th>
                                            <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">WA_number key</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dataMasterTable">
                                        <?php
                                        if ($resultMaster->num_rows > 0) {
                                            $no = 1 + $offset;
                                            while ($row = $resultMaster->fetch_assoc()) {
                                        ?>
                                                <tr>
                                                    <td class='py-2 px-4 border-b border-gray-300'>
                                                        <div class='flex items-center px-1 py-1 rounded-md shadow-md  text-yellow-600 cursor-pointer' id="open-modal-edit<?= $row['id'] ?>" data-id='<?= $row['id'] ?>'>
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                            </svg>
                                                        </div>
                                                    </td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= $no++ ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= $row["roles"] ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= $row["username"] ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= $row["password"] ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= $row["project_name"] ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= $row["userdb"] ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= $row["passdb"] ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= $row["dbname"] ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= $row["host"] ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= $row["siswa"] ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= !empty($row["kelas"]) ? $row["kelas"] : "-" ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= !empty($row["angkatan"]) ? $row["angkatan"] : "-" ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= $row["phone_column"] ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= !empty($row["nama_siswa_column"]) ? $row["nama_siswa_column"] : "-" ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= $row["id_siswa"] ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= !empty($row["name"]) ? $row["name"] : "-" ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= !empty($row["nowa"]) ? $row["nowa"] : "-" ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= !empty($row["jenjang"]) ? $row["jenjang"] : "-" ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= !empty($row["tanggal_tagihan"]) ? $row["tanggal_tagihan"] : "-" ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= !empty($row["tagihan"]) ? $row["tagihan"] : "-" ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= !empty($row["tanggal_lunas"]) ? $row["tanggal_lunas"] : "-" ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= !empty($row["lunas"]) ? $row["lunas"] : "-" ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= !empty($row["tbname"]) ? $row["tbname"] : "-" ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= !empty($row["id_tagihan"]) ? $row["id_tagihan"] : "-" ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= !empty($row["id_siswa_tagihan"]) ? $row["id_siswa_tagihan"] : "-" ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= !empty($row["name_tagihan"]) ? $row["name_tagihan"] : "-" ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= !empty($row["sekolah"]) ? $row["sekolah"] : "-" ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= !empty($row["wa_apikey"]) ? $row["wa_apikey"] : "-" ?></td>
                                                    <td class='py-2 px-4 border-b border-gray-300'><?= !empty($row["wa_numberkey"]) ? $row["wa_numberkey"] : "-" ?></td>
                                                </tr>

                                                <div id="modal-edit<?= $row['id'] ?>" class="fixed inset-0 z-10 flex items-center ml-64 mt-12 h-dvh mx-2 justify-center opacity-0 transition-opacity duration-300 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                                                    <div class="relative transform overflow-hidden rounded-lg w-3/5 h-4/5 bg-white text-left shadow-xl transition-transform duration-300 scale-95 overflow-y-auto">
                                                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                                            <div class="sm:flex sm:items-start">
                                                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                                                    <h3 class="text-lg font-semibold text-gray-900" id="modal-title">Update Data</h3>
                                                                    <form action="" method="POST" id="ubahdataform">
                                                                        <input type="hidden" name="id_mstr" value="<?= $row['id'] ?>">
                                                                        <div class="mt-2">
                                                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-20">
                                                                                <div class="flex-row">
                                                                                    <label for="rolesedit" class="block text-gray-700 font-medium mb-2 after:content-['*'] after:text-pink-500">Pilih roles :</label>
                                                                                    <select name="rolesedit" id="rolesedit" class="block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-200">
                                                                                        <?php
                                                                                        $queryRoles = "SELECT DISTINCT roles FROM master_setting";
                                                                                        $resultRoles = $mainConn->query($queryRoles);
                                                                                        // Cek apakah opsi ini adalah yang saat ini
                                                                                        while ($role = $resultRoles->fetch_assoc()) {
                                                                                            $selected = ($row['roles'] == $role['roles']) ? 'selected' : '';
                                                                                            echo '<option value="' . $role['roles'] . '" ' . $selected . '>' . $role['roles'] . '</option>';
                                                                                        }

                                                                                        ?>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="usernameedit" class="block text-gray-700 font-medium mb-2 after:content-['*'] after:text-pink-500">Username :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="usernameedit" name="usernameedit" required type="text" autocomplete="username" placeholder="username..."
                                                                                            value="<?= htmlspecialchars($row['username']); ?>"
                                                                                            class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                        <p id="username-erroredit" class="mt-1 text-sm text-red-600 hidden">Username telah digunakan</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <div class="flex items-center justify-between">
                                                                                        <label for="password_lama" class="block text-sm font-medium leading-6 text-gray-900 after:content-['*'] after:text-pink-500">Password Saat Ini :</label>
                                                                                    </div>
                                                                                    <div class="mt-2">
                                                                                        <input id="password_lama" name="password_lama" type="text" value="<?= htmlspecialchars($row['password']); ?>" disabled class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-w00 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                    <div class="flex items-center justify-between">
                                                                                        <label for="passwordedit" class="block text-sm font-medium leading-6 text-gray-900">Password Baru :</label>
                                                                                    </div>
                                                                                    <div class="mt-2">
                                                                                        <input id="passwordedit" name="passwordedit" type="password" placeholder="***" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-w00 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>

                                                                                <div class="flex-row">
                                                                                    <label for="project-nameedit" class="block text-gray-700 font-medium mb-2 after:content-['*'] after:text-pink-500">Project name (Format : KotaNasabah_NamaNasabah):</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="project-nameedit" name="project-nameedit" required type="text" placeholder="projectname...." value="<?= htmlspecialchars($row['project_name']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                        <p id="projectnameedit-error" class="mt-1 text-sm text-red-600 hidden">Project name telah digunakan!</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="userdbedit" class="block text-gray-700 font-medium mb-2 after:content-['*'] after:text-pink-500">User db :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="userdbedit" name="userdbedit" required type="text" placeholder="userdb..." value="<?= htmlspecialchars($row['userdb']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <div class="flex items-center justify-between">
                                                                                        <label for="passdbedit" class="block text-sm font-medium leading-6 text-gray-900">Pass db :</label>
                                                                                    </div>
                                                                                    <div class="mt-2">
                                                                                        <input id="passdbedit" name="passdbedit" type="password" placeholder="passdb..." value="<?= htmlspecialchars($row['passdb']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-w00 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <div class="flex items-center justify-between">
                                                                                        <label for="dbnameedit" class="block text-sm font-medium leading-6 text-gray-900 after:content-['*'] after:text-pink-500">Db name :</label>
                                                                                    </div>
                                                                                    <div class="mt-2">
                                                                                        <input id="dbnameedit" name="dbnameedit" required type="text" placeholder="dbname..." value="<?= htmlspecialchars($row['dbname']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-w00 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="hostedit" class="block text-gray-700 font-medium mb-2 after:content-['*'] after:text-pink-500">Host :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="hostedit" name="hostedit" required type="text" placeholder="host..." value="<?= htmlspecialchars($row['host']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="siswaedit" class="block text-gray-700 font-medium mb-2">Siswa :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="siswaedit" name="siswaedit" type="text" placeholder="siswa..." value="<?= htmlspecialchars($row['siswa']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="kelasedit" class="block text-gray-700 font-medium mb-2">Kelas :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="kelasedit" name="kelasedit" type="text" placeholder="kelas..." value="<?= htmlspecialchars($row['kelas']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="angkatanedit" class="block text-gray-700 font-medium mb-2">Angkatan :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="angkatanedit" name="angkatanedit" type="text" placeholder="angkatan..." value="<?= htmlspecialchars($row['angkatan']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="phone-columnedit" class="block text-gray-700 font-medium mb-2">Phone column :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="phone-columnedit" name="phone-columnedit" type="text" placeholder="phonecolumn..." value="<?= htmlspecialchars($row['phone_column']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="namasiswa-columnedit" class="block text-gray-700 font-medium mb-2">Nama siswa column :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="namasiswa-columnedit" name="namasiswa-columnedit" type="text" placeholder="namasiswacolumn..." value="<?= htmlspecialchars($row['nama_siswa_column']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="idsiswaedit" class="block text-gray-700 font-medium mb-2">Id siswa :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="idsiswaedit" name="idsiswaedit" type="text" placeholder="idsiswa..." value="<?= htmlspecialchars($row['id_siswa']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="nameedit" class="block text-gray-700 font-medium mb-2">Name :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="nameedit" name="nameedit" type="text" placeholder="name..." value="<?= htmlspecialchars($row['name']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="nowaedit" class="block text-gray-700 font-medium mb-2">Nowa :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="nowaedit" name="nowaedit" type="number" placeholder="089122232..." value="<?= htmlspecialchars($row['nowa']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="jenjangedit" class="block text-gray-700 font-medium mb-2">Jenjang :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="jenjangedit" name="jenjangedit" type="text" placeholder="jenjang..." value="<?= htmlspecialchars($row['jenjang']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="tanggal-tagihanedit" class="block text-gray-700 font-medium mb-2">Tanggal tagihan :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="tanggal-tagihanedit" name="tanggal-tagihanedit" type="text" placeholder="tanggaltagihan..." value="<?= htmlspecialchars($row['tanggal_tagihan']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="tagihanedit" class="block text-gray-700 font-medium mb-2">Tagihan :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="tagihanedit" name="tagihanedit" type="text" placeholder="tagihan..." value="<?= htmlspecialchars($row['tagihan']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="tanggal-lunasedit" class="block text-gray-700 font-medium mb-2">Tanggal lunas :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="tanggal-lunasedit" name="tanggal-lunasedit" type="text" placeholder="tanggallunas..." value="<?= htmlspecialchars($row['tanggal_lunas']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="lunasedit" class="block text-gray-700 font-medium mb-2">Lunas :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="lunasedit" name="lunasedit" type="text" placeholder="lunas..." value="<?= htmlspecialchars($row['lunas']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="tagihantabnameedit" class="block text-gray-700 font-medium mb-2">Tagihantab name :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="tagihantabnameedit" name="tagihantabnameedit" type="text" placeholder="tagihantabname..." value="<?= htmlspecialchars($row['tbname']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="idtagihanedit" class="block text-gray-700 font-medium mb-2">Id tagihan :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="idtagihanedit" name="idtagihanedit" type="text" placeholder="idtagihan..." value="<?= htmlspecialchars($row['id_tagihan']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="idsiswatagihanedit" class="block text-gray-700 font-medium mb-2">Id siswa tagihan :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="idsiswatagihanedit" name="idsiswatagihanedit" type="text" placeholder="idsiswatagihan..." value="<?= htmlspecialchars($row['id_siswa_tagihan']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="nametagihanedit" class="block text-gray-700 font-medium mb-2">Name tagihan :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="nametagihanedit" name="nametagihanedit" type="text" placeholder="nametagihan..." value="<?= htmlspecialchars($row['name_tagihan']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="sekolahedit" class="block text-gray-700 font-medium mb-2 after:content-['*'] after:text-pink-500">Sekolah :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="sekolahedit" name="sekolahedit" required type="text" placeholder="sekolah..." value="<?= htmlspecialchars($row['sekolah']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="waapiedit" class="block text-gray-700 font-medium mb-2">wa api key :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="waapiedit" name="waapiedit" type="text" placeholder="waapikey..." value="<?= htmlspecialchars($row['wa_apikey']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex-row">
                                                                                    <label for="wanumberedit" class="block text-gray-700 font-medium mb-2">wa number key :</label>
                                                                                    <div class="mt-2">
                                                                                        <input id="wanumberedit" name="wanumberedit" type="text" placeholder="wanumberkey..." value="<?= htmlspecialchars($row['wa_numberkey']); ?>" class="block w-full rounded-md border-0 py-1.5 px-2 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-200 sm:text-sm sm:leading-6">
                                                                                    </div>
                                                                                </div>


                                                                            </div>
                                                                        </div>
                                                                        <div class="mt-10">
                                                                            <p class="text-sm text-gray-500">Apakah Anda yakin ingin mengubah data ini?</p>
                                                                        </div>
                                                                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                                                            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:ml-3 sm:w-auto" name="ubahdata">Update</button>
                                                                            <button type="button" id="close-modal-edit" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                        <?php
                                            }
                                        }
                                        ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- alert notif -->
                    <div class="fixed top-4 right-4 z-50">
                        <?php
                        if (isset($_SESSION['toast_message'])) {
                            $toastMessage = $_SESSION['toast_message'];
                            $toastType = $_SESSION['toast_type'];
                            $bgColor = '';

                            switch ($toastType) {
                                case 'success':
                                    $bgColor = 'bg-green-500';
                                    $icon = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>';
                                    break;
                                case 'failed':
                                    $bgColor = 'bg-red-500';
                                    break;
                                case 'warning':
                                    $bgColor = 'bg-yellow-500';
                                    $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                            </svg>';
                                    break;
                            }
                            echo '<div id="toastMessage" class="fixed top-4 right-4 ' . $bgColor . ' text-white px-4 py-3 rounded-md flex items-center space-x-4 min-w-64 max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl">';

                            // Tampilkan ikon hanya jika ada
                            if (!empty($icon)) {
                                echo $icon;
                            }

                            echo '<div class="flex-1">
                                    <span class="font-bold">' . ucfirst($toastType) . '</span>
                                    <p class="text-sm">' . $toastMessage . '</p>
                                </div>
                                <button class="ml-auto focus:outline-none" onclick="this.parentElement.remove();">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>';
                            unset($_SESSION['toast_message']);
                            unset($_SESSION['toast_type']);
                        }
                        ?>
                    </div>
                </div>
            </main>


        </div>



    </div>

    <script>
        const modal = document.getElementById('modal');
        // const modaledit = document.getElementById('modal-edit');
        const openModalButton = document.getElementById('open-modal');
        // const openModalEditButton = document.getElementById('open-modal-edit');
        const closeModalButton = document.getElementById('close-modal');
        // const closeModalEditButton = document.getElementById('close-modal-edit');
        const prevPageBtn = document.getElementById('prevPage');
        const nextPageBtn = document.getElementById('nextPage');
        const pageInfo = document.getElementById('pageInfo');

        let currentPage = <?php echo $currentPage; ?>;
        let totalPages = <?php echo $totalPages; ?>;

        prevPageBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage > 1) {
                currentPage--;
                updatePageInfo();
                loadData();
            }
        });

        nextPageBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage < totalPages) {
                currentPage++;
                updatePageInfo();
                loadData();
            }
        });

        function updatePageInfo() {
            pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
            prevPageBtn.disabled = currentPage === 1;
            nextPageBtn.disabled = currentPage === totalPages;
        }



        function loadData() {
            window.location.href = `?page=${currentPage}`;
        }
    </script>
    <script src="../../js/s_scriptAd.js"></script>

</body>

</html>