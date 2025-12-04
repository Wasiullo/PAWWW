<?php
session_start(); // Rozpoczęcie sesji
include '../cfg.php'; // Wczytanie pliku cfg

function FormularzLogowania() {
    // Wyświetlenie formularza logowania do panelu CMS
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
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $content = '';

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
    if ($action === 'edit' && $id > 0) // Wyświetlenie okna edycji podstrony, gdy ID jest większe od 0
    {
        echo $content;
        echo EdytujPodstrone($link, $id);
     
    }elseif ($action === 'add') {
        // Wyświetlnie okna dodawania nowej podstrony
        echo DodajNowaPodstrone();
    }elseif ($action === 'delete' && $id > 0) {
        // Usuwanie podstrony
        echo UsunPodstrone($link, $id); 
        ListaPodstron($link);
    }else {
        // Wyświetlenie strony głównej panelu CMS
        echo 'Witaj w Panelu CMS!';
        echo $content;
        ListaPodstron($link);
    }
    
} else {
    // Wyświetlenie komunikatu o błędzie i powrót do logowania
    echo $error_message; 
    echo FormularzLogowania();
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