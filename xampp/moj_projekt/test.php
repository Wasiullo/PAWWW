<?php

$color = 'green';
$fruit = 'apple';

session_start();
echo "Imię: " . $_SESSION['imie'] . "<br>";
echo "Indeks: " . $_SESSION['indeks'];
?>