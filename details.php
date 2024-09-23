<?php
include("cardstorage.php");

$card = [];

if (count($_GET) > 0) {
    if (isset($_GET["id"]) && trim($_GET["id"]) !== "") {

        $cardStorage = new CardStorage();
        $card = $cardStorage->findById($_GET["id"]);
    } else {
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
};

if ($card === NULL){
    header("Location: index.php");
    exit();
};

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKémon | <?= $card["name"] ?></title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/details.css">
</head>

<body class="clr-<?= $card["type"]?>">
    <header>
        <h1><a href="index.php">IKémon</a> > <?= $card["name"] ?></h1>
    </header>
    <div id="content">
        <div id="details" class="clr-white">
            <div class="image clr-<?= $card["type"] ?>">
                <img src="<?= $card["image"] ?>" alt="<?= $card["name"] ?>_image">
            </div>
            <div class="info">
                <h2 class="aligne-center no-margins"><?= $card["name"] ?></h2>
                <div class="description"><?= $card["description"] ?></div>
                <span class="card-type"><span class="icon">🏷</span> Type: <?= $card["type"] ?></span>
                <div class="attributes">
                    <div class="card-hp"><span class="icon">❤</span> Health: <?= $card["hp"] ?></div>
                    <div class="card-attack"><span class="icon">⚔</span> Attack: <?= $card["attack"] ?></div>
                    <div class="card-defense"><span class="icon">🛡</span> Defense: <?= $card["defense"] ?></div>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <p>IKémon | ELTE IK Webprogramozás</p>
    </footer>
</body>
</html>