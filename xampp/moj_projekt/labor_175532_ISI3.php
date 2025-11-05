<?php

  $nr_indeksu = '175532';
  $nrGrupy = 'ISI3';

  echo 'Kacper Wasiulewski ' . $nr_indeksu . ' grupa ' . $nrGrupy . '<br/><br/>';
  echo 'Zastosowanie metody include() <br/>';

  include 'test.php';

  echo "A $color $fruit <br/> <br/>";


  echo 'Zastosowanie metody require_once()<br/>';
  require_once 'test.php';

  echo '<br/><br/>Zastosowanie warunku if<br/>';

  if ($nr_indeksu == '175532'):
    echo 'Numer indeksu jest poprawny';
  elseif (empty($nr_indeksu)):
    echo 'Zmienna jest pusta!';
  else:
    echo 'Numer indeksu jest niepoprawny';
  endif; 

  echo '<br/><br/>Zastosowanie warunku switch<br/>';
  $i = 1;

  switch ($i):
    case 0:
      echo 'Zmienna i jest równa 0';
      break;
    case 1:
      echo 'Zmienna i jest równa 1';
      break;
    case 2:
      echo 'Zmienna i jest równa 2';
      break;
    endswitch;

    echo '<br/><br/>Zastowanie pętli while<br/>';
    while ($i < 7):
      echo $i++;
      echo '<br/>';
    endwhile;

    echo '<br/><br/>Zastowanie pętli for<br/>';
    for ($k = 0; $k <= 10; $k++):
      echo "Zmienna k jest równa $k <br/>";
    endfor;

    echo '<br/><br/>Zastowanie zmiennej $_GET<br/>';
    echo 'Cześć ' . htmlspecialchars($_GET["imie"]) . '!';

    echo '<br/><br/>Zastowanie zmiennej $_POST<br/>';
    $wiek = $_POST['wiek'];
    echo "Cześć! Masz $wiek lat.";

    echo '<br/><br/>Zastowanie zmiennej $_SESSION<br/>';
    session_start();
    $_SESSION['imie'] = "Kacper";
    $_SESSION['indeks'] = 175532;
    echo "<a href='test.php'>Wynik SESSION w test.php</a>";
?>