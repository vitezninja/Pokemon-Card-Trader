<?php
include("userstorage.php");
include("auth.php");

session_start();

$userStorage = new UserStorage();
$auth = new Auth($userStorage);

if ($auth->is_authenticated() && $auth->authorize(["admin"])) {
    $user = $auth->authenticated_user();
} else {
    header("Location: index.php");
    exit();
}

$adminID = array_values($userStorage->findAdmin())[0]["id"];

if (count($_GET) > 0) {
    if (isset($_GET["id"]) && trim($_GET["id"]) !== '') {

        $id = $_GET["id"];
        if($id == $adminID) {
            header("Location: index.php");
            exit();
        } else {
            $userStorage->delete($id);
        }
    } else {
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

header("Location: adminpanel.php");
exit();