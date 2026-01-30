<?php

session_start();


function recover_words(){
    $words_tab = [];
    $my_file = fopen('mots.txt', 'r');
    if ($my_file){
        while (!feof($my_file)){
            $line = trim(fgets($my_file));
            array_push($words_tab, $line);
        }
    fclose ($my_file);
    }
    return $words_tab;
}

function word_choice($tab){
    $random = rand(0,count($tab)-1);
    $word = strtoupper(trim($tab[$random]));
    return $word;
}

function word_empty($word){
    $new_word = "";
    for ($i=0; $i < strlen($word); $i++) { 
        $new_word .= "_";
    }
    return trim($new_word);
}

function display_new_word() {
    return $_SESSION["new_word"];
}

function display_played_letters() {
    if (empty($_SESSION["list_played_letters"])) {
        return "Aucune lettre pour l'instant";
    }
    $result = "";
    foreach ($_SESSION["list_played_letters"] as $letter) {
        $result .= $letter . " ";
    }
    return trim($result);
}

function number_errors() {
    return $_SESSION["errors"];
}


$alphabet = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"];

if (!isset($_SESSION["list_played_letters"])) {
    $_SESSION["list_played_letters"] = [];
}

if (!isset($_SESSION["errors"])) {
    $_SESSION["errors"] = 0;
}

if (!isset($_SESSION["game"])) {
    $_SESSION["game"] = true;
}

if ($_SESSION["game"]) {
    $words_tab = recover_words();
    $_SESSION["word"] = word_choice($words_tab);
    $_SESSION["new_word"] = word_empty($_SESSION["word"]);
    $_SESSION["game"] = false;
}


if (!empty($_POST["lettre"])) {
    $lettre = $_POST["lettre"];
    if (!in_array($lettre, $_SESSION["list_played_letters"])) {
        array_push($_SESSION["list_played_letters"], $lettre);
        if (str_contains($_SESSION["word"], $lettre)) {
            for ($i = 0; $i < strlen($_SESSION["word"]); $i++) {
                if ($_SESSION["word"][$i] === $lettre) {
                    $_SESSION["new_word"][$i] = $lettre;
                }
            }
        } else {
            $_SESSION["errors"]++;
        }
    }
}


if (isset($_POST["reset"])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

if (isset($_POST["abandon"])) {
    $_SESSION["errors"] = 8;
}


?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="css/style.css">
    <title>Pendu</title>
</head>
<body>
    <header>
        <section class="container-title">
            <i class="fa-solid fa-skull-crossbones"></i>
            <h1>Le Pendu des Pirates</h1>
            <i class="fa-solid fa-skull-crossbones"></i>
        </section>
        <p>À la recherche du trésor perdu...</p>
    </header>
    <main>
        <section class="main-container">
            <div class="main-left">
                <article class="pirate-container">
                    <img src="images/pirate<?php echo number_errors(); ?>.png" alt="Image d'un pirate">
                </article>
                <article class="errors">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <p>Erreurs : <?php echo number_errors() . " / 8"; ?></p>
                </article>
            </div>
            <div class="main-right">
                <article class="word">
                    <div>
                        <h2>Mot secret</h2>
                    </div>
                    <p>
                        <?php echo display_new_word(); ?>
                    </p>
                </article>
                <article class="played-letters">
                    <div>
                        <h2>Lettres déjà essayées</h2>
                    </div>
                    <p><?php echo display_played_letters(); ?></p>
                </article>
                <article class="options-container">
                    <a href="admin.php" class="admin-link">Accéder à l'administration</a>
                    <form method="POST" class="abandon-form">
                        <button name="abandon">Abandonner la partie</button>
                    </form>
                </article>
            </div>
        </section>
        <section class="letters">
            <h2>Choisissez une lettre</h2>
            <article>
                <?php 
                foreach ($alphabet as $value) {
                    if (in_array($value, $_SESSION["list_played_letters"])) {
                        $disabled = "disabled";
                    } else {
                        $disabled = "";
                    }
                    echo '
                    <form method="POST"">
                        <input type="hidden" name="lettre" value="' . $value . '">
                        <button type="submit" ' . $disabled . '>' . $value . '</button>
                    </form>';
                }
                ?>
            </article>
        </section>
    </main>
    <?php
    if ($_SESSION["errors"] >= 8 || !str_contains($_SESSION["new_word"], "_")) {

        if ($_SESSION["errors"] >= 8) {
            $resultClass = "defeat";
            $message = "<h3>Défaite...</h3><p>Le mot était : <span>" . $_SESSION["word"] . "</span></p>";
        } else {
            $resultClass = "victory";
            $message = "<h3>Victoire !</h3><p>Le trésor est à vous, capitaine ! </p>";
        }

        echo '
        <footer class="end-game">
            <section class="' . $resultClass . '">
                ' . $message . '
                <form method="POST">
                    <button name="reset">Nouvelle partie</button>
                </form>
            </section>
        </footer>';
    }
    ?>

    
</body>
</html>