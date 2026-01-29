<?php


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


function add_word($words_tab, $new_word, $message) {
    $new_word = strtolower(trim($new_word));
    $valid = true;
    if ($new_word == "") {
        $message = "Le mot est vide.";
        $valid = false;
    } elseif (!preg_match("/^[a-zA-Z]+$/", $new_word)) {
        $message = "Le mot ne doit contenir que des lettres.";
        $valid = false;
    } elseif (in_array($new_word, $words_tab)) {
        $message = "Ce mot existe déjà.";
        $valid = false;
    }
    if ($valid) {
        $file = fopen("mots.txt","a");
        if ($file) {
            fwrite($file, "\n" . $new_word);
            fclose($file);
            array_push($words_tab, $new_word);
            $message = "Mot ajouté avec succès.";
        }
    }
    return [$words_tab, $message];
}

function delete_word($words_tab, $word_to_delete, $message) {
    if (count($words_tab) <= 1) {
        $message = "Il doit rester au moins un mot.";
        return $words_tab;
    }
    $new_tab = [];
    foreach ($words_tab as $word) {
        if ($word != $word_to_delete) {
            array_push($new_tab, $word);
        }
    }
    $file = fopen("mots.txt", "w");
    if ($file) {
        foreach ($new_tab as $index => $word) {
            if ($index == 0) {
                fwrite($file, $word);
            } else {
                fwrite($file, "\n" . $word);
            }
        }
        fclose($file);
    }
    $message = "Mot supprimé.";
    return [$new_tab, $message];
}

$message = "";
$words_tab = recover_words();

if (isset($_POST["add"])) {
    $result = add_word($words_tab, $_POST["word"], $message);
    $words_tab = $result[0];
    $message = $result[1];
}

if (isset($_POST["delete"])) {
    $result = delete_word($words_tab, $_POST["word"], $message);
    $words_tab = $result[0];
    $message = $result[1];
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
    <title>Pendu - Admin</title>
</head>
<body>
    <header>

    </header>
    <main>
        <h1>Administration du pendu</h1>
        <p><?php echo $message; ?></p>
        <h2>Ajouter un mot</h2>
        <form method="post">
            <input type="text" name="word">
            <input type="submit" name="add" value="Ajouter">
        </form>
        <h2>Supprimer un mot</h2>
        <form method="post">
            <select name="word">
                <?php foreach ($words_tab as $w) {
                    echo "<option value=\"$w\">$w</option>";
                } ?>
            </select>
            <input type="submit" name="delete" value="Supprimer">
        </form>
    </main>
    <footer>

    </footer>
</body>
</html>
