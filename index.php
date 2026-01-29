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
    return $new_word;
}

function display_new_word() {
    return $_SESSION["new_word"];
}

function display_played_letters() {
    if (empty($_SESSION["list_played_letters"])) {
        return "Aucune";
    }
    $result = "";
    foreach ($_SESSION["list_played_letters"] as $letter) {
        $result .= $letter . " ";
    }
    return trim($result);
}

function number_errors() {
    return $_SESSION["errors"] . " / 8";
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


?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <title>Pendu</title>
</head>
<body>
    <header>
        
    </header>
    <main>

        <section>
            <h2>Mot à trouver</h2>
            <p style="font-size:2rem; letter-spacing:10px;">
                <?php echo display_new_word(); ?>
            </p>
        </section>
        <section>
            <h3>Lettres déjà jouées</h3>
            <p><?php echo display_played_letters(); ?></p>
        </section>
        <section>
            <h3>Nombre d'errors</h3>
            <p><?php echo number_errors(); ?></p>
        </section>
        <section>
            <?php
            if (!str_contains($_SESSION["new_word"], "_")) {
                echo "<h3>Victoire !</h3>";
            }
            if ($_SESSION["errors"] >= 8) {
                echo "<h3>Perdu ! Le mot était : " . $_SESSION["word"] . "</h3>";
            }
            ?>
        </section>
        <section>
            <?php 
            foreach ($alphabet as $value) {
                if (in_array($value, $_SESSION["list_played_letters"])) {
                    $disabled = "disabled";
                } else {
                    $disabled = "";
                }
                echo '
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="lettre" value="' . $value . '">
                    <button type="submit" ' . $disabled . '>' . $value . '</button>
                </form>';
            }
            ?>
        </section>
    </main>
    <footer>
        <form method="POST">
            <button name="reset">Nouvelle partie</button>
        </form>
    </footer>
    
</body>
</html>