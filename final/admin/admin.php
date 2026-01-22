<?php
session_start(); // Rozpoczęcie sesji
include '../cfg.php'; // Wczytanie pliku cfg

$error_message = ''; // Inicjacja zmiennej przechowującej błąd

if (!isset($_SESSION['logged_in'])) // Jeżeli nie jest się zalogowanym
{
    if((isset($_POST['xl_submit'])))
    {
        $input_login = isset($_POST['login_email']) ? trim($_POST['login_email']) : ''; // Pobranie podanego loginu
        $input_pass = isset($_POST['login_pass']) ? trim($_POST['login_pass']) : ''; // Pobranie podanego hasła
        if ($input_login === $login && $input_pass === $pass) 
        {
            $_SESSION['logged_in'] = true; // Zalogowanie w przypadku podania poprawnego loginu i hasła
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();

        } else {
            // Komunikat o błędzie w przypadku podania błędnego loginu lub hasła
            $error_message = '<p style="color: red;">Podano nieprawidłowy e-mail lub hasło.</p>';
        }
    }
}


if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true)  // Jeżeli jest się zalogowanym
{
    $catManager = new CategoryManager(); // Inicjalizacja klasy zarządzenia kategoriami
    $prodManager = new ProductManager(); // Inicjalizacja klasy zarządzania produktami

    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $content = '';

    // Wyświetlanie wyboru ekranu - zadządzania podstronami lub kategoriami
    echo '<a href="admin.php">Zarządzaj Podstronami (Strona)</a> | ';
    echo '<a href="admin.php?action=kategorie_list">Zarządzaj Kategoriami (Sklep)</a> | ';
    echo '<a href="admin.php?action=produkty_list">Zarządzaj produktami (Sklep)</a>';


    if (isset($_POST['edit_submit'])) // Panel edycji podstrony
    {
        $id_update = isset($_POST['id_edycji']) ? (int)$_POST['id_edycji'] : 0; // Aktualizacja ID podstrony
        $new_title = mysqli_real_escape_string($link, $_POST['page_title']); // Nowy tytuł podstrony
        $new_content = mysqli_real_escape_string($link, $_POST['page_content']); // Nowa zawartość podstrony
        $new_status = isset($_POST['status']) ? 1 : 0; // Nowy status podstrony
        // Zapytanie do bazy danych w celu aktualizacji podstrony
        $query_update = "UPDATE page_list SET 
            page_title = '$new_title', 
            page_content = '$new_content', 
            status = $new_status 
            WHERE id = $id_update LIMIT 1";
        if (mysqli_query($link, $query_update)) {
            $content = '<p style="color: green;">Strona o ID ' . $id_update . ' została zaktualizowana.</p>'; // Wyświetlenie komunikatu o aktualizacji podstrony i przydzieleniu jej nowego ID
            $action = '';
        } else {
            $content = '<p style="color: red;">Wystąpił błąd: ' . mysqli_error($link) . '</p>'; // Wyświetlenie komunikatu o błędzie
        }
    }
    if (isset($_POST['add_submit'])) //Panel dodawania podstrony
    {
        $new_title = mysqli_real_escape_string($link, $_POST['page_title']); // Tytuł nowej podstrony
        $new_content = mysqli_real_escape_string($link, $_POST['page_content']); // Zawartość nowej podstrony
        $new_status = isset($_POST['status']) ? 1 : 0; // Status nowej podstrony
        
        // Zapytanie do bazy danych w celu utworzenia nowej podstrony
        $query_insert = "INSERT INTO page_list (page_title, page_content, status) 
                         VALUES ('$new_title', '$new_content', $new_status)";
        
        if (mysqli_query($link, $query_insert)) {
            $content = '<p style="color: green;">Utworzono nową stronę o ID: ' . mysqli_insert_id($link) . '.</p>'; // Wyświetlenie komunikatu o utworzeniu nowej podstrony i jej ID
            $action = '';
        } else {
            $content = '<p style="color: red;">Wystąpił błąd: ' . mysqli_error($link) . '</p>'; // Wyświetlenie komunikatu o błędzie
        }
    }

    if (isset($_POST['cat_add_submit'])) // Panel dodawania kategorii - działanie
    {
        if($catManager->dodajKategorie($link, $_POST['matka'], $_POST['nazwa'])) {
            $content = '<p style="color: green;">Dodano kategorię.</p>';
        } else {
            $content = '<p style="color: red;">Wystąpił błąd podczas dodawania.</p>';
        }
        $action = 'kategorie_list';
    }
    if (isset($_POST['cat_edit_submit'])) // Panel edycji kategorii - działanie
    {
        if($catManager->edytujKategorie($link, $_POST['id_edycji'], $_POST['matka'], $_POST['nazwa'])) {
            $content = '<p style="color: green;">Kategoria została zaktualizowana.</p>';
        } else {
            $content = '<p style="color: red;">Wystąpił błąd podczas edycji.</p>';
        }
        $action = 'kategorie_list';
    }
    if (isset($_POST['prod_add_submit'])) // Panel dodawania produktu - działanie
    {
        if($prodManager->DodajProdukt($link, $_POST)){
            $content = '<p style="color: green;">Produkt został dodany.</p>';
        }else {
            $content = '<p style="color: red;">Błąd dodawania produktu: ' . mysqli_error($link) . '</p>';
        }
        $action = 'produkty_list';
    }
    if (isset($_POST['prod_edit_submit'])) // Panel edycji produktu - działanie
    {
        if($prodManager->EdytujProdukt($link, $_POST['id_edycji'], $_POST)) {
            $content = '<p style="color: green;">Produkt został zaktualizowany.</p>';
        } else {
            $content = '<p style="color: red;">Błąd edycji produktu.</p>';
        }
        $action = 'produkty_list';
    }

    // Wyświetlenie komunikatów
    echo $content;
    
    // Wyświetlanie kategorii
    if ($action === 'kategorie_list') {
        echo '<h3 class="heading">Drzewo Kategorii</h3>';
        $catManager->pokazKategorie($link);
    }
    // Dodawanie kategorii
    elseif ($action === 'kategorie_add') {
        echo $catManager->formularzDodawania();
    }
    // Edycja kategorii
    elseif ($action === 'kategorie_edit' && $id > 0) {
        echo $catManager->formularzEdycji($link, $id);
    }
    // Usunięcie kategorii
    elseif ($action === 'kategorie_delete' && $id > 0) {
        $catManager->usunKategorie($link, $id);
        echo '<p style="color:green">Kategoria została usunięta.</p>';
        $catManager->pokazKategorie($link);
    }
    // Wyświetlenie produktów
    elseif ($action === 'produkty_list') {
        echo '<h3 class="heading">Lista Produktów</h3>';
        $prodManager->PokazProdukty($link);
    }
    // Dodawanie produktu
    elseif ($action === 'produkty_add') {
        echo $prodManager->FormularzDodawania();
    }
    // Edycja produktu
    elseif ($action === 'produkty_edit' && $id > 0) {
        echo $prodManager->FormularzEdycji($link, $id);
    }
    // Usuwanie produktu
    elseif ($action === 'produkty_delete' && $id > 0) {
        $prodManager->UsunProdukt($link, $id);
        echo '<p style="color:green">Produkt usunięty.</p>';
        $prodManager->PokazProdukty($link);
    }
    // Wyświetlenie okna edycji podstrony, gdy ID jest większe od 0
    elseif ($action === 'edit' && $id > 0) {
        echo EdytujPodstrone($link, $id);
    }
    // Wyświetlnie okna dodawania nowej podstrony
    elseif ($action === 'add') {
        echo DodajNowaPodstrone();
    }
    // Usuwanie podstrony
    elseif ($action === 'delete' && $id > 0) {
        echo UsunPodstrone($link, $id); 
        ListaPodstron($link);
    }
    // Domyślny widok
    else {
        echo '<h3 class="heading">Lista Podstron</h3>';
        ListaPodstron($link);
    }
    
} else {
    // Wyświetlenie komunikatu o błędzie i powrót do logowania
    echo $error_message; 
    echo FormularzLogowania();
}

