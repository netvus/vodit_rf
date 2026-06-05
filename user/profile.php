<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";
require_once "../includes/slider_data.php";

$user_id = (int) $_SESSION["user_id"];
$fio = $_SESSION["fio"];
$slides = get_slider_slides();

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

$result = mysqli_query(
    $conn,
    "SELECT * FROM applications WHERE user_id='$user_id' ORDER BY id DESC"
);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Личный кабинет</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="app-page">

<nav class="navbar navbar-expand-lg app-navbar">
    <div class="container">
        <span class="navbar-brand app-brand">Водить.РФ</span>
        <div class="d-flex align-items-center gap-3">
            <span><?= htmlspecialchars($fio) ?></span>
            <a href="../logout.php" class="btn btn-outline-primary btn-sm">Выйти</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-5">
            <h1 class="h3 mb-3">Личный кабинет</h1>
            <p class="text-muted">Здесь отображаются заявки на обучение и доступна отправка отзыва после завершения курса.</p>
            <a href="create_application.php" class="btn btn-primary">Оформить заявку</a>
        </div>

        <div class="col-lg-7">
            <div id="profileSlider" class="carousel slide simple-slider" data-bs-ride="carousel" data-bs-interval="3000">
                <div class="carousel-inner">
                    <?php foreach($slides as $index => $slide): ?>
                    <div class="carousel-item <?= $index === 0 ? "active" : "" ?>">
                        <img src="../<?= htmlspecialchars($slide["image"]) ?>" class="d-block w-100 slider-image" alt="<?= htmlspecialchars($slide["title"]) ?>">
                        <div class="slider-note">
                            <strong><?= htmlspecialchars($slide["title"]) ?></strong>
                            <div><?= htmlspecialchars($slide["subtitle"]) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#profileSlider" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Назад</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#profileSlider" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Вперед</span>
                </button>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <h2 class="h4 mb-3">История заявок</h2>
        <div class="row g-3">
            <?php if(mysqli_num_rows($result) === 0): ?>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-0 text-muted">У вас пока нет созданных заявок.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php while($app = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h3 class="h5 mb-0"><?= htmlspecialchars($app["transport_type"]) ?></h3>
                            <span class="badge <?= $status_badges[$app["status"]] ?? "text-bg-secondary" ?>">
                                <?= htmlspecialchars($status_labels[$app["status"]] ?? $app["status"]) ?>
                            </span>
                        </div>

                        <p class="mb-2"><strong>Дата начала:</strong> <?= htmlspecialchars($app["start_date"]) ?></p>
                        <p class="mb-3"><strong>Оплата:</strong> <?= htmlspecialchars($app["payment_method"]) ?></p>

                        <?php if($app["status"] === "done"): ?>
                        <a href="review.php?id=<?= (int) $app["id"] ?>" class="btn btn-outline-primary btn-sm">Оставить отзыв</a>
                        <?php else: ?>
                        <span class="text-muted small">Отзыв станет доступен после завершения обучения.</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
