<?php
include("cardstorage.php");
include("userstorage.php");
include("auth.php");

session_start();

function validate(&$data, &$errors, &$cardIDs)
{
    if (isset($_POST["id"]) && trim($_POST["id"]) !== "") {
        $data["id"] = $_POST["id"];
        if(in_array($data["id"], $cardIDs)){
            $errors["global"] = "Ez a kártya id már létezik!";
        }
    } else {
        $errors["id"] = "A kártya idjának a megadása kötelező!";
    }

    if (isset($_POST["cardname"]) && trim($_POST["cardname"]) !== "") {
        $data["cardname"] = $_POST["cardname"];
    } else {
        $errors["cardname"] = "A kártya névének megadása kötelező!";
    }

    if (isset($_POST["type"]) && trim($_POST["type"]) !== "") {
        $data["type"] = $_POST["type"];
    } else {
        $errors["type"] = "A típus megadása kötelező!";
    }

    if (isset($_POST["hp"]) && $_POST["hp"] > 0) {
        $data["hp"] = $_POST["hp"];
    } else {
        $errors["hp"] = "Az életpont megadása kötelező és nagyobbnak kell lenie mint 0!";
    }

    if (isset($_POST["attack"]) && $_POST["attack"] > 0) {
        $data["attack"] = $_POST["attack"];
    } else {
        $errors["attack"] = "A támadás megadása kötelező és nagyobbnak kell lenie mint 0!";
    }

    if (isset($_POST["defense"]) && $_POST["defense"] > 0) {
        $data["defense"] = $_POST["defense"];
    } else {
        $errors["defense"] = "A védelem megadása kötelező és nagyobbnak kell lenie mint 0!";
    }

    if (isset($_POST["price"]) && $_POST["price"] > 0) {
        $data["price"] = $_POST["price"];
    } else {
        $errors["price"] = "Az ár megadása kötelező és nagyobbnak kell lenie mint 0!";
    }

    if (isset($_POST["description"]) && trim($_POST["description"]) !== "") {
        $data["description"] = $_POST["description"];
    } else {
        $errors["description"] = "A kártya lerísínak a megadása kötelező!";
    }

    if (isset($_POST["image"]) && trim($_POST["image"]) !== "") {
        $data["image"] = $_POST["image"];
    } else {
        $errors["image"] = "A kártya képének a megadása kötelező!";
    }

    return count($errors) === 0;
}

$userStorage = new UserStorage();
$auth = new Auth($userStorage);

$cardStorage = new CardStorage();
$cards = $cardStorage->findMany(function ($card) {
    return true;
});
$cardIDs = [];
foreach ($cards as $id => $card) {
    array_push($cardIDs, $id);
}

if ($auth->is_authenticated() && $auth->authorize(["admin"])) {
    $user = $auth->authenticated_user();
} else {
    header("Location: index.php");
    exit();
}

$errors = [];
$data = [];
$admin = array_values($userStorage->findAdmin())[0];