// Klasa zarządzania kategoriami
class CategoryManager {
    
    // Wyświetlanie kategorii
    function pokazKategorie($link) {
        echo '<p><a href="admin.php?action=kategorie_add" style="padding: 10px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;"> Dodaj Kategorię </a></p>';
        
        $queryMain = "SELECT * FROM kategorie WHERE matka = 0 ORDER BY id ASC";
        $resultMain = mysqli_query($link, $queryMain);

        echo "<ul>";
        while ($rowMain = mysqli_fetch_array($resultMain)) {
            echo "<li><b>" . htmlspecialchars($rowMain['nazwa']) . "</b> (ID: " . $rowMain['id'] . ")";
            echo ' <a href="admin.php?action=kategorie_edit&id='.$rowMain['id'].'">[Edytuj]</a> ';
            echo ' <a href="admin.php?action=kategorie_delete&id='.$rowMain['id'].'" onclick="return confirm(\'Usunąć kategorię?\')">[Usuń]</a>';

            $matkaId = $rowMain['id'];
            $querySub = "SELECT * FROM kategorie WHERE matka = '$matkaId' ORDER BY id ASC";
            $resultSub = mysqli_query($link, $querySub);

            if (mysqli_num_rows($resultSub) > 0) {
                echo "<ul>";
                while ($rowSub = mysqli_fetch_array($resultSub)) {
                    echo "<li>" . htmlspecialchars($rowSub['nazwa']) . " (ID: " . $rowSub['id'] . ")";
                    echo ' <a href="admin.php?action=kategorie_edit&id='.$rowSub['id'].'">[E]</a> ';
                    echo ' <a href="admin.php?action=kategorie_delete&id='.$rowSub['id'].'" onclick="return confirm(\'Usunąć podkategorię?\')">[X]</a>';
                    echo "</li>";
                }
                echo "</ul>";
            }
            echo "</li>";
        }
        echo "</ul>";
    }

