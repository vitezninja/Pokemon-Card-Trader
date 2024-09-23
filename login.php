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

    if (isset($_POST["password"]) && trim($_POST["password"]) !== "") {
        $data["password"] = $_POST["password"];
    } else {
        $errors["password"] = "Jelszó megadása kötelező!";
    }

    return count($errors) === 0;
}

$user_storage = new UserStorage();
$auth = new Auth($user_storage);

if ($auth->is_authenticated()) {
    header("Location: index.php");
    exit();
}

$errors = [];
$data = $_SESSION["user"] ?? [];
if (count($_POST) > 0) {
    if (validate($data, $errors)) {
        if ($auth->user_exists($data["username"])) {
            $user = $auth->authenticate($data["username"], $data["password"]);
            if ($user == NULL) {
                $errors["global"] = "Felhasználóhoz tartozó jelszó helytelen!";
            }   
            else {
                $auth->login($user);
                header("Location: index.php");
                exit();
            }
        } 
        else {
            $errors["global"] = "Nincs a megadott névhez tartozó felhasználó!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKémon | Bejelentkezés</title>
    <link rel="stylesheet" href="styles/main.css">
</head>

<body>
    <header>
        <h1><a href="index.php">IKémon</a> > Bejelentkezés</h1>
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
                    <label for="password" class="">Jelszó:</label>
                    <br>
                    <input type="password" name="password" id="password" value="<?= $data["password"] ?? "" ?>" size="30">
                    <p class="red small"><?= $errors["password"] ?? "" ?></p>
                </div>

                <div class="aligne-center">
                    <button type="submit" class="inputButton">Bejelentkezés</button>
                </div>
            </form>
        </div>
        <div class="form-container2">
            <form action="registration.php" method="post">
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