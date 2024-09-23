<?php
include("cardstorage.php");
include("userstorage.php");
include("auth.php");

session_start();


$userStorage = new UserStorage();
$auth = new Auth($userStorage);

if ($auth->is_authenticated()) {
    $user = $auth->authenticated_user();
}

$cardStorage = new CardStorage();
$cards = $cardStorage->findMany(function ($card) {
    return true;
});

$admin = array_values($userStorage->findAdmin())[0];

if (isset($user)){
    $data = $userStorage->findById($user["id"]);
}

if (count($_GET) > 0) {
    if (isset($_GET["filter"]) && trim($_GET["filter"]) !== "") {
        $filter = $_GET["filter"];
        $cards = $cardStorage->findMany(function ($card) use ($filter) {
            return $card["type"] == $filter;
        });
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKémon | Home</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/cards.css">
</head>

<body>
    <header>
        <h1 class="left"><a href="index.php">IKémon</a> > Home</h1>
        <?php if (isset($user)) : ?>
            <?php $_SESSION["id"] = $user["id"] ?>
            <?php if ($auth->authorize(["admin"])) : ?>
                <h1 class="right"><span class="icon"><a href="user.php"><?= $user["username"] ?></a> | <a href="adminpanel.php">Admin felület</a> | <a href="logout.php">Kilépés</a></span></h1>
            <?php else :?>
                <h1 class="right"><span class="icon"><a href="user.php"><?= $user["username"] ?></a> | Pénz: <?= $data["money"] ?> | <a href="logout.php">Kilépés</a></span></h1>
            <?php endif ?>
        <?php else :?>
            <h1 class="right"><a href="login.php">Bejelentkezés</a></h1>
        <?php endif ?> 
    </header>
    <div id="content2">
        <div class="form-container">
            <form action="" method="get">
                <div class="form-row">
                    <label for="filter">Filter:</label>
                </div>
                <div class="form-row">
                    <select name="filter" id="filter" class="five">
                        <?php if(isset($_GET["filter"]) && trim($_GET["filter"]) !== "") : ?>
                            <option value="<?= $_GET["filter"] ?>"><?= $_GET["filter"] ?></option>
                        <?php endif ?>
                        <?php if(isset($_GET["filter"]) && trim($_GET["filter"]) == "") : ?>
                            <option value="">-- Válassz --</option>
                        <?php endif ?>
                        <?php if(isset($_GET["filter"]) && trim($_GET["filter"]) !== "normal") : ?>
                            <option value="normal">normal</option>
                        <?php endif ?>
                        <?php if(isset($_GET["filter"]) && trim($_GET["filter"]) !== "fire") : ?>                            
                            <option value="fire">fire</option>
                        <?php endif ?>
                        <?php if(isset($_GET["filter"]) && trim($_GET["filter"]) !== "water") : ?>
                            <option value="water">water</option>
                        <?php endif ?>
                        <?php if(isset($_GET["filter"]) && trim($_GET["filter"]) !== "electric") : ?>
                            <option value="electric">electric</option>
                        <?php endif ?>
                        <?php if(isset($_GET["filter"]) && trim($_GET["filter"]) !== "grass") : ?>
                            <option value="grass">grass</option>
                        <?php endif ?>
                        <?php if(isset($_GET["filter"]) && trim($_GET["filter"]) !== "ice") : ?>
                            <option value="ice">ice</option>
                        <?php endif ?>
                        <?php if(isset($_GET["filter"]) && trim($_GET["filter"]) !== "fighting") : ?>
                            <option value="fighting">fighting</option>
                        <?php endif ?>
                        <?php if(isset($_GET["filter"]) && trim($_GET["filter"]) !== "poison") : ?>
                            <option value="poison">poison</option>                        
                        <?php endif ?>
                        <?php if(isset($_GET["filter"]) && trim($_GET["filter"]) !== "ground") : ?>
                            <option value="ground">ground</option>
                        <?php endif ?>
                        <?php if(isset($_GET["filter"]) && trim($_GET["filter"]) !== "psychic") : ?>
                            <option value="psychic">psychic</option>
                        <?php endif ?>
                        <?php if(isset($_GET["filter"]) && trim($_GET["filter"]) !== "bug") : ?>
                            <option value="bug">bug</option>
                        <?php endif ?>
                        <?php if(isset($_GET["filter"]) && trim($_GET["filter"]) !== "rock") : ?>
                            <option value="rock">rock</option>                        <?php endif ?>
                        <?php if(isset($_GET["filter"]) && trim($_GET["filter"]) !== "ghost") : ?>
                            <option value="ghost">ghost</option>
                        <?php endif ?>
                        <?php if(isset($_GET["filter"]) && trim($_GET["filter"]) !== "dark") : ?>
                            <option value="dark">dark</option>
                        <?php endif ?>
                        <?php if(isset($_GET["filter"]) && trim($_GET["filter"]) !== "steel") : ?>                            
                            <option value="steel">steel</option>
                        <?php endif ?>
                        <?php if(isset($_GET["filter"]) && trim($_GET["filter"]) !== "") : ?>
                            <option value="">-- Válassz --</option>
                        <?php endif ?>
                        <?php if(!isset($_GET["filter"])) : ?>
                            <option value="">-- Válassz --</option>
                            <option value="normal">normal</option>
                            <option value="fire">fire</option>
                            <option value="water">water</option>
                            <option value="electric">electric</option>
                            <option value="grass">grass</option>
                            <option value="ice">ice</option>
                            <option value="fighting">fighting</option>
                            <option value="poison">poison</option>
                            <option value="ground">ground</option>
                            <option value="psychic">psychic</option>
                            <option value="bug">bug</option>
                            <option value="rock">rock</option>
                            <option value="ghost">ghost</option>
                            <option value="dark">dark</option>
                            <option value="steel">steel</option>
                        <?php endif ?>
                    </select>
                </div>
                <div class="aligne-center"> 
                    <button type="submit" class="inputButton">Szűrés</button>
                </div>
            </form>
        </div>
    </div>
    <div id="content">
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
                    <?php if (isset($user) && !$auth->authorize(["admin"]) && in_array($id, $admin["cards"])) : ?>
                        <a href="buy.php?id=<?= $user["id"] ?>&card=<?= $id ?>" class="link">
                            <div class="buy">
                                <span class="card-price"><span class="icon">💰</span> <?= $card["price"] ?></span>
                            </div>
                        </a>
                    <?php else :?>
                        <div class="grey">
                            <span class="card-price"><span class="icon">💰</span> <?= $card["price"] ?></span>
                        </div>
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