    // Dodawanie kategorii
    function formularzDodawania() {
        return '
        <h2 class="heading">Dodaj Kategorię</h2>
        <form method="post" action="admin.php?action=kategorie_list">
            Nazwa: <input type="text" name="nazwa" required> <br><br>
            Matka (ID rodzica, 0 = kategoria główna): <input type="number" name="matka" value="0"> <br><br>
            <input type="submit" name="cat_add_submit" value="Dodaj Kategorię">
        </form>';
    }

    // Edycja kategorii
    function formularzEdycji($link, $id) {
        $id = (int)$id;
        $query = "SELECT * FROM kategorie WHERE id = '$id' LIMIT 1";
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_array($result);
        
        if(!$row) return "Nie znaleziono kategorii.";

        return '
        <h2 class="heading">Edytuj Kategorię</h2>
        <form method="post" action="admin.php?action=kategorie_list">
            <input type="hidden" name="id_edycji" value="'.$id.'">
            Nazwa: <input type="text" name="nazwa" value="'.htmlspecialchars($row['nazwa']).'" required> <br><br>
            Matka: <input type="number" name="matka" value="'.$row['matka'].'"> <br><br>
            <input type="submit" name="cat_edit_submit" value="Zapisz Zmiany">
        </form>';
    }

    // Funkcja dodawania kategorii
    function dodajKategorie($link, $matka, $nazwa) {
        $matka = (int)$matka;
        $nazwa = mysqli_real_escape_string($link, $nazwa);
        $query = "INSERT INTO kategorie (matka, nazwa) VALUES ('$matka', '$nazwa')";
        return mysqli_query($link, $query);
    }

