<?php
session_start();
require_once 'shop.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id_prod = isset($_POST['id_prod']) ? (int)$_POST['id_prod'] : 0;

    // Dodawanie produktu
    if ($action === 'add') {
        $nazwa = $_POST['nazwa'];
        $cena = (float)$_POST['cena_netto'];
        $vat = (float)$_POST['vat'];
        
        addToCart($id_prod, $nazwa, $cena, $vat);
        
        header("Location: index.php?id=koszyk");
        exit();
    } 
    
    // Usuwanie produktu
    elseif ($action === 'remove') {
        removeFromCart($id_prod);
        header("Location: index.php?id=koszyk");
        exit();
    } 
    
    // Aktualizacja ilości
    elseif ($action === 'update') {
        $ilosc = (int)$_POST['ilosc'];
        updateQuantity($id_prod, $ilosc);
        header("Location: index.php?id=koszyk");
        exit();
    }
}

// Jeśli ktoś wejdzie bezpośrednio bez POST, odeślij na stronę główną
header("Location: index.php");
exit();
?>