<?php

session_start();
require_once '../Config/db.php';
require_once '../Config/wa.php';


$current_page = basename($_SERVER['PHP_SELF']);

// if (!isset($_SESSION['logged_in'])) {
//     header('Location: login.php');
//     exit();
// }


// $userData = $_SESSION['user_data'];
// $mainConn = getDbConnection('localhost', 'root', '', 'apiwa');
// $mainConn = getDbConnection('103.23.103.43', 'elpe', 'Bismillah99', 'sendwa');

$username = $userData['username'];
$conn = getDbConnection($userData['host'], $userData['userdb'], $userData['passdb'], $userData['dbname']);

// Ambil data pesan yang disubmit dari database
$queryPesan = "SELECT * FROM log_pesan WHERE project_name = '" . $userData['project_name'] . "' ORDER BY sent_at DESC";
$resultPesan = $mainConn->query($queryPesan);



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
    <script src="../js/scriptLog.js"></script>
    <title>History</title>
</head>

<body class="bg-gray-100">
    <div class="flex">
        <!-- asidebar -->
        <aside id="sidebar" class="fixed top-0 left-0 w-14 min-h-screen bg-repeat-y transition-all transform ease-in-out duration-300 z-40 sm:w-60" style="background-image: url('../assets/img/2151554909.jpg');">
            <div class="p-4 text-white bg-sky-900">
                <div class="flex flex-col items-center sm:flex-row">
                    <img class="w-6 h-6 sm:w-16 sm:h-16 rounded-full" src="../assets/img/1.png" alt="Profile Picture">
                    <div class="mt-2 text-center sm:ml-4">
                        <h4 class="block w-full font-semibold text-xs sm:text-lg text-wrap sm:text-start"><?php echo htmlspecialchars($username); ?></h4>
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
                <h1 id="header-title" class="ml-20 sm:ml-10 text-lg font-semibold">Log - WA</h1>
            </header>

            <!-- main -->
            <div class="bg-white mt-32 shadow-md rounded-lg mx-3 sm:mx-4 p-6 sm:max-w-[calc(100%-2rem)] w-full h-11/12">
                <h2 class="text-2xl font-semibold mb-4">Data Table History</h2>
                <div class="overflow-y-auto max-h-[calc(100vh-15rem)]">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr class="bg-teal-800 text-white sticky top-0">
                                <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">No.</th>
                                <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Nama</th>
                                <th class="py-2 px-4 border-b border-gray-300 text-center text-sm font-semibold">Pesan</th>
                                <th class="py-2 px-4 border-b border-gray-300 text-center text-sm font-semibold">Nama Penerima</th>
                                <th class="py-2 px-4 border-b border-gray-300 text-center text-sm font-semibold">Status</th>
                                <th class="py-2 px-4 border-b border-gray-300 text-center text-sm font-semibold">Sent_at</th>
                                <th class="py-2 px-4 border-b border-gray-300 text-left text-sm font-semibold">Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($resultPesan->num_rows > 0) {
                                $no = 1;
                                while ($row = $resultPesan->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td class='py-2 px-4 border-b border-gray-300'>" . $no++ . "</td>";
                                    echo "<td class='py-2 px-4 border-b border-gray-300'>" . $row['nama_pengirim'] . "</td>";
                                    echo "<td class='py-2 px-4 border-b border-gray-300 whitespace-nowrap overflow-hidden text-ellipsis max-w-xs'>" . $row["pesan"] . "</td>";
                                    echo "<td class='py-2 px-4 border-b border-gray-300 text-center text'>" . $row["nama_penerima"] . "</td>";
                                    // Menampilkan status dengan badge
                                    echo "<td class='py-2 px-4 border-b border-gray-300'>";
                                    if ($row['status'] === 'pending') {
                                        echo "<span class='inline-block bg-yellow-500 text-white px-2 py-1 rounded-full text-xs shadow-md'>Pending</span>";
                                    } elseif ($row['status'] === 'berhasil') {
                                        echo "<span class='inline-block bg-green-500 text-white px-2 py-1 rounded-full text-xs shadow-md'>Berhasil</span>";
                                    } elseif ($row['status'] === 'gagal') {
                                        echo "<span class='inline-block bg-red-500 text-white px-2 py-1 rounded-full text-xs shadow-md'>Gagal</span>";
                                    }
                                    echo "</td>";
                                    echo "<td class='py-2 px-4 border-b border-gray-300 text-center'>" . $row["sent_at"] . "</td>"; // Kolom waktu seharusnya hanya ini
                                    echo "<td class='py-2 px-4 border-b border-gray-300'>" . $row['method'] . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='py-2 px-4 border-b border-gray-300 text-center'>Tidak ada data</td></tr>";
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>



    </div>

</body>

</html>