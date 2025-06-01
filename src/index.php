<?php
require_once 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$is_admin = $_SESSION['is_admin'] ?? false;

// Function to fetch existing QR codes with scan counts
function getQrCodesWithScanCounts($db_conn, $user_id, $is_admin) {
    $sql = "SELECT qc.*, COUNT(sl.id) as scan_count 
            FROM qr_codes qc
            LEFT JOIN scan_logs sl ON qc.id = sl.qr_code_id";
    if (!$is_admin) {
        $sql .= " WHERE qc.user_id = " . intval($user_id);
    }
    $sql .= " GROUP BY qc.id ORDER BY qc.created_at DESC";
    $result = $db_conn->query($sql);
    $qr_codes = [];
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $qr_codes[] = $row;
        }
    }
    return $qr_codes;
}

$existing_qr_codes = getQrCodesWithScanCounts($conn, $user_id, $is_admin);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-P">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP QR Code Generator & Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-4 md:p-8">
    <div class="container mx-auto max-w-4xl bg-white p-6 md:p-8 rounded-lg shadow-xl">
        <header class="mb-8 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-slate-700">QR Code Generator & Tracker</h1>
            <p class="text-slate-500">Create and track your QR codes easily.</p>
            <p class="mt-2 text-sm text-blue-600"><a href="init_db.php" class="hover:underline">Initialize/Check Database Tables</a></p>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="mt-2 text-sm text-blue-600">
                    <a href="change_password.php" class="hover:underline mr-4">Change Password</a>
                    <a href="logout.php" class="hover:underline">Logout</a>
                </div>
            <?php endif; ?>
        </header>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="mb-6 p-4 rounded-md <?php echo strpos($_SESSION['message_type'], 'success') !== false ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>" role="alert">
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
        <?php endif; ?>

        <section class="mb-10 p-6 bg-slate-50 rounded-lg shadow">
            <h2 class="text-2xl font-semibold text-slate-600 mb-4">Create New QR Code</h2>
            <form action="generate.php" method="POST" class="space-y-4">
                <div>
                    <label for="data_url" class="block text-sm font-medium text-slate-700 mb-1">URL or Text to Encode:</label>
                    <input type="text" id="data_url" name="data_url" required
                           class="w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="e.g., https://www.example.com">
                </div>
                <div>
                    <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                        Generate QR Code
                    </button>
                </div>
            </form>
        </section>

        <form method="get" class="mb-4 text-right">
            <button type="submit" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-medium py-1 px-3 rounded-md text-sm shadow-sm transition duration-150">
                Refresh Scan Counts
            </button>
        </form>

        <section>
            <h2 class="text-2xl font-semibold text-slate-600 mb-6">Existing QR Codes</h2>
            <?php if (empty($existing_qr_codes)): ?>
                <p class="text-slate-500">No QR codes generated yet. Create one above!</p>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($existing_qr_codes as $qr): ?>
                        <div class="bg-slate-50 p-4 rounded-lg shadow-md flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6">
                            <div class="flex-shrink-0">
                                <img src="<?php echo htmlspecialchars(QR_CODE_DIR . $qr['image_filename']); ?>" alt="QR Code" class="w-32 h-32 md:w-40 md:h-40 border border-slate-200 rounded-md">
                            </div>
                            <div class="flex-grow text-center md:text-left">
                                <p class="text-sm text-slate-600 break-all"><strong>Original Data:</strong> <?php echo htmlspecialchars($qr['data_url']); ?></p>
                                <p class="text-sm text-slate-600">
                                    <strong>Redirect Link:</strong>
                                    <a href="<?php echo htmlspecialchars(BASE_URL . 'redirect.php?code=' . $qr['short_code']); ?>" target="_blank" class="text-indigo-600 hover:underline break-all">
                                        <?php echo htmlspecialchars(BASE_URL . 'redirect.php?code=' . $qr['short_code']); ?>
                                    </a>
                                </p>
                                <p class="text-sm text-slate-500"><strong>Scans:</strong> <?php echo $qr['scan_count']; ?></p>
                                <p class="text-xs text-slate-400">Created: <?php echo date("M j, Y, g:i a", strtotime($qr['created_at'])); ?></p>
                            </div>
                            <div class="flex-shrink-0 mt-4 md:mt-0">
                                 <a href="<?php echo htmlspecialchars(QR_CODE_DIR . $qr['image_filename']); ?>" download
                                   class="inline-block bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-3 rounded-md text-sm shadow-sm transition duration-150">
                                   Download
                                 </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <footer class="mt-10 text-center text-sm text-slate-400">
            <p>&copy; <?php echo date("Y"); ?> Your QR Tracker. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>