<?php
    include("cardstorage.php");
    include('userstorage.php');
    include('auth.php');

    session_start();

    $cardStorage = new CardStorage();
    $userStorage = new UserStorage();
    $auth = new Auth($userStorage);

    if ($auth->is_authenticated() && !$auth->authorize(["admin"])) {
        $user = $auth->authenticated_user();
    } else {
        header("Location: index.php");
        exit();
    }

    if(count($_GET) !== 2) {
        header("Location: index.php");
        exit();
    }

    $user2 = $userStorage->findById($user["id"]);
    if(count($user2["cards"]) >= 5) {
        header("Location: index.php");
        exit();
    }

    $users = $userStorage->findMany(function ($user) {
        return true;
    });
    $userIds = [];
    foreach ($users as $id => $userData){
        array_push($userIds, $id);
    }


    if(isset($_GET["id"]) && trim($_GET["id"]) !== "") {
        if (in_array($_GET["id"], $userIds)) {
            $userID = $_GET["id"];
        } else {
            header("Location: index.php");
            exit();
        }
    } else {
        header("Location: index.php");
        exit();
    }

    $admin = array_values($userStorage->findAdmin())[0];

    $cardsForSale = $cardStorage->findAdminCards($admin["cards"]);

    if(isset($_GET["card"]) && trim($_GET["card"]) !== "") {
        if (in_array($_GET["card"], $admin["cards"])) {
            $card = $_GET["card"];
        } else {
            header("Location: index.php");
            exit();
        }
    } else {
        header("Location: index.php");
        exit();
    }

    $cardExists = $cardStorage->findById($card)["price"];

    if($cardExists > $user2["money"]) {
        header("Location: index.php");
        exit();
    }

    $data = [];
    $userData = $userStorage->findById($admin["id"]);
    $data["username"] = $userData["username"];
    $data["password"] = $userData["password"];
    $data["id"] = $userData["id"];
    $data["roles"] = $userData["roles"];
    $data["cards"] = [];
    foreach ($userData["cards"] as $cardID) {
        if($cardID !== $card) {
            array_push($data["cards"], $cardID);
        }
    }
    $data["money"] = (int)$userData["money"];
    $data["email"] = $userData["email"];
    $userStorage->update($admin["id"], $data);

    $data2 = [];
    $userData2 = $userStorage->findById($userID);
    $data2["username"] = $userData2["username"];
    $data2["password"] = $userData2["password"];
    $data2["id"] = $userData2["id"];
    $data2["roles"] = $userData2["roles"];
    $data2["cards"] = $userData2["cards"];
    array_push($data2["cards"], $card);
    $data2["money"] = (int)$userData2["money"]-$cardExists;
    $data2["email"] = $userData2["email"];
    $userStorage->update($userID, $data2);

    header("Location: index.php");
    exit();
?>