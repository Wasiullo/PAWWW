<?php
include ('cfg.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once 'phpmailer/src/Exception.php';
require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';

function PokazKontakt(){
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
        $odbiorca = 'wasiullo@wp.pl';
        return WyslijMailKontakt($odbiorca);
    }
    return $wynik;
}

function WyslijMailKontakt($odbiorca) {

    $nadawca = 'wasiullo@wp.pl'; 
    $haslo = ''; // Tutaj hasło do poczty

    if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
        echo '<p style="color: red;">[nie_wypelniles_pola]: Musisz wypełnić wszystkie pola przed wysłaniem wiadomości.</p>'; 
        return PokazKontakt(); 
    } else {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.wp.pl';
            $mail->SMTPAuth   = true;
            $mail->Username   = $nadawca;
            $mail->Password   = $haslo;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';


            $mail->setFrom($nadawca, 'Formularz Kontaktowy');
            $mail->addAddress($odbiorca);
            $mail->addReplyTo($_POST['email']); 

            $mail->isHTML(false);
            $mail->Subject = $_POST['temat'];
            $mail->Body    = $_POST['tresc'];
            
            $mail->SMTPOptions = array(
                'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
                )
            );
            $mail->send();
            return '<p style="color: green;">[wiadomosc_wyslana]: Twoja wiadomość została wysłana pomyślnie.</p>';
            
        } catch (Exception $e) {
            return '<p style="color: red;">Wystąpił błąd podczas wysyłania: ' . $mail->ErrorInfo . '</p>';
        }
    }
}

function PrzypomnijHaslo() {
    global $login, $pass;
    
    $nadawca = 'wasiullo@wp.pl'; 
    $haslo = ''; // Tutaj hasło do poczty

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

    if (isset($_POST['przypomnij_submit'])) {
        $email_admin = $_POST['email_admin'];

        if ($email_admin === $login) {
            $odbiorca = $email_admin;

            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.wp.pl';
                $mail->SMTPAuth   = true;
                $mail->Username   = $nadawca;
                $mail->Password   = $haslo;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom($nadawca, 'Przypominanie hasła');
                $mail->addAddress($odbiorca);

                $mail->isHTML(false); 
                $mail->Subject = 'Przypomnienie hasła do Panelu Administracyjnego CMS';
                $mail->Body    = "Twoje hasło do panelu administratora to: " . $pass . "\n\nZalecana jest zmiana hasła po zalogowaniu.";

                  $mail->SMTPOptions = array(
                    'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                    )
                );
                $mail->send();
                return '<p style="color: green;">Wysłano hasło na adres e-mail: ' . htmlspecialchars($odbiorca) . '.</p>';
                
            } catch (Exception $e) {
                return '<p style="color: red;">Wystąpił błąd podczas wysyłania: ' . $mail->ErrorInfo . '</p>';
            }
        } else {
            return '<p style="color: red;">Podany adres e-mail nie jest powiązany z kontem administratora.</p>' . $form_haslo;
        }
    }

    return $form_haslo;
}
?>