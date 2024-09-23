<?php
include("cardstorage.php");
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

$users = [];

if (count($_GET) > 0) {
    if (isset($_GET["filter"]) && trim($_GET["filter"]) !== "") {
        $filter = $_GET["filter"];
        $users = $userStorage->findMany(function ($otherUser) use ($filter, $user) {
            return str_contains($otherUser['username'], $filter) && $otherUser["id"] !== $user["id"];
        });
    } else {
        $users = $userStorage->findMany(function($otherUser) use ($user) {
            return $otherUser["id"] !== $user["id"];
        });
    }
} else  {
    $users = $userStorage->findMany(function($otherUser) use ($user) {
        return $otherUser["id"] !== $user["id"];
    });
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKémon | Admin felület</title>
    <link rel="stylesheet" href="styles/main.css">
</head>

<body>
    <header>
        <h1 class="left"><a href="index.php">IKémon</a> > Admin felület</h1>
        <h1 class="right"><span class="icon"><a href="logout.php">Kilépés</a></span></h1>
    </header>
    <div id="content">
        <div class="form-container2">
            <form action="cardcreate.php" method="post">
                <div class="aligne-center">
                    <button type="submit" class="inputButton" >Kártya készités</button>
                </div>
            </form>
        </div>
        <div class="form-container2">
            <form action="" method="get">
                <div class="form-row">
                    <label for="filter">Filter:</label>
                </div>
                <div class="form-row">
                    <input type="text" name="filter" id="filter" size="40" value="<?= $_GET["filter"] ?? "" ?>">
                </div>
                <div class="aligne-center">
                    <button type="submit" class="inputButton2">Szűrés</button>
                </div>
            </form>
        </div>
        <div class="top-margain">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="admin-table-th">
                            Név
                        </th>
                        <th class="admin-table-th">
                            Id
                        </th>
                        <th class="admin-table-th">
                            Kártyák
                        </th>
                        <th class="admin-table-th">
                            Email
                        </th>
                        <th class="admin-table-th">
                            Pénz
                        </th>
                        <th class="admin-table-th">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $id => $otherUser) : ?>
                        <tr>
                            <td class="admin-table-td">
                                <a href="user.php?id=<?= $id ?>"><?= $otherUser["username"] ?></a>
                            </td>
                            <td class="admin-table-td">
                                <?= $id ?>
                            </td>
                            <td class="admin-table-td">
                                <?= count($otherUser["cards"]) > 0 ? "" : "Nincs" ?>
                                <?php foreach ($otherUser["cards"] as $card) : ?>
                                    <?= $card ?>,
                                <?php endforeach ?>
                            </td>
                            <td class="admin-table-td">
                                <?= $otherUser["email"] ?>
                            </td>
                            <td class="admin-table-td">
                                <?= $otherUser["money"] ?>
                            </td>
                            <td class="admin-table-td">
                                <form action="remove.php?id=<?= $id ?>" method="post">
                                    <button type="submit" class="inputButton2">Törlés</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
    <footer>
        <p>IKémon | ELTE IK Webprogramozás</p>
    </footer>
</body>
</html>