<?php
// Wyświetlanie podstron w indexie
function PokazPodstrone($id)
{
    global $link; // Nawiązanie połączenia z bazą danych
    $id_clear = mysqli_real_escape_string($link, $id); // Czyszczenie id dla ochrony przed SQLInjection
    $query = "SELECT * FROM page_list WHERE id='$id_clear' LIMIT 1"; // Zapytanie do bazy danych z ustawionym limitem
    $result = mysqli_query($link, $query); // Wczytanie rezultatu zapytania
    $row = mysqli_fetch_array($result); // Wczytywanie wierszy z rezultatu jako tablice
    if(empty($row['id'])) // Sprawdzenie czy znaleziono podstronę
    {
        $web = '[nie_znaleziono_strony]'; // Wyświetlone, gdy podstrona nie zostanie znaleziona
    }
    else
    {
        $web = $row['page_content']; // Pobranie zawartości podstrony z bazy
    }
    return $web;
}

?>