    // Funkcja edycji kategorii
    function edytujKategorie($link, $id, $matka, $nazwa) {
        $id = (int)$id;
        $matka = (int)$matka;
        $nazwa = mysqli_real_escape_string($link, $nazwa);
        $query = "UPDATE kategorie SET nazwa = '$nazwa', matka = '$matka' WHERE id = '$id' LIMIT 1";
        return mysqli_query($link, $query);
    }

    // Funkcja usuwania kategorii
    function usunKategorie($link, $id) {
        $id = (int)$id;
        $query = "DELETE FROM kategorie WHERE id = '$id' LIMIT 1";
        return mysqli_query($link, $query);
    }
}

// Klasa zarządzanie produktami
class ProductManager {

    // Wyświetlanie produktów
    function PokazProdukty($link) {
        echo '<p><a href="admin.php?action=produkty_add" style="padding: 10px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;"> Dodaj Produkt </a></p>';
        
        $query = "SELECT * FROM produkty ORDER BY id DESC";
        $result = mysqli_query($link, $query);

        if (!$result) {
            echo "Błąd zapytania: " . mysqli_error($link);
            return;
        }

        echo '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">';
        echo '<tr style="background-color: #f2f2f2;">
                <th>ID</th>
                <th>Zdjęcie</th> <th>Tytuł</th>
                <th>Cena Netto</th>
                <th>VAT</th>
                <th>Magazyn</th>
                <th>Status</th>
                <th>Dostępność</th>
                <th>Akcje</th>
              </tr>';

        while ($row = mysqli_fetch_array($result)) {
            $dostepnosc = "Dostępny";
            $color = "green";
            $dataWygasniecia = $row['data_wygasniecia'];
            $teraz = date('Y-m-d H:i:s');

            if ($row['status_dostepnosci'] != 1 || $row['ilosc_sztuk'] <= 0 || ($dataWygasniecia != NULL && $dataWygasniecia < $teraz)) {
                $dostepnosc = "Niedostępny";
                $color = "red";
            }

            $imgTag = '-';
            if (!empty($row['zdjecie'])) {
                $imgTag = '<img src="'.htmlspecialchars($row['zdjecie']).'" alt="Produkt" style="max-width: 100px; max-height: 100px; object-fit: cover;">';
            }

            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td style='text-align:center;'>" . $imgTag . "</td>";
            echo "<td>" . htmlspecialchars($row['tytul']) . "</td>";
            echo "<td>" . $row['cena_netto'] . " zł</td>";
            echo "<td>" . $row['podatek_vat'] . "%</td>";
            echo "<td>" . $row['ilosc_sztuk'] . " szt.</td>";
            echo "<td>" . ($row['status_dostepnosci'] == 1 ? 'Aktywny' : 'Nieaktywny') . "</td>";
            echo "<td style='color:$color; font-weight:bold;'>" . $dostepnosc . "</td>";
            echo "<td>
                    <a href='admin.php?action=produkty_edit&id=" . $row['id'] . "'>Edytuj</a> | 
                    <a href='admin.php?action=produkty_delete&id=" . $row['id'] . "' onclick='return confirm(\"Usunąć produkt?\")'>Usuń</a>
                  </td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    // Dodawanie produktów
    function FormularzDodawania() {
        return '
        <h2 class="heading">Dodaj Produkt</h2>
        <form method="post" action="admin.php?action=produkty_list">
            <table>
                <tr><td>Tytuł:</td><td><input type="text" name="tytul" required></td></tr>
                <tr><td>Opis:</td><td><textarea name="opis" rows="4"></textarea></td></tr>
                <tr><td>Data wygaśnięcia:</td><td><input type="datetime-local" name="data_wygasniecia"></td></tr>
                <tr><td>Cena netto:</td><td><input type="number" step="0.01" name="cena_netto" required></td></tr>
                <tr><td>Podatek VAT (%):</td><td><input type="number" step="0.01" name="podatek_vat" value="23"></td></tr>
                <tr><td>Ilość sztuk:</td><td><input type="number" name="ilosc_sztuk" required></td></tr>
                <tr><td>Status dostępności:</td><td>
                    <select name="status_dostepnosci">
                        <option value="1">Aktywny</option>
                        <option value="0">Nieaktywny</option>
                    </select>
                </td></tr>
                <tr><td>Kategoria (ID):</td><td><input type="number" name="kategoria"></td></tr>
                <tr><td>Gabaryt:</td><td><input type="text" name="gabaryt"></td></tr>
                <tr><td>Link do zdjęcia:</td><td><input type="text" name="zdjecie"></td></tr>
                <tr><td></td><td><input type="submit" name="prod_add_submit" value="Dodaj Produkt"></td></tr>
            </table>
        </form>';
    }

    // Funkcja dodawania produktów
    function DodajProdukt($link, $dane) {
        $tytul = mysqli_real_escape_string($link, $dane['tytul']);
        $opis = mysqli_real_escape_string($link, $dane['opis']);
        $cena = (float)$dane['cena_netto'];
        $vat = (float)$dane['podatek_vat'];
        $ilosc = (int)$dane['ilosc_sztuk'];
        $status = (int)$dane['status_dostepnosci'];
        $kategoria = (int)$dane['kategoria'];
        $gabaryt = mysqli_real_escape_string($link, $dane['gabaryt']);
        $zdjecie = mysqli_real_escape_string($link, $dane['zdjecie']);
        $data_wyg = !empty($dane['data_wygasniecia']) ? "'" . $dane['data_wygasniecia'] . "'" : "NULL";
        $query = "INSERT INTO produkty (tytul, opis, data_utworzenia, data_wygasniecia, cena_netto, podatek_vat, ilosc_sztuk, status_dostepnosci, kategoria, gabaryt, zdjecie) 
                  VALUES ('$tytul', '$opis', NOW(), $data_wyg, '$cena', '$vat', '$ilosc', '$status', '$kategoria', '$gabaryt', '$zdjecie')";
        
        return mysqli_query($link, $query);
    }

    // Edycja produktów
    function FormularzEdycji($link, $id) {
        $id = (int)$id;
        $query = "SELECT * FROM produkty WHERE id = '$id' LIMIT 1";
        $result = mysqli_query($link, $query);
        $row = mysqli_fetch_array($result);

        if(!$row) return "Nie znaleziono produktu.";

        $dataWyg = $row['data_wygasniecia'] ? date('Y-m-d\TH:i', strtotime($row['data_wygasniecia'])) : '';
        
        $imgPreview = '';
        if (!empty($row['zdjecie'])) {
            $imgPreview = '<div style="margin-bottom:10px;"><img src="'.htmlspecialchars($row['zdjecie']).'" style="max-width: 200px; border: 1px solid #ccc; padding: 5px;"></div>';
        }

        return '
        <h2 class="heading">Edytuj Produkt</h2>
        <form method="post" action="admin.php?action=produkty_list">
            <input type="hidden" name="id_edycji" value="'.$id.'">
            <table>
                <tr><td>Tytuł:</td><td><input type="text" name="tytul" value="'.htmlspecialchars($row['tytul']).'" required></td></tr>
                <tr><td>Opis:</td><td><textarea name="opis" rows="4">'.htmlspecialchars($row['opis']).'</textarea></td></tr>
                <tr><td>Data wygaśnięcia:</td><td><input type="datetime-local" name="data_wygasniecia" value="'.$dataWyg.'"></td></tr>
                <tr><td>Cena netto:</td><td><input type="number" step="0.01" name="cena_netto" value="'.$row['cena_netto'].'" required></td></tr>
                <tr><td>Podatek VAT (%):</td><td><input type="number" step="0.01" name="podatek_vat" value="'.$row['podatek_vat'].'"></td></tr>
                <tr><td>Ilość sztuk:</td><td><input type="number" name="ilosc_sztuk" value="'.$row['ilosc_sztuk'].'" required></td></tr>
                <tr><td>Status dostępności:</td><td>
                    <select name="status_dostepnosci">
                        <option value="1" '.($row['status_dostepnosci'] == 1 ? 'selected' : '').'>Aktywny</option>
                        <option value="0" '.($row['status_dostepnosci'] == 0 ? 'selected' : '').'>Nieaktywny</option>
                    </select>
                </td></tr>
                <tr><td>Kategoria (ID):</td><td><input type="number" name="kategoria" value="'.$row['kategoria'].'"></td></tr>
                <tr><td>Gabaryt:</td><td><input type="text" name="gabaryt" value="'.htmlspecialchars($row['gabaryt']).'"></td></tr>
                
                <tr>
                    <td>Zdjęcie (link):</td>
                    <td>
                        '.$imgPreview.' <input type="text" name="zdjecie" value="'.htmlspecialchars($row['zdjecie']).'">
                    </td>
                </tr>

                <tr><td></td><td><input type="submit" name="prod_edit_submit" value="Zapisz Zmiany"></td></tr>
            </table>
        </form>';
    }

    // Funkcja edycji produktów
    function EdytujProdukt($link, $id, $dane) {
        $id = (int)$id;
        $tytul = mysqli_real_escape_string($link, $dane['tytul']);
        $opis = mysqli_real_escape_string($link, $dane['opis']);
        $cena = (float)$dane['cena_netto'];
        $vat = (float)$dane['podatek_vat'];
        $ilosc = (int)$dane['ilosc_sztuk'];
        $status = (int)$dane['status_dostepnosci'];
        $kategoria = (int)$dane['kategoria'];
        $gabaryt = mysqli_real_escape_string($link, $dane['gabaryt']);
        $zdjecie = mysqli_real_escape_string($link, $dane['zdjecie']);
        $data_wyg = !empty($dane['data_wygasniecia']) ? "'" . $dane['data_wygasniecia'] . "'" : "NULL";
        $query = "UPDATE produkty SET 
            tytul = '$tytul', 
            opis = '$opis', 
            data_modyfikacji = NOW(), 
            data_wygasniecia = $data_wyg, 
            cena_netto = '$cena', 
            podatek_vat = '$vat', 
            ilosc_sztuk = '$ilosc', 
            status_dostepnosci = '$status', 
            kategoria = '$kategoria', 
            gabaryt = '$gabaryt', 
            zdjecie = '$zdjecie' 
            WHERE id = '$id' LIMIT 1";
            
        return mysqli_query($link, $query);
    }

    // Usuwanie produktów
    function UsunProdukt($link, $id) {
        $id = (int)$id;
        $query = "DELETE FROM produkty WHERE id = '$id' LIMIT 1";
        return mysqli_query($link, $query);
    }
}

// Wyświetlenie formularza logowania do panelu CMS
function FormularzLogowania() {
    $wynik = '
        <div class="logowanie">
            <h1 class="heading">Panel CMS: </h1>
            <div class="logowanie">
                <form method="post" name="LoginForm" enctype="multipart/form-data" action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'">
                    <table class="logowanie">
                        <tr><td class="log4_t">[email]</td><td><input type="text" name="login_email" class="logowanie" /></td></tr>
                        <tr><td class="log4_t">[hasło]</td><td><input type="password" name="login_pass" class="logowanie" /></td></tr>
                        <tr><td>&nbsp;</td><td><input type="submit" name="xl_submit" class="logowanie" value="zaloguj" /></td></tr>
                    </table>
                </form>
            </div>
        </div>
    ';
    return $wynik;
}

function ListaPodstron($link) {
    echo '<p><a href="admin.php?action=add" style="padding: 10px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;"> Dodaj </a></p>'; // Przycisk dodawania nowej podstrony
    $query = "SELECT id, page_title FROM page_list ORDER BY status DESC"; // Zapytanie do bazy danych w celu pobrania ID stron i ich tytułów
    $result = mysqli_query($link, $query); 

    echo "<ul>";
    while ($row = mysqli_fetch_array($result)) {
        echo "<li>";
        echo $row['id'] . ' ' . $row['page_title']; // Wyświetlenie ID i tytułów podstron
        echo ' <a href="admin.php?action=edit&id=' . $row['id'] . '">Edytuj</a>'; // Przekierowanie do edycji podstrony o podanym ID
        // Usunięcie podstrony - dodano wyświetlenie okna potwierdzenia usunięcia w celu uniknięia przypadkowego usunięcia podstrony
        echo ' <a href="admin.php?action=delete&id=' . $row['id'] . '" onclick="return confirm(\'Czy na pewno chcesz usunąć stronę o ID: ' . $row['id'] . ' i tytule: ' . htmlspecialchars($row['page_title']) . '?\')">Usuń</a>';
        echo "</li>";
    }
    echo "</ul>";
}

function EdytujPodstrone($link, $id)
{
    $id = (int)$id; // Pobranie ID postrony
    $query_select = "SELECT * FROM page_list WHERE id = $id LIMIT 1"; // Zapytanie do bazy danych 
    $result = mysqli_query($link, $query_select); // Wczytywanie rezultatu zapytania
    $data = mysqli_fetch_array($result); // Wczytywanie wierszy z rezultatu jako tablice
    if (!$data) {
        return '<p style="color: red;">Nie znaleziono strony o podanym ID.</p>'; // Komunikat o błędzie w przypadku nieznalezienia podstrony
    }
    $tytul = htmlspecialchars($data['page_title']); // Pobranie tytułu podstrony
    $tresc = htmlspecialchars($data['page_content']); // Pobranie zawartości podstrony
    $aktywny_checked = ($data['status'] == 1) ? 'checked' : ''; // Sprawdzenie czy strona jest aktywna
    $form = '
        <h2 class="heading">Edytuj: ' . $tytul . '</h2>
        <form method="post" action="admin.php?action=edit&id=' . $id . '">
            <input type="hidden" name="id_edycji" value="' . $id . '">
            <table>
                <tr>
                    <td>Tytuł:</td>
                    <td><input type="text" name="page_title" value="' . $tytul . '" required /></td>
                </tr>
                <tr>
                    <td>Treść:</td>
                    <td><textarea name="page_content" rows="10" cols="50" required>' . $tresc . '</textarea></td>
                </tr>
                <tr>
                    <td>Aktywna:</td>
                    <td><input type="checkbox" name="status" value="1" ' . $aktywny_checked . ' /></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" name="edit_submit" value="Zapisz" /></td>
                </tr>
            </table>
        </form>
    ';
    return $form;
}

function DodajNowaPodstrone() {
    $form = '
        <h2 class="heading">Dodaj nową</h2>
        <form method="post" action="admin.php?action=add">
            <table>
                <tr>
                    <td>Tytuł:</td>
                    <td><input type="text" name="page_title" value="" required /></td>
                </tr>
                <tr>
                    <td>Treść:</td>
                    <td><textarea name="page_content" rows="10" cols="50" required></textarea></td>
                </tr>
                <tr>
                    <td>Aktywna:</td>
                    <td><input type="checkbox" name="status" value="1" checked /></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" name="add_submit" value="Utwórz" /></td>
                </tr>
            </table>
        </form>
    ';
    return $form;
}

function UsunPodstrone($link, $id) {
    // Usuwanie podstrony o podanym ID z bazy danych 
    $id = (int)$id;

    $query_delete = "DELETE FROM page_list WHERE id = $id LIMIT 1";  // Użycie LIMIT w celu zabezpieczenia przed przypadkowym usunięciem więcej niż 1 podstrony
    
    if (mysqli_query($link, $query_delete)) {
        return '<p style="color: green;">Strona o ID: ' . $id . ' została usunięta.</p>'; // Wyświetlenie komunikatu o usunięciu podstrony
    } else {
        return '<p style="color: red;">Wystąpił błąd: ' . mysqli_error($link) . '</p>'; // Wyświetlenie komunikatu o błędzie
    }
}

?>