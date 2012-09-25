<?php
// send away people who came here on an odd way
if(empty($_REQUEST)) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// we use PEAR mail (http://pear.php.net/package/Mail/redirected)   
require_once "Mail.php";
require_once "includes.php";

// formulate a nice email to the support
$format = 'Hallo,

hiermit beantrage ich den Zugang zu einen gemeinsam genutzen Dienst:

* Antragssteller: %s
* Mobilfunknummer: %s
* Dienst: %s

----

Begründung:

%s

----

Ich akzeptiere folgende Nutzungsbedingungen zum Umgang mit Passworten:

Nutzungsbedingungen zum Umgang mit Passworten
=============================================

1. Du wirst sorgsam mit dem Passwort umgehen. Du wirst es nicht zusammen mit dem Nutzernamen aufschreiben oder speichern. Falls du Zweifel hast, ob ein Dritter Kenntnis von dem Passwort erlangt hat, meldest du dich umgehend bei support@piraten-mv.de.

2. Du wirst das Passwort an niemanden weitergeben. Falls du der Meinung bist, dass es eine gute Idee ist, dass auch eine andere Person Zugriff zu dem Dienst erhält, dann schreib bitte eine E-Mail an support@piraten-mv.de.

3. Du wirst das Passwort nicht ändern. Es handelt sich um einen Zugang, den du dir mit anderen Personen teilst. Diese Personen würden den Zugriff zum Dienst nach einer Passwortänderung verlieren. Falls du der Meinung bist, dass es eine gute Idee ist, dass das Passwort geändert werden sollte, dann schreib bitte eine E-Mail an support@piraten-mv.de.

4. Du wirst uns per E-Mail an support@piraten-mv.de Bescheid geben, wenn du den Zugang zum Dienst nicht mehr nutzt. Du wirst dann auch das Passwort den von dir genutzten Anwendungen entfernen, die das Passwort für den Zugang zu dem Dienst speichern.

5. Dein Name, deine E-Mail-Adresse und deine Mobilfunknummer werden zusammen mit dem heutigen Datum und dem Namen des Dienstes gespeichert, damit wir nachvollziehen können, wer wann Zugriff erhalten hat.

Beste Grüße,
%s

-- 
Diese E-Mail wurde automatisch über das Formular %s generiert. Rückfragen zu diesem Formular beantworten wir gerne unter support@piraten-mv.de.
';

$body = sprintf($format, $_REQUEST['name'], $_REQUEST['mobile'], $_REQUEST['service'], $_REQUEST['description'], $_REQUEST['name'], $_SERVER['HTTP_REFERER']);

// create the mail
$subject = "Antrag auf Zugang zu " . $_REQUEST['service'] . "";
$mail_from = "Piraten MV Support <support@piraten-mv.de>";
$mail_to = "Piraten MV Support <support@piraten-mv.de>";

// connect to the mail server   
$smtp = Mail::factory('smtp',
                      array (
                             'host' => $host,
                             'auth' => true,
                             'username' => $username,
                             'password' => $password
                             ));

// send the mail to the support
// FIXME: we currently send the mail from the support@piraten-mv.de account and use a Reply-To header to route answers to the one who filled the form.  
$headers = array (
                  'From' => $mail_from,
                  'To' => $mail_to,
                  'Subject' => $subject,
                  'CC' => $_REQUEST['name'] . " <" . $_REQUEST['email'] . ">",
                  'Reply-To' => $_REQUEST['name'] . "<" . $_REQUEST['email'] . ">",
                  'Content-Type' => "text/plain; charset=\"UTF-8\"",
                  'Content-Transfer-Encoding' => "8bit"
                  );
$mail = $smtp->send($mail_to . ", " . $_REQUEST['name'] . " <" . $_REQUEST['email'] . ">", $headers, $body);

// TODO: make nice error handling
if (PEAR::isError($mail)) {
    echo "Fehler beim Versender der E-Mail : ". $mail->getMessage();
}

// TODO: make nice thank you page
print '
<html>
<head>
<title>Danke!</title>
</head>
<body>
<h1>Danke!</h1>
</body>
</html>
';
?>

