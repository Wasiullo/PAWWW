<?php
    // połączenie z bazą danych
    $dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
    $baza = 'moja_strona';

    $link = mysqli_connect($dbhost, $dbuser, $dbpass);
    if (!$link) echo '<b>przerwane połączenie </b>';
    if(!mysqli_select_db($link, $baza)) echo 'nie wybrano bazy';

    //login i hasło używane do logwania do panelu CMS
    $login = 'wasiullo@wp.pl';
    $pass = '!@#QWEasdzxc';
    
?>