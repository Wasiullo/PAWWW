<?php
// Do wysyłania e-maili wykorzystano PHPMailer; pełna dokumentacja: https://github.com/PHPMailer/PHPMailer
include ('cfg.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once 'phpmailer/src/Exception.php';
require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';

function PokazKontakt(){
    // Wyświetlanie okienka do wysłania e-maila kontaktowego
    $wynik = '
        <h2 class="heading">Formularz Kontaktowy</h2>
        <form method="post" action="index.php?id=contact">
            <div class="form-group">
                <label for="email">Twój adres E-mail:</label>
                <input type="email" id="email" name="email" required class="form-control">
            </div>
            <div class="form-group">
                <label for="temat">Temat:</label>
                <input type="text" id="temat" name="temat" required class="form-control">
            </div>
            <div class="form-group">
                <label for="tresc">Treść Wiadomości:</label>
                <textarea id="tresc" name="tresc" rows="8" required class="form-control"></textarea>
            </div>
            <button type="submit" name="kontakt_submit" class="form-submit-btn">Wyślij Wiadomość</button>
        </form>
    ';
    if (isset($_POST['kontakt_submit'])) {
        $odbiorca = 'wasiullo@wp.pl'; // Poczta, na którą ma zostać wysłana wiadomość; można użyć tej samej, co w nadawcy
        return WyslijMailKontakt($odbiorca);
    }
    return $wynik;
}

function WyslijMailKontakt($odbiorca) {

    $nadawca = 'wasiullo@wp.pl'; // Poczta, z której wiadmość ma zostać wysłana
    $haslo = ''; // Tutaj hasło do poczty

    // Zabezpieczenie przed wysłaniem wiadomości bez podania tematu, treści lub adresu e-mail wysyłającego
    if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
        echo '<p style="color: red;">[nie_wypelniles_pola]: Musisz wypełnić wszystkie pola przed wysłaniem wiadomości.</p>'; 
        return PokazKontakt(); 
    } else {
        $mail = new PHPMailer(true); // Wywołanie PHPMailera

        try {
            // Ustawienia poczty nadawczej: adres hosta poczty, autoryzacja, login, hasło, szyfrowanie, port poczty, zestaw znaków
            $mail->isSMTP();
            $mail->Host       = 'smtp.wp.pl';
            $mail->SMTPAuth   = true;
            $mail->Username   = $nadawca;
            $mail->Password   = $haslo;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';


            $mail->setFrom($nadawca, 'Formularz Kontaktowy'); // Jako nadawca będzie wyświetlał się podany tekst (np. Formularz Kontaktowy)
            $mail->addAddress($odbiorca); // Ustawienie do kogo ma trafić wiadomość
            $mail->addReplyTo($_POST['email']);  // Przy wysyłaniu odpowiedzi na wiadomość wysłanie wiadomości do faktycznego nadawcy, nie trzeba szukać kto jest nadawcą - zostanie to automatycznie uzupełnione

            $mail->isHTML(false);
            $mail->Subject = $_POST['temat']; // Zawarcie w temacie e-maila tematu wspisanego na stronie
            $mail->Body    = $_POST['tresc']; // Zawarcie w treści e-maila treść wpisaną na stronie
            
            // Ustawienia SMTP
            $mail->SMTPOptions = array(
                'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
                )
            );
            // Jeżeli wysyłanie zwraca błąd odkomentować linię poniżej - wyświetlenie logów ułatwiających znalezienie błędu
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->send(); // Wysłanie wiadomości
            return '<p style="color: green;">[wiadomosc_wyslana]: Twoja wiadomość została wysłana pomyślnie.</p>';
            // W przypadku poczty na WP należy uruchomić IMAP w ustawieniach konta, dodatkowo należy wysłać jakąkolwiek wiadomość z konta - bez tego IMAP nie będzie działać, a logi wyświetlą niepowodzenie logowania lub brak aktywacji SMTP
            
        } catch (Exception $e) {
            // Wyświetlanie błędu podczas wysyłania (związane z błędem konfiguracyjnym)
            return '<p style="color: red;">Wystąpił błąd podczas wysyłania: ' . $mail->ErrorInfo . '</p>';
        }
    }
}

