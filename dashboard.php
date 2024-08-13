<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$username = htmlspecialchars($user['username']);

$items_per_page = 4;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT COUNT(*) AS count FROM users WHERE username LIKE ?";
$search_param = "%$search%";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $search_param);
$stmt->execute();
$result = $stmt->get_result();
$total_items = $result->fetch_assoc()['count'];
$total_pages = ceil($total_items / $items_per_page);

$sql = "SELECT * FROM users WHERE username LIKE ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sii', $search_param, $items_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

</head>

<body class="bg-gray-100">
    <nav class="bg-blue-500 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#" class="text-white text-lg">Dashboard</a>
            <form action="dashboard.php" method="GET" class=" w-1/2">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    class="p-2 border border-gray-300 rounded" placeholder="Cari Nama Pengguna">
                <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded">Search</button>
            </form>
            <div class="dropdown relative">
                <button class="dropdown-button bg-blue-700 text-white px-4 py-2 rounded">
                    <?php echo $username; ?>
                </button>
                <div class="dropdown-content absolute right-0 mt-2 bg-white border border-gray-200 rounded shadow-lg">
                    <a href="logout.php" class="block px-4 py-2">Logout</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="container mx-auto p-6 flex justify-between">
        <?php while ($user = $result->fetch_assoc()): ?>
        <div class="bg-white w-60 rounded p-2 text-center item-center">
            <h1 class="bg-yellow-200 mb-2">No : <?php echo htmlspecialchars($user['id']); ?></h1>
            <div class="w-full flex justify-center">
                <div class="w-32 h-32 mb-2 rounded-full bg-gray-500"></div>
            </div>
            <h1> Username : <p class="font-bold mb-2"><?php echo htmlspecialchars($user['username']); ?></p>
            </h1>
            <p> created : <?php echo htmlspecialchars($user['createdAt']); ?></p>
            <a href="edit.php?id=<?php echo $user['id']; ?>" class="text-blue-500 hover:underline">Edit</a>
            <a href="delete.php?id=<?php echo $user['id']; ?>" class="text-red-500 hover:underline">Delete</a>
        </div>
        <?php endwhile; ?>
        <div class="mt-4">
            <nav class="flex justify-between">
                <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="bg-blue-500 text-white px-4 py-2 rounded">Previous</a>
                <?php endif; ?>
                <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="bg-blue-500 text-white px-4 py-2 rounded">Next</a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</body>

</html>