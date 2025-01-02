<?php
session_start();
require_once '../Config/db.php';
require_once '../Config/wa.php';
require_once '../Config/messages.php';

$current_page = basename($_SERVER['PHP_SELF']);

// if (!isset($_SESSION['logged_in'])) {
//     header('Location: login.php');
//     exit();
// }


// $userData = $_SESSION['user_data'];
// $username = $userData['username'];
$project_name = $userData['project_name'];
// $conn = getDbConnection($userData['host'], $userData['userdb'], $userData['passdb'], $userData['dbname']);
// $mainConn = getDbConnection('localhost', 'root', '', 'apiwa');

// Ambil data dari database
$siswaOptions = fetchDropdownOptions($conn, $userData['siswa'], $userData['nama_siswa_column']);
$sekolahOptions =  fetchDropdownOptions($conn, $userData['siswa'], $userData['sekolah']);
$kelasOptions = fetchDropdownClassOptions($conn, $userData['siswa'], $userData['jenjang'], $userData['kelas'], true);
$angkatanOptions = fetchDropdownOptions($conn, $userData['siswa'], $userData['angkatan']);



date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send'])) {
    $stmt = $mainConn->prepare("SELECT wa_apikey, wa_numberkey FROM master_setting WHERE project_name = ?");
    $stmt->bind_param("s", $project_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $credentials = $result->fetch_assoc();
    $apiKey = $credentials['wa_apikey'] ?? '';
    $numberKey = $credentials['wa_numberkey'] ?? '';

    // Pengecekan API key dan number key
    if (empty($apiKey) || empty($numberKey)) {
        $_SESSION['toast_message'] = 'API Key atau Number Key tidak ditemukan atau kosong!';
        $_SESSION['toast_type'] = 'failed';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    $sendMethod = $_POST['send_method'];
    $message = trim($_POST['message']);


    // Check if message is empty
    if (empty($message)) {
        $_SESSION['toast_message'] = 'Pesan tidak boleh kosong!';
        $_SESSION['toast_type'] = 'warning';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }


    $requestData = [
        'project_name' => $project_name,
        'method' => $sendMethod,
        'target' => null,
        'description' => $message,
    ];

    switch ($sendMethod) {
        case 'SEND_SISWA':
            $siswaIds = $_POST['siswa'] ?? [];
            if (empty($siswaIds)) {
                $_SESSION['toast_message'] = 'Silakan pilih siswa yang ingin dikirimkan pesan!';
                $_SESSION['toast_type'] = 'warning';
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                if (!empty($siswaIds)) {
                    foreach ($siswaIds as $siswaId) {
                        $requestData['target'] = $siswaId;
                        $response = sendRequestToApiwa($requestData);
                        handleApiResponse($response);
                    }
                }
            }
            break;


        case 'SEND_KELAS':
            if (isset($_POST['kelas'])) {
                $kelas = $_POST['kelas'];
                if (empty($kelas)) {
                    $_SESSION['toast_message'] = 'Silakan pilih kelas yang ingin dikirimkan pesan!';
                    $_SESSION['toast_type'] = 'warning';
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } else {
                    $requestData['target'] = $kelas;
                    $response = sendRequestToApiwa($requestData);
                    handleApiResponse($response);
                }
            }
            break;

        case 'SEND_ANGKATAN':
            if (isset($_POST['angkatan'])) {
                $requestData['target'] = $_POST['angkatan'];
                $response = sendRequestToApiwa($requestData);
                handleApiResponse($response);
            }
            break;

        case 'SEND_ALL':
            $requestData['method'] = 'SEND_ALL';
            $response = sendRequestToApiwa($requestData);
            handleApiResponse($response);
            break;
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


function sendRequestToApiwa($data)
{
    // $url = 'http://localhost/sendwa/apiwa.php';
    $url = 'http://localhost/apiwa/SendWAForClient/api/apiwa.php';
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array('Content-Type: application/json'),

    ));

    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response, true);
}

function handleApiResponse($response)
{
    // Cek apakah ada data dalam response dan jika status pada data adalah berhasil atau gagal
    if (isset($response['data']) && is_array($response['data'])) {
        foreach ($response['data'] as $res) {
            // Memeriksa status pada masing-masing item dalam data
            if (isset($res['status']) && $res['status'] === 'berhasil') {
                $_SESSION['toast_message'] = 'Pesan WhatsApp berhasil dikirim!';
                $_SESSION['toast_type'] = 'success';
            } else {
                $_SESSION['toast_message'] = 'Pesan gagal dikirim! ' . ($res['error'] ?? '');
                $_SESSION['toast_type'] = 'failed';
            }
        }
    }
}



