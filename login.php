<?php
session_start();
require_once "config/db.php";

$message = "";
$login = "";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $login = mysqli_real_escape_string($conn, trim($_POST["login"]));
    $password = $_POST["password"];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE login='$login'");

    if(mysqli_num_rows($result) === 1){
        $user = mysqli_fetch_assoc($result);

        if(password_verify($password, $user["password"])){
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["fio"] = $user["fio"];
            $_SESSION["role"] = $user["role"];

            if($user["role"] === "admin"){
                header("Location: admin/index.php");
            }else{
                header("Location: user/profile.php");
            }
            exit();
        }

        $message = "Неверный пароль.";
    }else{
        $message = "Пользователь с таким логином не найден.";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Авторизация</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="app-page">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-body p-4">
                    <h1 class="h3 text-center mb-4">Авторизация</h1>

                    <?php if($message !== ""): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Логин</label>
                            <input type="text" name="login" class="form-control" value="<?= htmlspecialchars($login) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Пароль</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Войти</button>
                    </form>

                    <p class="form-link mt-3 mb-0">
                        Еще не зарегистрированы? <a href="register.php">Регистрация</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