if (count($_POST) > 0) {

    if (validate($data, $errors, $cardIDs)) {

        $cardStorage->add($data);
        array_push($admin["cards"], $data["id"]);
        $userStorage->update($admin["id"], $admin);

        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKémon | Kártya kreáció</title>
    <link rel="stylesheet" href="styles/main.css">
</head>

<body>
    <header>
        <h1 class="left"><a href="index.php">IKémon</a> > <a href="adminpanel.php">Admin felület</a> > Kártya kreáció</h1>
        <h1 class="right"><span class="icon"><a href="logout.php">Kilépés</a></span></h1>
    </header>
    <div id="content">
        <p class="red aligne-center small top-margain"><?= $errors["global"] ?? "" ?></p>
        <div class="form-container">
            <form action="" method="post">
                <div class="form-row">
                    <label for="id">Pokemon id:</label>
                    <br>
                    <input type="text" name="id" id="id" value="<?= $data["id"] ?? "" ?>" size="30">
                    <p class="red small"><?= $errors["id"] ?? ""  ?></p>
                </div>

                <div class="form-row">
                    <label for="cardname">Pokemon neve:</label>
                    <br>
                    <input type="text" name="cardname" id="cardname" value="<?= $data["cardname"] ?? "" ?>" size="30">
                    <p class="red small"><?= $errors["cardname"] ?? ""  ?></p>
                </div>

                <div class="form-row">
                    <label for="type">Típus:</label>
                    <br>
                    <select name="type" id="type" class="eight">
                        <?php if(isset($_POST["type"]) && trim($_POST["type"]) !== "") : ?>
                            <option value="<?= $_POST["type"] ?>"><?= $_POST["type"] ?></option>
                        <?php endif ?>
                        <?php if(isset($_POST["type"]) && trim($_POST["type"]) == "") : ?>
                            <option value="">-- Válassz --</option>
                        <?php endif ?>
                        <?php if(isset($_POST["type"]) && trim($_POST["type"]) !== "normal") : ?>
                            <option value="normal">normal</option>
                        <?php endif ?>
                        <?php if(isset($_POST["type"]) && trim($_POST["type"]) !== "fire") : ?>                            
                            <option value="fire">fire</option>
                        <?php endif ?>
                        <?php if(isset($_POST["type"]) && trim($_POST["type"]) !== "water") : ?>
                            <option value="water">water</option>
                        <?php endif ?>
                        <?php if(isset($_POST["type"]) && trim($_POST["type"]) !== "electric") : ?>
                            <option value="electric">electric</option>
                        <?php endif ?>
                        <?php if(isset($_POST["type"]) && trim($_POST["type"]) !== "grass") : ?>
                            <option value="grass">grass</option>
                        <?php endif ?>
                        <?php if(isset($_POST["type"]) && trim($_POST["type"]) !== "ice") : ?>
                            <option value="ice">ice</option>
                        <?php endif ?>
                        <?php if(isset($_POST["type"]) && trim($_POST["type"]) !== "fighting") : ?>
                            <option value="fighting">fighting</option>
                        <?php endif ?>
                        <?php if(isset($_POST["type"]) && trim($_POST["type"]) !== "poison") : ?>
                            <option value="poison">poison</option>                        
                        <?php endif ?>
                        <?php if(isset($_POST["type"]) && trim($_POST["type"]) !== "ground") : ?>
                            <option value="ground">ground</option>
                        <?php endif ?>
                        <?php if(isset($_POST["type"]) && trim($_POST["type"]) !== "psychic") : ?>
                            <option value="psychic">psychic</option>
                        <?php endif ?>
                        <?php if(isset($_POST["type"]) && trim($_POST["type"]) !== "bug") : ?>
                            <option value="bug">bug</option>
                        <?php endif ?>
                        <?php if(isset($_POST["type"]) && trim($_POST["type"]) !== "rock") : ?>
                            <option value="rock">rock</option>                        <?php endif ?>
                        <?php if(isset($_POST["type"]) && trim($_POST["type"]) !== "ghost") : ?>
                            <option value="ghost">ghost</option>
                        <?php endif ?>
                        <?php if(isset($_POST["type"]) && trim($_POST["type"]) !== "dark") : ?>
                            <option value="dark">dark</option>
                        <?php endif ?>
                        <?php if(isset($_POST["type"]) && trim($_POST["type"]) !== "steel") : ?>                            
                            <option value="steel">steel</option>
                        <?php endif ?>
                        <?php if(isset($_POST["type"]) && trim($_POST["type"]) !== "") : ?>
                            <option value="">-- Válassz --</option>
                        <?php endif ?>
                        <?php if(!isset($_POST["type"])) : ?>
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
                    <p class="red small"><?= $errors["type"] ?? ""  ?></p>
                </div>

                <div class="form-row">
                    <label for="hp">Élet pontok:</label>
                    <br>
                    <input type="number" name="hp" id="hp" value="<?= $data["hp"] ?? 0 ?>" size="30">
                    <p class="red small"><?= $errors["hp"] ?? ""  ?></p>
                </div>

                <div class="form-row">
                    <label for="attack">Támadás:</label>
                    <br>
                    <input type="number" name="attack" id="attack" value="<?= $data["attack"] ?? 0 ?>" size="30">
                    <p class="red small"><?= $errors["attack"] ?? ""  ?></p>
                </div>

                <div class="form-row">
                    <label for="defense">Védelem:</label>
                    <br>
                    <input type="number" name="defense" id="defense" value="<?= $data["defense"] ?? 0 ?>" size="30">
                    <p class="red small"><?= $errors["defense"] ?? ""  ?></p>
                </div>

                <div class="form-row">
                    <label for="price">Ár:</label>
                    <br>
                    <input type="number" name="price" id="price" value="<?= $data["price"] ?? 0 ?>" size="30">
                    <p class="red small"><?= $errors["price"] ?? ""  ?></p>
                </div>

                <div class="form-row">
                    <label for="description">Leírás:</label>
                    <br>
                    <textarea type="text" name="description" id="description" class="eight four"><?= $data["description"] ?? "" ?></textarea>
                    <p class="red small"><?= $errors["description"] ?? ""  ?></p>
                </div>

                <div class="form-row">
                    <label for="image">Kép:</label>
                    <br>
                    <input type="text" name="image" id="image" value="<?= $data["image"] ?? "" ?>" size="30">
                    <p class="red small"><?= $errors["image"] ?? ""  ?></p>
                </div>

                <div class="aligne-center">  
                    <button type="submit" class="inputButton">Készités</button>
                </div>
            </form>
        </div>
    </div>
    <footer>
        <p>IKémon | ELTE IK Webprogramozás</p>
    </footer>
</body>
</html>