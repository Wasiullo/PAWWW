<?php
    $folder = 'html/';
    $strona = $folder . 'glowna.html';
    if (isset($_GET['idp'])) 
    {
        $idp = $_GET['idp'];
        $dozwolone = [
            'glowna'    => 'glowna.html',
            'nbswiat'   => 'nbswiat.html',
            'nbeuropy'  => 'nbeuropy.html',
            'nbpolski'  => 'nbpolski.html',
            'nbnyc'     => 'nbnyc.html',
            'filmy'     => 'filmy.html'
        ];
        if (array_key_exists($idp, $dozwolone)) 
        {
            $plik = $folder . $dozwolone[$idp];
            if (file_exists($plik)) {
                $strona = $plik;
            }
        }
    }
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
                <li><a href="index.php">Strona Główna</a></li>
                <li><a href="index.php?idp=nbswiat">Najwyższy budynek</a></li> 
                <li><a href="index.php?idp=nbeuropy">Europa</a></li>
                <li><a href="index.php?idp=nbpolski">Polska</a></li>
                <li><a href="index.php?idp=nbnyc">Nowy Jork</a></li>
                <li><a href="index.php?idp=filmy">Filmy</a></li>
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
        <div id="animacjaTestowa1" class=test-block>Kliknij, a się powiększę</div>
        <script>
            $("#animacjaTestowa1").on("click", function(){
                $(this).animate({
                    width: "500px",
                    opacity: 0.4,
                    fontsize: "3em",
                    borderwith: "10px"
                }, 1500);
            });
        </script>
        <div id="animacjaTestowa2", class="test-block"> Najedź kursorem, a się powiększe</div>
        <script>
        $("#animacjaTestowa2").on({
            "mouseover": function() {
                $(this).animate({
                    width: 300
                }, 800);
        },
            "mouseout": function() {
                $(this).animate({
                width: 200
              }, 800);
            }
        });
        </script>
        <div id="animacjaTestowa3" class="test-block">Kliknij, abym urósł</div>
        <script>
            $("#animacjaTestowa3").on("click", function(){
                if (!$(this).is(":animated")) {
                    $(this).animate({
                        width: "+=" + 50,
                        height: "+=" + 10,
                        opacity: "-=" + 0.1,
                        duration : 15
                    });
                }
            });
        </script>
        <main>
            <?php include($strona); ?>
        </main>
        <footer>
            <div class="kontakt">
                <p>Kontakt</p>
                <p>☎️ 698-229-701</p>
                <p>✉️ kacper.wasiulewski@student.uwm.edu.pl</p>
            </div>
            <div>
                <p>&copy; 2025 Kacper Wasiulewski - Wszelkie prawa zastrzeżone</p>
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