// Fungsi untuk mengupdate status log_pesan
function updateStatusLogPesan($conn, $log_id, $status)
{
    $stmtUpdate = $conn->prepare("UPDATE log_pesan SET status = ? WHERE id = ?");
    $stmtUpdate->bind_param("si", $status, $log_id);
    $stmtUpdate->execute();
    $stmtUpdate->close();
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
    <style>
        input[type="checkbox"]:checked {
            background-color: green;
            /* Warna hijau untuk background checkbox saat dicentang */
            border-color: green;
            /* Warna hijau untuk border checkbox saat dicentang */
        }

        input[type="checkbox"]:checked::before {
            content: '';
            display: block;
            width: 100%;
            height: 100%;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="white"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.707-4.707a1 1 0 011.414-1.414L8.414 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }
    </style>
    <title>SendWa</title>
</head>

<body class="bg-gray-100">
    <div class="flex">
        <!-- asidebar -->
        <aside id="sidebar" class="fixed top-0 left-0 w-14 min-h-screen bg-repeat-y transition-all transform ease-in-out duration-300 z-40 sm:w-60" style="background-image: url('../assets/img/2151554909.jpg');">
            <div class="p-4 text-white bg-sky-900">
                <div class="flex flex-col items-center sm:flex-row">
                    <img class="w-6 h-6 sm:w-16 sm:h-16 rounded-full" src="../assets/img/1.png" alt="Profile Picture">
                    <div class="mt-2 text-center sm:ml-4">
                        <h4 class="block w-full font-semibold text-xs text-wrap sm:text-lg sm:text-start"><?php echo htmlspecialchars($username); ?></h4>
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
                        <svg class="hidden sm:block w-7 h-7 text-cyan-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                            <path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
                            <path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
                        </svg>
                        <span class="hidden sm:block ml-2 text-cyan-50 from-neutral-300">Send Whatsapp</span>
                    </div>
                </div>
                <ul class="ml-2 text-gray-300">
                    <li class="relative group px-3 sm:ml-12 py-2 mt-4 mb-4 hover:bg-white hover:text-black <?php echo ($current_page == 'kirimWA.php') ? 'bg-white underline underline-offset-8 rounded-l text-sky-800' : ''; ?>">
                        <a href="kirimWA.php" class="flex items-center transition-all">
                            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>

                            <span class="hidden sm:block">Send Wa</span>
                            <!-- Tooltip -->
                        </a>
                        <div class="sm:hidden absolute left-full top-1/2 ml-3 -translate-y-1/2 px-2 py-1 text-sm text-white bg-black rounded-md opacity-0 group-hover:opacity-100 transition-opacity">
                            Send WhatsApp
                        </div>
                    </li>
                    <li class="relative group px-3 sm:ml-12 py-2 mt-4 mb-4 hover:bg-white hover:text-black <?php echo ($current_page == 'tagihan.php') ? 'bg-white underline underline-offset-8 rounded-l text-sky-800' : ''; ?>">
                        <a href="tagihan.php" class="flex items-center transition-all">
                            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                            </svg>

                            <span class="hidden sm:block">Reminder</span>
                            <!-- Tooltip -->
                        </a>
                        <div class="sm:hidden absolute left-full top-1/2 ml-3 -translate-y-1/2 px-2 py-1 text-sm text-white bg-black rounded-md opacity-0 group-hover:opacity-100 transition-opacity">
                            Reminder
                        </div>
                    </li>
                    <li class="relative group px-3 sm:ml-12 py-2 mb-4 hover:bg-white hover:text-black <?php echo ($current_page == 'log.php') ? 'bg-white underline underline-offset-8 rounded-l text-sky-800' : ''; ?>">
                        <a href="log.php" class="flex items-center transition-all">
                            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.057 1.123-.08M15.75 18H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08M15.75 18.75v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5A3.375 3.375 0 0 0 6.375 7.5H5.25m11.9-3.664A2.251 2.251 0 0 0 15 2.25h-1.5a2.251 2.251 0 0 0-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5a9 9 0 0 0-9-9Z" />
                            </svg>
                            <span class="hidden sm:block">Log</span>
                        </a>
                        <!-- Tooltip -->
                        <div class="absolute left-full top-1/2 ml-3 -translate-y-1/2 px-2 py-1 text-sm text-white bg-black rounded-md opacity-0 group-hover:opacity-100 transition-opacity sm:hidden">
                            Log
                        </div>
                    </li>
                </ul>
                <div class="mt-12 sm:mt-2">
                    <a href="logout.php" class="relative group flex items-center px-4 py-2 text-white hover:bg-white hover:text-black">
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
                    <img class="h-12 w-auto" src="../assets/img/Logo_512.png" alt="Your Company">
                    <span class="text-lg font-bold text-white ml-4 sm:ml-0">Send WhatsApp</span>
                </div>
            </div>
            <!-- header -->
            <header id="header-bar" class="flex justify-between items-center mb-5 min-h-14 bg-slate-50 shadow-sm shadow-slate-400 fixed top-14 left-0 right-0 z-20 sm:ml-60">
                <h1 id="header-title" class="ml-20 sm:ml-10 text-lg font-semibold">Kirim - WA</h1>
            </header>

            <!-- main -->
            <main class="container mx-auto px-4 py-4 mt-28">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Form Card 1 -->
                    <div class="bg-white shadow-md rounded-lg p-6">
                        <h2 class="text-xl font-bold mb-4">Kirim - WA</h2>
                        <form action="" method="post" id="sendMessageForm">
                            <div class="mb-4">
                                <label for="method" class="block text-gray-700 font-medium mb-2 after:content-['*'] after:text-pink-500">Metode :</label>
                                <select required name="send_method" id="method" class="block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-200">
                                    <option value="" disabled selected>Pilih metode kirim</option> <!-- Placeholder option -->
                                    <option name="method" value="SEND_SISWA">Kirim ke Siswa yang Dipilih</option>
                                    <option name="method" value="SEND_KELAS">Kirim ke Semua Siswa di Kelas</option>
                                    <option name="method" value="SEND_ANGKATAN">Kirim ke Semua Siswa di Angkatan</option>
                                    <!-- <option name="method" value="SEND_ALL">Kirim ke Semua Siswa</option> -->
                                </select>
                            </div>
                            <div class="mb-4" id="siswaField">
                                <label for="siswa" class="block text-gray-700 font-medium">Pilih Siswa :</label>
                                <p class="mb-2 font-extralight text-sm text-end">Jumlah siswa yang dipilih: <span id="selected-count">0</span></p>
                                <div id="siswa" class="relative border border-gray-300 rounded-md p-4 max-h-64 overflow-y-auto">
                                    <div class="mb-2 flex items-center border bg-slate-400 border-gray-300 rounded-md shadow-md sticky top-0 z-10">
                                        <svg class="w-5 h-5 text-gray-500 ml-2 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M15.65 10.65a6.65 6.65 0 1 1-13.3 0 6.65 6.65 0 0 1 13.3 0z"></path>
                                        </svg>
                                        <input type="text" name="search-siswa" id="search-siswa" class="w-2/3 pl-2 pr-2 pt-1 pb-1 border-none rounded-md focus:ring-0" placeholder="Cari nama siswa...">
                                        <div class="m-2">
                                            <select id="filter-kelas" class="w-full p-1 border border-gray-300 rounded-md text-sm">
                                            <option value="">Semua Kelas</option>
                                                <?php
                                                foreach ($kelasOptions as $kelas):
                                                    // Hanya tampilkan jika $kelas tidak kosong dan tidak null
                                                    if (!empty(trim($kelas))) {
                                                ?>
                                                        <option value="<?= htmlspecialchars($kelas); ?>"><?= htmlspecialchars($kelas); ?></option>
                                                <?php
                                                    }
                                                endforeach;
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="siswa-list" name="siswa[]">
                                        <?php foreach ($siswaOptions as $index => $option): ?>
                                            <?php $inputId = "siswa_option_" . $option; ?>
                                            <div class="flex items-center">
                                                <input type="checkbox" id="<?= $inputId; ?>"
                                                    class="bg-transparent peer mr-2 appearance-none h-4 w-4 border-2 rounded-full hover:border-teal-500 cursor-pointer border-teal-300"
                                                    name="siswa[]"
                                                    value="<?= htmlspecialchars($option); ?>"
                                                    data-siswa-id="<?= $index; ?>"> <!-- Gunakan $index sebagai data-siswa-id -->
                                                <label for="<?= $inputId; ?>"
                                                    class="flex items-center w-full p-1 border-b rounded cursor-pointer transition-colors duration-200 hover:bg-teal-600 hover:text-white  peer-checked:bg-green-500 peer-checked:text-white">
                                                    <?= htmlspecialchars($option); ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>


                                    <!-- Pesan ketika tidak ada siswa -->
                                    <div id="no-siswa-message" class="hidden text-gray-500 p-2">
                                        Tidak ada nama siswa yang cocok.
                                    </div>
                                    <div id="loading-siswa" class="hidden text-center py-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" class="mx-auto w-8 h-8">
                                            <circle fill="#2B2527" stroke="#2B2527" stroke-width="15" r="15" cx="40" cy="65">
                                                <animate attributeName="cy" calcMode="spline" dur="2" values="65;135;65;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="-.4"></animate>
                                            </circle>
                                            <circle fill="#2B2527" stroke="#2B2527" stroke-width="15" r="15" cx="100" cy="65">
                                                <animate attributeName="cy" calcMode="spline" dur="2" values="65;135;65;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="-.2"></animate>
                                            </circle>
                                            <circle fill="#2B2527" stroke="#2B2527" stroke-width="15" r="15" cx="160" cy="65">
                                                <animate attributeName="cy" calcMode="spline" dur="2" values="65;135;65;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="0"></animate>
                                            </circle>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <!-- Form Dropdown Sekolah -->
                            <!-- Dropdown Pilih Sekolah -->
                            <div class="mb-4" id="sekolahField">
                                <label for="sekolah" class="block text-gray-700 font-medium mb-2">Pilih Sekolah :</label>
                                <select name="sekolah" id="sekolah" class="block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-200" onchange="fetchKelasOptions()">
                                <option value="">Pilih Sekolah</option>
                                    <?php
                                    foreach ($sekolahOptions as $option):
                                        // Hanya tampilkan jika $kelas tidak kosong dan tidak null
                                        if (!empty(trim($option))) {
                                    ?>
                                            <option value="<?= htmlspecialchars($option); ?>"><?= htmlspecialchars($option); ?></option>
                                    <?php
                                        }
                                    endforeach;
                                    ?>
                                </select>
                            </div>

                            <!-- Dropdown Pilih Kelas -->
                            <div class="mb-4" id="kelasField">
                                <label for="kelas" class="block text-gray-700 font-medium mb-2">Pilih Kelas :</label>
                                <select name="kelas" id="kelas" class="block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-200">
                                    <option value="">Pilih Sekolah Dulu</option>
                                </select>
                            </div>



                            <div class="mb-4" id="angkatanField">
                                <label for="angkatan" class="block text-gray-700 font-medium mb-2">Pilih Angkatan :</label>
                                <select name="angkatan" id="angkatan" class="block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-200">
                                    <option value="">Pilih Angkatan</option>
                                    <?php foreach ($angkatanOptions as $option): ?>
                                        <option value="<?= htmlspecialchars($option); ?>"><?= htmlspecialchars($option); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="message" class="block text-gray-700 font-medium mb-2">Pesan :</label>
                                <textarea name="message" id="message" rows="4" cols="50" class="block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-200"></textarea>
                            </div>
                            <button type="submit" id="sendButton" name="send" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition duration-300" value="Kirim Pesan">Kirim Pesan</button>
                        </form>
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

                    <!-- Form Card 2 -->
                    <div class="bg-white flex flex-col shadow-md rounded-lg p-6 ">
                        <h2 class="text-xl font-bold mb-7 text-center">Information</h2>

                        <ul class="list-decimal leading-10 flex-col items-center justify-center text-justify mx-10 md:mx-20 whitespace-normal break-words text-wrap w-4/5">
                            <li>Silahkan isi metode pengiriman terlebih dahulu</li>
                            <li>Kemudian pilih penerima sesuai metode yang dipilih
                            </li>
                            <li>Isikan pesan custom pada text box yang tersedia
                            </li>
                            <li>Klik kirim untuk mengirim pesan dan tunggu beberapa saat sampai ada notifikasi berhasil
                            </li>
                            <li>Cek log untuk melihat log pengiriman pesan</li>
                        </ul>


                    </div>
                </div>
            </main>


        </div>



    </div>
    <script src="../js/s_scriptSisw.js"></script>
    <script src="../js/s_script.js"></script>

</body>

</html>