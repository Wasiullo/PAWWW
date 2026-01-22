<?php
    session_start();
    include('cfg.php');
    include('showpage.php');
    include('contact.php');
    include('shop.php');
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.3.2/css/flag-icons.min.css">
        <meta name="description" content="Projekt 1">
        <meta name="keywords" content="HTML5, CSS3, JavaScript">
        <meta name="author" content="Kacper Wasiulewski">
        <meta charset="UTF-8">
        <script src="scripts/kolorujtlo.js" type="text/javascript"></script>
        <script src="scripts/timedate.js" type="text/javascript"></script>
        <script src="scripts/jquery-3.7.1.js" type="text/javascript"></script>
        <title>Największe budynki świata</title>
    </head>
    <body onload="startclock()">
        <header>
            <h1>Największe budynki świata</h1>
            <p>Poznaj architektoniczne rekordy globu</p>
        </header>
        <nav>
            <ul>
                <li><a href="index.php?id=1">Strona Główna</a></li>
                <li><a href="index.php?id=2">Najwyższy budynek</a></li> 
                <li><a href="index.php?id=3">Europa</a></li>
                <li><a href="index.php?id=4">Polska</a></li>
                <li><a href="index.php?id=5">Nowy Jork</a></li>
                <li><a href="index.php?id=6">Filmy</a></li>
                <li><a href="index.php?id=contact">Kontakt</a></li>
                <li><a href="index.php?id=forgot_pass">Przypomnij Hasło</a></li>
                <li><a href="index.php?id=sklep">Sklep</a></li>
                <li><a href="index.php?id=koszyk">Koszyk</a></li>
            </ul>
        </nav>
        <p style="text-align: center;">Wybierz motyw:</p> 
        <FORM METHOD="POST" NAME="background" style="text-align:center">
            <INPUT TYPE="button" VALUE="żółty" ONCLICK="changeBackground('#FFF000')">
            <INPUT TYPE="button" VALUE="czarny" ONCLICK="changeBackground('#000000')">
            <INPUT TYPE="button" VALUE="biały" ONCLICK="changeBackground('#FFFFFF')">
            <INPUT TYPE="button" VALUE="zielony" ONCLICK="changeBackground('#00FF00')">
            <INPUT TYPE="button" VALUE="niebieski" ONCLICK="changeBackground('#0000FF')">
            <INPUT TYPE="button" VALUE="pomarańczowy" ONCLICK="changeBackground('#FF8000')">
            <INPUT TYPE="button" VALUE="szary" ONCLICK="changeBackground('#c0c0c0')">                
            <INPUT TYPE="button" VALUE="czerwony" ONCLICK="changeBackground('#FF0000')">
        </FORM>
        <div style="text-align:right">
            <p>Dziś jest: </p>
            <div id="data"></div>
            <p>Godzina: </p>
            <div id="zegarek"></div>
            
        </div>
        
        <main>
            <?php
            // Wyświetlanie podstron - domyślnie podstrona z id = 1
              if (isset($_GET['id']))
              {
                  $id_strony = $_GET['id'];
                  if ($id_strony === 'contact') {
                      echo PokazKontakt();
                  } elseif ($id_strony === 'forgot_pass') {
                      echo PrzypomnijHaslo();
                  } elseif ($id_strony === 'sklep'){
                      echo PokazProduktySklep($link); 
                  } elseif ($id_strony === 'koszyk'){
                      echo showCart(); 
                  }else {
                      $tresc_strony = PokazPodstrone($id_strony);
                      echo $tresc_strony;
                  }
              }
              else
              {

                  $tresc_strony = PokazPodstrone(1); 
                  echo $tresc_strony;
              }
            ?>
        </main>
        <footer>
            <div class="kontakt">
                <p>Kontakt</p>
                <p>☎️ 698-229-701</p>
                <p>✉️ kacper.wasiulewski@student.uwm.edu.pl</p>
            </div>
            <div>
                <p>&copy; 2026 Kacper Wasiulewski - Wszelkie prawa zastrzeżone</p>
            </div>
                <a href="index.php"><img src="img/logo.png" alt="logo" class="logo"></a>
        </footer>
        <?php
            $nr_indeksu = '175532';
            $nrGrupy = 'ISI3';
            echo 'Autor: Kacper Wasiulewski '.$nr_indeksu.' grupa '.$nrGrupy.' <br /><br />';
        ?>

    </body>
</html>