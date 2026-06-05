<?php
require_once "includes/slider_data.php";
$slides = get_slider_slides();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Водить.РФ</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="app-page">

<nav class="navbar navbar-expand-lg app-navbar">
    <div class="container">
        <span class="navbar-brand app-brand">Водить.РФ</span>
        <div class="d-flex gap-2">
            <a href="login.php" class="btn btn-outline-primary btn-sm">Войти</a>
            <a href="register.php" class="btn btn-primary btn-sm">Регистрация</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="row g-4 align-items-center">
        <div class="col-lg-5">
            <h1 class="h2 mb-3">Запись на курсы обучения вождению речного транспорта</h1>
            <p class="text-muted mb-3">
                Система позволяет выбрать вид транспорта, указать дату старта обучения
                и отправить заявку администратору.
            </p>
            <div class="d-flex gap-2 flex-wrap">
                <a href="login.php" class="btn btn-outline-primary">Войти</a>
                <a href="register.php" class="btn btn-primary">Зарегистрироваться</a>
            </div>
        </div>

        <div class="col-lg-7">
            <div id="guestSlider" class="carousel slide simple-slider" data-bs-ride="carousel" data-bs-interval="3000">
                <div class="carousel-inner">
                    <?php foreach($slides as $index => $slide): ?>
                    <div class="carousel-item <?= $index === 0 ? "active" : "" ?>">
                        <img src="<?= htmlspecialchars($slide["image"]) ?>" class="d-block w-100 slider-image" alt="<?= htmlspecialchars($slide["title"]) ?>">
                        <div class="slider-note">
                            <strong><?= htmlspecialchars($slide["title"]) ?></strong>
                            <div><?= htmlspecialchars($slide["subtitle"]) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#guestSlider" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Назад</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#guestSlider" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Вперед</span>
                </button>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6">Виды транспорта</h2>
                    <p class="mb-0 text-muted">Катер, яхта и круизный лайнер.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6">Личный кабинет</h2>
                    <p class="mb-0 text-muted">Просмотр заявок и возможность оставить отзыв после завершения обучения.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6">Администратор</h2>
                    <p class="mb-0 text-muted">Проверка всех заявок, фильтрация, сортировка и изменение статусов.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
