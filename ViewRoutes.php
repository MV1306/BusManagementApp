<?php
// API base URL (replace with your actual ASP.NET API base URL)
$apiUrl = "https://192.168.29.141/BusManagementAPI/GetAllRoutes";

// Fetch all routes via cURL
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$response = curl_exec($ch);
curl_close($ch);

$routes = json_decode($response, true);

// Pagination settings
$limit = 10; // Number of routes per page
$totalRoutes = count($routes);
$totalPages = ceil($totalRoutes / $limit);

// Get current page from URL, default is 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
if ($page > $totalPages) $page = $totalPages;

// Calculate start index
$start = ($page - 1) * $limit;

// Get only the subset of routes for the current page
$routesPage = array_slice($routes, $start, $limit);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Routes</title>
    <style>
        table { border-collapse: collapse; width: 80%; margin: 20px auto; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f2f2f2; }
        a.button { padding: 5px 10px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 3px; }
        a.button.delete { background-color: #f44336; }
        .hidden { display: none; }
        /* Pagination styles */
        .pagination {
            text-align: center;
            margin: 20px;
        }
        .pagination a {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 2px;
            border: 1px solid #ccc;
            text-decoration: none;
            color: #333;
            border-radius: 4px;
        }
        .pagination a.active {
            background-color: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }
        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">Bus Routes</h2>

<div style="width: 90%; margin: 20px auto; text-align: right;">
    <a href="AddRoute.php" class="button" style="background-color: #007BFF; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;">Add New Route</a>
</div>


<table>
    <thead>
        <tr>
            <th class="hidden">ID</th>
            <th>Route Code</th>
            <th>From</th>
            <th>To</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($routesPage as $route): ?>
        <tr>
            <td class="hidden"><?= htmlspecialchars($route['id']) ?></td>
            <td><?= htmlspecialchars($route['code']) ?></td>
            <td><?= htmlspecialchars($route['from']) ?></td>
            <td><?= htmlspecialchars($route['to']) ?></td>
            <td>
                <a href="EditRoute.php?id=<?= $route['id'] ?>" class="button">Edit</a>
                <a href="delete_route.php?id=<?= $route['id'] ?>" class="button delete" onclick="return confirm('Are you sure you want to delete this route?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Pagination links -->
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>">&laquo; Prev</a>
    <?php endif; ?>

    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
        <a href="?page=<?= $p ?>" class="<?= $p == $page ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>">Next &raquo;</a>
    <?php endif; ?>
</div>

</body>
</html>
