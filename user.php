<?php
include("cardstorage.php");
include("userstorage.php");
include("auth.php");

session_start();

$userStorage = new UserStorage();
$auth = new Auth($userStorage);

if ($auth->is_authenticated()) {
    $user = $auth->authenticated_user();
} else {
    header("Location: index.php");
    exit();
}

if(isset($_GET["id"]) && $auth->authorize(["admin"])){
    $getUsed = true;
    $data = $userStorage->findById($_GET["id"]);
    if($data == NULL){
        header("Location: index.php");
        exit();
    }
} else if (isset($_SESSION["id"]) && trim($_SESSION["id"]) !== "") {
    $getUsed = false;
    $data = $userStorage->findById($_SESSION["id"]);
    if($data == NULL){
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

$cardStorge = new CardStorage();
$cards = [];

foreach ($data["cards"] as $cardid) {
    $cards["$cardid"] = $cardStorge->findById($cardid);
};

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKémon | <?= $data["username"] ?></title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/cards.css">
</head>

<body>
    <header>
        <?php if($getUsed) : ?>
            <h1 class="left"><a href="index.php">IKémon</a> > <a href="adminpanel.php">Admin felület</a> > <?= $data["username"] ?></h1>
            <h1 class="right"><span class="icon"><a href="logout.php">Kilépés</a></span></h1>
        <?php else : ?>
            <h1 class="left"><a href="index.php">IKémon</a> > <?= $data["username"] ?> </h1>
            <h1 class="right"><span class="icon"><a href="logout.php">Kilépés</a></span></h1>
        <?php endif ?>
    </header>
    <div id="content2">
        <div class="aligne-center">
            <table>
                <thead>
                    <tr>
                        <th>
                            Név
                        </th>
                        <th>
                            Email
                        </th>
                        <th>
                            Pénz
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?= $data["username"] ?>
                        </td>
                        <td>
                            <?= $data["email"] ?>
                        </td>
                        <td>
                            <?php if(in_array("admin", $data["roles"])) : ?>
                                💰 ∞
                            <?php else : ?>
                                💰 <?= $data["money"] ?>
                            <?php endif ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div id="content">
        <h2 class="aligne-center">Kártyáid:</h2>
        <?php if(in_array("admin", $data["roles"])) : ?>
            <p class="aligne-center"><?= count($data["cards"]) ?> / ∞</p>
        <?php else : ?>
            <p class="aligne-center"><?= count($data["cards"]) ?> / 5</p>
        <?php endif ?>
        <div id="card-list">
            <?php foreach ($cards as $id => $card) : ?>
                <div class="pokemon-card">
                    <div class="image clr-<?= $card["type"] ?>">
                        <img src="<?= $card["image"] ?>" alt="<?= $card["name"] ?>_image">
                    </div>
                    <div class="details">
                        <h2><a href="details.php?id=<?= $id ?>"><?= $card["name"] ?></a></h2>
                        <span class="card-type"><span class="icon">🏷</span> <?= $card["type"] ?></span>
                        <span class="attributes">
                            <span class="card-hp"><span class="icon">❤</span> <?= $card["hp"] ?></span>
                            <span class="card-attack"><span class="icon">⚔</span> <?= $card["attack"] ?></span>
                            <span class="card-defense"><span class="icon">🛡</span> <?= $card["defense"] ?></span>
                        </span>
                    </div>
                    <?php if ($auth->authorize(["admin"])) : ?>
                        <div class="grey">
                            <span class="card-price"><span class="icon">💰</span> <?= $card["price"] ?></span>
                        </div>
                    <?php else :?>
                        <a href="sell.php?id=<?= $data["id"] ?>&card=<?= $id ?>" class="link">
                            <div class="sell">
                                <span class="card-price"><span class="icon">💰</span> <?= (int)($card["price"]*0.9) ?></span>
                            </div>
                        </a>
                    <?php endif ?>
                    
                </div>
            <?php endforeach ?>
            <?= count($cards) > 0 ? "" : "Nincs" ?>
        </div>
    </div>
    <footer>
        <p>IKémon | ELTE IK Webprogramozás</p>
    </footer>
</body>
</html>