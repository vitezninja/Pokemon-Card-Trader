<?php
include('userstorage.php');
include('auth.php');
session_start();

function validate(&$data, &$errors)
{
    if (isset($_POST["username"]) && trim($_POST["username"]) !== "") {
        $data["username"] = $_POST["username"];
    } else {
        $errors["username"] = "Felhasználó név megadása kötelező!";
    }
    if (isset($_POST["email"]) && trim($_POST["email"]) !== "") {
        $data["email"] = $_POST["email"];
    } else{
        $errors["email"] = "Az email címet meg kell adni!";
    }
    if(isset($_POST["email"]) && trim($_POST["email"]) !== "" && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $data["email"] = $_POST["email"];
    } else{
        $errors["email"] = "Helyes email cím megadása kötelező!";
    }
    if (isset($_POST["password"]) && trim($_POST["password"]) !== "") {
        $data["password"] = $_POST["password"];
    } else {
        $errors["password"] = "Jelszó megadása kötelező!";
    }
    if (isset($_POST["password2"]) && trim($_POST["password2"]) !== "") {
        $data["password2"] = $_POST["password2"];
    } else {
        $errors["password2"] = "Jelszót megkell erősíteni!";
    }
    if(isset($_POST["password"]) && isset($_POST["password2"]) && $_POST["password"] != $_POST["password2"]) {
        $errors["password2"] = "Jelszók nem egyeznek!";
    }

    return count($errors) === 0;
}

$userStorage = new UserStorage();
$auth = new Auth($userStorage);

if ($auth->is_authenticated()) {
    header("Location: login.php");
    exit();
}

$errors = [];
$data = [];

if (count($_POST) > 0) {
    if (validate($data, $errors)) {
        if ($auth->user_exists($data['username'])) {
            $errors['global'] = "Az a felhasználó már létezik.";
        } else {
            $auth->register($data);
            $user = $auth->authenticate($data["username"], $data["password"]);
            $auth->login($user);
            header("Location: index.php");
            exit();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKémon | Regisztráció</title>
    <link rel="stylesheet" href="styles/main.css">
</head>

<body>
    <header>
        <h1><a href="index.php">IKémon</a> > <a href="login.php">Bejelentkezés</a> > Regisztráció</h1>
    </header>
    <div id="content">
        <p class="red aligne-center small top-margain"><?= $errors["global"] ?? "" ?></p>
        <div class="form-container">
            <form action="" method="post">
                <div class="form-row">
                    <label for="username">Felhasználó név:</label>
                    <br>
                    <input type="text" name="username" id="username" value="<?= $data["username"] ?? "" ?>" size="30">
                    <p class="red small"><?= $errors["username"] ?? "" ?></p>
                </div>

                <div class="form-row">
                    <label for="email">Email cím:</label>
                    <br>
                    <input type="text" name="email" id="email" value="<?= $data["email"] ?? "" ?>" size="30">
                    <p class="red small"><?= $errors["email"] ?? "" ?></p>
                </div>

                <div class="form-row">
                    <label for="password" class="">Jelszó:</label>
                    <br>
                    <input type="password" name="password" id="password" value="<?= $data["password"] ?? "" ?>" size="30">
                    <p class="red small"><?= $errors["password"] ?? "" ?></p>
                </div>

                <div class="form-row">
                    <label for="password2" class="">Jelszó mégegyszer:</label>
                    <br>
                    <input type="password" name="password2" id="password2" value="<?= $data["password2"] ?? "" ?>" size="30">
                    <p class="red small"><?= $errors["password2"] ?? "" ?></p>
                </div>

                <div class="aligne-center">
                    <button type="submit" class="inputButton">Regisztráció</button>
                </div>
            </form>
        </div>
    </div>
    <footer>
        <p>IKémon | ELTE IK Webprogramozás</p>
    </footer>
</body>
</html>