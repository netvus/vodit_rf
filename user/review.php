<?php
require_once "../includes/auth_check.php";
require_once "../config/db.php";

$user_id = (int) $_SESSION["user_id"];
$id = (int) ($_GET["id"] ?? 0);
$message = "";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $review = mysqli_real_escape_string($conn, trim($_POST["review"]));

    mysqli_query(
        $conn,
        "INSERT INTO reviews (user_id,application_id,review_text)
         VALUES ('$user_id','$id','$review')"
    );

    $message = "Отзыв отправлен.";
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Отзыв</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="app-page">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card">
                <div class="card-body p-4">
                    <h1 class="h3 mb-4">Отзыв об обучении</h1>

                    <?php if($message !== ""): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Текст отзыва</label>
                            <textarea name="review" class="form-control" rows="5" placeholder="Напишите впечатления о пройденном обучении" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Отправить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