function PrzypomnijHaslo() {
    global $login, $pass; // Pobranie loginu i hasła do panelu CMS z pliku cfg
    
    $nadawca = 'wasiullo@wp.pl'; // Login do poczty nadawcy 
    $haslo = ''; // Hasło do poczty nadawcy

    // Podstrona przypomnienia hasła
    $form_haslo = '
        <h2 class="heading">Przypomnij Hasło</h2>
        <form method="post" action="index.php?id=forgot_pass">
            <div class="form-group">
                <label for="email_admin">Podaj adres e-mail konta administratora:</label>
                <input type="email" id="email_admin" name="email_admin" required class="form-control">
            </div>
            <button type="submit" name="przypomnij_submit" class="form-submit-btn">Wyślij Hasło</button>
        </form>
    ';
    // Wykonanie funkcji po kliknięciu przyicsku przypomnienia
    if (isset($_POST['przypomnij_submit'])) {
        $email_admin = $_POST['email_admin']; // Pobranie wpisanego adresu e-mail

        if ($email_admin === $login) { //Sprawdzenie, czy podany e-mail jest taki sam, co adres e-mail do logowania do panelu CMS
            $odbiorca = $email_admin; // Przypisanie e-maila admina jako odbiorcy; jeżeli używamy jako adresu do logowania np. admin@root.com (maila, do którego dostępu nie mamy) należy podać adres e-mail, na który ma przyjść hasło (np. adres nadawcy)

            // Wywołanie PHPMailera
            $mail = new PHPMailer(true);

            try {
                // Ustawienia poczty nadawczej: adres hosta poczty, autoryzacja, login, hasło, szyfrowanie, port poczty, zestaw znaków
                $mail->isSMTP();
                $mail->Host       = 'smtp.wp.pl';
                $mail->SMTPAuth   = true;
                $mail->Username   = $nadawca;
                $mail->Password   = $haslo;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom($nadawca, 'Przypominanie hasła'); // Jako nadawca będzie wyświetlał się podany tekst (np. Przypominanie hasła)
                $mail->addAddress($odbiorca); // Ustawienie do kogo ma trafić wiadomość

                $mail->isHTML(false); 
                $mail->Subject = 'Przypomnienie hasła do Panelu Administracyjnego CMS'; // Temat wiadomości
                $mail->Body    = "Twoje hasło do panelu administratora to: " . $pass . "\n\nZalecana jest zmiana hasła po zalogowaniu."; // Treść wiadomości z hasłem do panelu CMS
                
                // Ustawienia SMTP
                $mail->SMTPOptions = array(
                    'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                    )
                );
                // Jeżeli wysyłanie zwraca błąd odkomentować linię poniżej - wyświetlenie logów ułatwiających znalezienie błędu
                // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                $mail->send(); // Wysłanie wiadomości
                return '<p style="color: green;">Wysłano hasło na adres e-mail: ' . htmlspecialchars($odbiorca) . '.</p>'; // Wyświetlenie komunikatu z potwierdzeniem wysłania hasła na adres e-mail odbiorcy
                // W przypadku poczty na WP należy uruchomić IMAP w ustawieniach konta, dodatkowo należy wysłać jakąkolwiek wiadomość z konta - bez tego IMAP nie będzie działać, a logi wyświetlą niepowodzenie logowania lub brak aktywacji SMTP
                
            } catch (Exception $e) {
                // Wyświetlanie błędu podczas wysyłania (związane z błędem konfiguracyjnym)
                return '<p style="color: red;">Wystąpił błąd podczas wysyłania: ' . $mail->ErrorInfo . '</p>';
            }
        } else {
            // Komunikat wyświetlany w przypadku podania niepoprawnego adresu e-mail do konta administratora
            return '<p style="color: red;">Podany adres e-mail nie jest powiązany z kontem administratora.</p>' . $form_haslo;
        }
    }

    return $form_haslo;
}
?>