<?php
// sklep.php - Logika koszyka zakupowego

// Dodawania produktu do koszyka
function addToCart($id_prod, $nazwa, $cena_netto, $vat, $ile_sztuk = 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Sprawdzenie czy produkt jest w koszyku
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $id_prod) {
            $_SESSION['cart'][$key]['ilosc'] += $ile_sztuk;
            return;
        }
    }

    // Dodawanie nowego produktu
    $_SESSION['cart'][] = [
        'id' => $id_prod,
        'nazwa' => $nazwa,
        'cena_netto' => (float)$cena_netto,
        'vat' => (float)$vat,
        'ilosc' => (int)$ile_sztuk,
        'data' => time()
    ];
}

// Usuwanie produktu z koszyka
function removeFromCart($id_prod) {
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $id_prod) {
            unset($_SESSION['cart'][$key]);
            // Przeindeksowanie tablicy, aby usunąć "dziury" w kluczach
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            break;
        }
    }
}

// Aktualizacja ilości produktów
function updateQuantity($id_prod, $nowa_ilosc) {
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $id_prod) {
            if ($nowa_ilosc <= 0) {
                removeFromCart($id_prod);
            } else {
                $_SESSION['cart'][$key]['ilosc'] = (int)$nowa_ilosc;
            }
            break;
        }
    }
}

// Wyświetlanie koszyka
function showCart() {
    if (empty($_SESSION['cart'])) {
        return '<h3 class="heading">Twój koszyk jest pusty</h3>';
    }

    $html = '<h2 class="heading">Twój Koszyk</h2>';
    $html .= '<table class="cart-table" border="1" cellpadding="5" style="width:100%; border-collapse:collapse;">';
    $html .= '<tr style="background:#e6f0ff"><th>Produkt</th><th>Cena Brutto</th><th>Ilość</th><th>Wartość</th><th>Akcja</th></tr>';

    $suma_calkowita = 0;

    foreach ($_SESSION['cart'] as $item) {
        $cena_brutto = $item['cena_netto'] * (1 + ($item['vat'] / 100)); // Zakładając, że VAT w bazie jest np. 23
        if ($item['vat'] < 1) $cena_brutto = $item['cena_netto'] * (1 + $item['vat']); // Jeśli VAT jest np. 0.23

        $wartosc = $cena_brutto * $item['ilosc'];
        $suma_calkowita += $wartosc;

        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($item['nazwa']) . '</td>';
        $html .= '<td>' . number_format($cena_brutto, 2) . ' zł</td>';
        
        // Zmiana ilości produktów w koszyku
        $html .= '<td>
            <form method="post" action="sklep.php" style="display:inline;"> <input type="hidden" name="action" value="update">
                <input type="hidden" name="id_prod" value="' . $item['id'] . '">
                <input type="number" name="ilosc" value="' . $item['ilosc'] . '" min="1" style="width: 50px;">
                <button type="submit" class="btn-small">Zmień</button>
            </form>
          </td>';
                  
        $html .= '<td>' . number_format($wartosc, 2) . ' zł</td>';
        
        // Usuwanie z koszyka
        $html .= '<td>
            <form method="post" action="sklep.php" style="display:inline;"> <input type="hidden" name="action" value="remove">
                <input type="hidden" name="id_prod" value="' . $item['id'] . '">
                <button type="submit" style="color:red; cursor:pointer;">Usuń</button>
            </form>
          </td>';
        $html .= '</tr>';
    }
    // Podsumowanie ceny produktów
    $html .= '<tr><td colspan="3" align="right"><strong>Razem do zapłaty:</strong></td>';
    $html .= '<td colspan="2"><strong>' . number_format($suma_calkowita, 2) . ' zł</strong></td></tr>';
    $html .= '</table>';
    
    // Przycisk powrotu do sklepu
    $html .= '<p style="margin-top:20px; text-align:center;"><a href="index.php?id=sklep" class="form-submit-btn" style="text-decoration:none;">Wróć do zakupów</a></p>';

    return $html;
}

// Widok sklepu
function PokazProduktySklep($link) {
    $query = "SELECT * FROM produkty WHERE status_dostepnosci = 1 AND ilosc_sztuk > 0";
    $result = mysqli_query($link, $query);
    
    $html = '<h2 class="heading">Nasze Produkty</h2>';
    $html .= '<div class="galeria">';
    
    while ($row = mysqli_fetch_array($result)) {
        $cena_brutto = $row['cena_netto'] * (1 + ($row['podatek_vat'] / 100));
        
        $img = '';
        if(!empty($row['zdjecie'])) {
            $sciezka = str_replace('../', '', $row['zdjecie']);
            $img = '<img src="'.htmlspecialchars($sciezka).'" style="max-height:150px; width:auto; display:block; margin:0 auto;">';
        }

        $html .= '<div style="border:1px solid #ccc; padding:15px; width:250px; text-align:center; border-radius:8px; background:#fff;">';
        $html .= $img;
        $html .= '<h3>' . htmlspecialchars($row['tytul']) . '</h3>';
        $html .= '<p><strong>Cena: ' . number_format($cena_brutto, 2) . ' zł</strong></p>';
        
        // Dodawanie do koszyka
        $html .= '<form method="post" action="sklep.php">';
        $html .= '<input type="hidden" name="action" value="add">';
        $html .= '<input type="hidden" name="id_prod" value="'.$row['id'].'">';
        $html .= '<input type="hidden" name="nazwa" value="'.htmlspecialchars($row['tytul']).'">';
        $html .= '<input type="hidden" name="cena_netto" value="'.$row['cena_netto'].'">';
        $html .= '<input type="hidden" name="vat" value="'.$row['podatek_vat'].'">';
        $html .= '<button type="submit" class="form-submit-btn" style="margin-top:10px;">Dodaj do koszyka</button>';
        $html .= '</form>';
        
        $html .= '</div>';
    }
    
    $html .= '</div>';
    return $html;
}
?>