<?php
// send away people who came here on an odd way
if(empty($_REQUEST)) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}
    
// we use PEAR mail (http://pear.php.net/package/Mail/redirected)
require_once "Mail.php";
require_once "includes.php";

// formulate a nice email to the board
$format = 'Hallo,

hiermit stelle ich folgenden Antrag an den Vorstand:

* Antragssteller: %s
* Antragsberechtigung: %s
* Kommentar: %s
* Titel: %s
* Verantwortlich: %s

----

h1. Antragstext

%s

h1. Begründung

%s

----

Beste Grüße,
%s

-- 
Diese E-Mail wurde automatisch über das Formular %s generiert. Rückfragen zu diesem Formular beantworten wir gerne unter support@piraten-mv.de.
';

$body = sprintf($format, $_REQUEST['name'], $_REQUEST['reason'], $_REQUEST['comment'], $_REQUEST['title'], $_REQUEST['delegation'], $_REQUEST['text'], $_REQUEST['description'], $_REQUEST['name'], $_SERVER['HTTP_REFERER']);

// create the mail
$subject = "Antrag: \"" . $_REQUEST['title'] . "\"";
$mail_from = "Piraten MV Support <support@piraten-mv.de>";
$mail_to = "Piraten MV Vorstand <vorstand@piraten-mv.de>";
    
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

