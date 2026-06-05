<?php
require_once "../includes/admin_check.php";
require_once "../config/db.php";

$status_labels = [
    "new" => "Новая",
    "learning" => "Идет обучение",
    "done" => "Обучение завершено",
];

$status_badges = [
    "new" => "text-bg-secondary",
    "learning" => "text-bg-warning",
    "done" => "text-bg-success",
];

$transport_types = [
    "Катер",
    "Яхта",
    "Круизный лайнер",
];

if(!isset($_SESSION["flash_message"])){
    $_SESSION["flash_message"] = "";
}

if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["status"])){
    $id = (int) $_POST["id"];
    $status = mysqli_real_escape_string($conn, $_POST["status"]);

    mysqli_query($conn, "UPDATE applications SET status='$status' WHERE id='$id'");
    $_SESSION["flash_message"] = "Статус заявки изменен.";

    $query_string = $_SERVER["QUERY_STRING"] !== "" ? "?" . $_SERVER["QUERY_STRING"] : "";
    header("Location: index.php$query_string");
    exit();
}

$search = trim($_GET["search"] ?? "");
$status_filter = $_GET["status_filter"] ?? "";
$transport_filter = $_GET["transport_filter"] ?? "";
$sort = $_GET["sort"] ?? "id";
$direction = strtolower($_GET["direction"] ?? "desc") === "asc" ? "asc" : "desc";
$page = max(1, (int) ($_GET["page"] ?? 1));
$per_page = 5;
$offset = ($page - 1) * $per_page;

$sort_map = [
    "id" => "applications.id",
    "fio" => "users.fio",
    "transport_type" => "applications.transport_type",
    "start_date" => "applications.start_date",
    "status" => "applications.status",
];

$order_by = $sort_map[$sort] ?? $sort_map["id"];
$where = ["1=1"];

if($search !== ""){
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $where[] = "(users.fio LIKE '%$search_escaped%' OR users.login LIKE '%$search_escaped%')";
}

if($status_filter !== ""){
    $status_escaped = mysqli_real_escape_string($conn, $status_filter);
    $where[] = "applications.status='$status_escaped'";
}

if($transport_filter !== ""){
    $transport_escaped = mysqli_real_escape_string($conn, $transport_filter);
    $where[] = "applications.transport_type='$transport_escaped'";
}

$where_sql = implode(" AND ", $where);

$count_result = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM applications
     INNER JOIN users ON applications.user_id = users.id
     WHERE $where_sql"
);

$total_rows = (int) (mysqli_fetch_assoc($count_result)["total"] ?? 0);
$total_pages = max(1, (int) ceil($total_rows / $per_page));

if($page > $total_pages){
    $page = $total_pages;
    $offset = ($page - 1) * $per_page;
}

$result = mysqli_query(
    $conn,
    "SELECT applications.*, users.fio, users.login
     FROM applications
     INNER JOIN users ON applications.user_id = users.id
     WHERE $where_sql
     ORDER BY $order_by $direction
     LIMIT $per_page OFFSET $offset"
);

function build_admin_url(array $overrides = []): string
{
    $params = array_merge($_GET, $overrides);
    foreach($params as $key => $value){
        if($value === "" || $value === null){
            unset($params[$key]);
        }
    }
    return "index.php" . (count($params) ? "?" . http_build_query($params) : "");
}

$flash_message = $_SESSION["flash_message"];
$_SESSION["flash_message"] = "";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Панель администратора</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="app-page">

<div class="container py-4">
    <h1 class="h3 mb-3">Панель администратора</h1>
    <p class="text-muted">Доступ для проверки всех заявок и изменения статусов.</p>

    <?php if($flash_message !== ""): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($flash_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
    </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Поиск</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Логин или ФИО">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Статус</label>
                    <select name="status_filter" class="form-select">
                        <option value="">Все статусы</option>
                        <?php foreach($status_labels as $status_key => $status_label): ?>
                        <option value="<?= $status_key ?>" <?= $status_filter === $status_key ? "selected" : "" ?>><?= htmlspecialchars($status_label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Транспорт</label>
                    <select name="transport_filter" class="form-select">
                        <option value="">Все виды</option>
                        <?php foreach($transport_types as $transport_type): ?>
                        <option value="<?= htmlspecialchars($transport_type) ?>" <?= $transport_filter === $transport_type ? "selected" : "" ?>><?= htmlspecialchars($transport_type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Сортировка</label>
                    <select name="sort" class="form-select">
                        <option value="id" <?= $sort === "id" ? "selected" : "" ?>>По ID</option>
                        <option value="fio" <?= $sort === "fio" ? "selected" : "" ?>>По ФИО</option>
                        <option value="transport_type" <?= $sort === "transport_type" ? "selected" : "" ?>>По транспорту</option>
                        <option value="start_date" <?= $sort === "start_date" ? "selected" : "" ?>>По дате</option>
                        <option value="status" <?= $sort === "status" ? "selected" : "" ?>>По статусу</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Порядок</label>
                    <select name="direction" class="form-select">
                        <option value="desc" <?= $direction === "desc" ? "selected" : "" ?>>Убывание</option>
                        <option value="asc" <?= $direction === "asc" ? "selected" : "" ?>>Возрастание</option>
                    </select>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Применить</button>
                    <a href="index.php" class="btn btn-outline-secondary">Сбросить</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Логин</th>
                            <th>ФИО</th>
                            <th>Транспорт</th>
                            <th>Дата старта</th>
                            <th>Оплата</th>
                            <th>Статус</th>
                            <th>Изменить</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($result) === 0): ?>
                        <tr>
                            <td colspan="8" class="text-center">Заявки не найдены.</td>
                        </tr>
                        <?php endif; ?>

                        <?php while($app = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= (int) $app["id"] ?></td>
                            <td><?= htmlspecialchars($app["login"]) ?></td>
                            <td><?= htmlspecialchars($app["fio"]) ?></td>
                            <td><?= htmlspecialchars($app["transport_type"]) ?></td>
                            <td><?= htmlspecialchars($app["start_date"]) ?></td>
                            <td><?= htmlspecialchars($app["payment_method"]) ?></td>
                            <td><span class="badge <?= $status_badges[$app["status"]] ?? "text-bg-secondary" ?>"><?= htmlspecialchars($status_labels[$app["status"]] ?? $app["status"]) ?></span></td>
                            <td>
                                <form method="POST" class="admin-status-form">
                                    <input type="hidden" name="id" value="<?= (int) $app["id"] ?>">
                                    <select name="status" class="form-select form-select-sm mb-2">
                                        <option value="new" <?= $app["status"] === "new" ? "selected" : "" ?>>Новая</option>
                                        <option value="learning" <?= $app["status"] === "learning" ? "selected" : "" ?>>Идет обучение</option>
                                        <option value="done" <?= $app["status"] === "done" ? "selected" : "" ?>>Обучение завершено</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm w-100">Сохранить</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <nav class="mt-3">
        <ul class="pagination">
            <li class="page-item <?= $page <= 1 ? "disabled" : "" ?>">
                <a class="page-link" href="<?= build_admin_url(["page" => max(1, $page - 1)]) ?>">Назад</a>
            </li>
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $i === $page ? "active" : "" ?>">
                <a class="page-link" href="<?= build_admin_url(["page" => $i]) ?>"><?= $i ?></a>
            </li>
            <?php endfor; ?>
            <li class="page-item <?= $page >= $total_pages ? "disabled" : "" ?>">
                <a class="page-link" href="<?= build_admin_url(["page" => min($total_pages, $page + 1)]) ?>">Вперед</a>
            </li>
        </ul>
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
