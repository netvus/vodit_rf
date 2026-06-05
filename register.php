<?php
require_once "config/db.php";

$errors = [];
$success_message = "";
$today = date("Y-m-d");
$form_data = [
    "login" => "",
    "fio" => "",
    "birth_date" => "",
    "phone" => "",
    "email" => "",
];

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $form_data["login"] = trim($_POST["login"]);
    $password = $_POST["password"];
    $form_data["fio"] = trim($_POST["fio"]);
    $form_data["birth_date"] = trim($_POST["birth_date"]);
    $form_data["phone"] = trim($_POST["phone"]);
    $form_data["email"] = trim($_POST["email"]);

    if(!preg_match('/^[A-Za-z0-9]{6,}$/', $form_data["login"])){
        $errors["login"] = "Логин: минимум 6 символов, только латинские буквы и цифры.";
    }

    if(mb_strlen($password) < 8){
        $errors["password"] = "Пароль должен содержать не менее 8 символов.";
    }

    if($form_data["fio"] === ""){
        $errors["fio"] = "Укажите ФИО.";
    }

    if($form_data["birth_date"] === ""){
        $errors["birth_date"] = "Укажите дату рождения.";
    }elseif($form_data["birth_date"] > $today){
        $errors["birth_date"] = "Дата рождения не может быть больше текущей даты.";
    }

    if($form_data["phone"] === ""){
        $errors["phone"] = "Укажите контактный телефон.";
    }

    if(!filter_var($form_data["email"], FILTER_VALIDATE_EMAIL)){
        $errors["email"] = "Укажите корректный e-mail.";
    }

    $login_escaped = mysqli_real_escape_string($conn, $form_data["login"]);
    $check = mysqli_query($conn, "SELECT id FROM users WHERE login='$login_escaped'");

    if(mysqli_num_rows($check) > 0){
        $errors["login"] = "Такой логин уже существует.";
    }

    if(count($errors) === 0){
        $fio = mysqli_real_escape_string($conn, $form_data["fio"]);
        $birth_date = mysqli_real_escape_string($conn, $form_data["birth_date"]);
        $phone = mysqli_real_escape_string($conn, $form_data["phone"]);
        $email = mysqli_real_escape_string($conn, $form_data["email"]);
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        mysqli_query(
            $conn,
            "INSERT INTO users (login,password,fio,birth_date,phone,email)
             VALUES ('$login_escaped','$password_hash','$fio','$birth_date','$phone','$email')"
        );

        $success_message = "Регистрация прошла успешно.";
        $form_data = [
            "login" => "",
            "fio" => "",
            "birth_date" => "",
            "phone" => "",
            "email" => "",
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Регистрация</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="app-page">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card">
                <div class="card-body p-4">
                    <h1 class="h3 text-center mb-4">Регистрация</h1>

                    <?php if($success_message !== ""): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
                    <?php endif; ?>

                    <form method="POST" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Логин</label>
                            <input type="text" name="login" class="form-control <?= isset($errors["login"]) ? "is-invalid" : "" ?>" value="<?= htmlspecialchars($form_data["login"]) ?>" required>
                            <?php if(isset($errors["login"])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors["login"]) ?></div><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Пароль</label>
                            <input type="password" name="password" class="form-control <?= isset($errors["password"]) ? "is-invalid" : "" ?>" required>
                            <?php if(isset($errors["password"])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors["password"]) ?></div><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ФИО</label>
                            <input type="text" name="fio" class="form-control <?= isset($errors["fio"]) ? "is-invalid" : "" ?>" value="<?= htmlspecialchars($form_data["fio"]) ?>" required>
                            <?php if(isset($errors["fio"])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors["fio"]) ?></div><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Дата рождения</label>
                            <input type="date" name="birth_date" class="form-control <?= isset($errors["birth_date"]) ? "is-invalid" : "" ?>" max="<?= $today ?>" value="<?= htmlspecialchars($form_data["birth_date"]) ?>" required>
                            <?php if(isset($errors["birth_date"])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors["birth_date"]) ?></div><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Контактный номер телефона</label>
                            <input type="text" name="phone" class="form-control <?= isset($errors["phone"]) ? "is-invalid" : "" ?>" value="<?= htmlspecialchars($form_data["phone"]) ?>" required>
                            <?php if(isset($errors["phone"])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors["phone"]) ?></div><?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control <?= isset($errors["email"]) ? "is-invalid" : "" ?>" value="<?= htmlspecialchars($form_data["email"]) ?>" required>
                            <?php if(isset($errors["email"])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors["email"]) ?></div><?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
                    </form>

                    <p class="form-link mt-3 mb-0">
                        Уже зарегистрированы? <a href="login.php">Авторизация</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
