<?php
// send away people who came here on an odd way
if(empty($_REQUEST)) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// we use PEAR mail (http://pear.php.net/package/Mail/redirected)
require_once "Mail.php";
require_once "includes.php";

// collect the problems from the request
$problem = "";
if (array_key_exists("nocode", $_REQUEST)) {
    $problem .= "- Ich habe keinen Einladungscode erhalten.\n\n";
}

if (array_key_exists("wrongcode", $_REQUEST)) {
    $problem .= "- Mein Einladungscode funktioniert nicht.\n\n";
}

if (array_key_exists("nologin", $_REQUEST)) {
    $problem .= "- Ich habe meinen Anmeldenamen vergessen.\n\n";
}

if (array_key_exists("nopass", $_REQUEST)) {
    $problem .= "- Ich habe meine Passwort vergessen.\n\n";
}

if (array_key_exists("passreset", $_REQUEST)) {
    $problem .= "- Ich habe mein Passwort zurücksetzen lassen, jedoch noch keine E-Mail bekommen.\n\n";
}

if (array_key_exists("wrongpass", $_REQUEST)) {
    $problem .= "- Mein Passwort funktioniert nicht mehr.\n\n";
}

if ($_REQUEST['description'] != "") {
    $problem .= "- Weitere Probleme/Problembeschreibung:\n\n" . $_REQUEST['description'] . "\n\n";
}

// formulate a nice email to the support
$format = 'Hallo lieber Support,

ich bin %s, Mitglied im Landesverband Mecklenburg-Vorpommern und habe Probleme mit dem Zugang zum %s.

Meine Probleme
==============

%s
Über mich
=========

- Name: %s
- Landesverband: Mecklenburg-Vorpommern
- E-Mail-Adresse: %s
- Mitgliesnummer bzw. Anschrift: %s

Ich bestätige, dass:

- ich Mitglied im Landesverband Mecklenburg-Vorpommern bin,
- meine Mitglieschaft vom Generalsekretär bestätigt wurde,
- ich meinen Mitgliedsbeitrag bezahlt habe und keine Beitragsrückstände habe und
- ich auf eventuelle Probleme beim Empfang von E-Mails (Spamfilter, Quotas) aufmerksam gemacht wurde.

Ich hoffe, dass ihr mir helfen könnt!

Beste Grüße,
%s

-- 
Diese E-Mail wurde automatisch über das Formular %s generiert. Rückfragen zu diesem Formular beantworten wir gerne unter support@piraten-mv.de.
';

$body = sprintf($format, $_REQUEST['name'], $_REQUEST['instance'], $problem, $_REQUEST['name'], $_REQUEST['email'], $_REQUEST['number'], $_REQUEST['name'], $_SERVER['HTTP_REFERER']);

// create the mail
$subject = "Probleme beim Zugang zum " . $_REQUEST['instance'];
$mail_from = "Piraten MV Support <support@piraten-mv.de>";

// choose the support team from the chosen LQFB instance
$mail_to = "";
switch($_REQUEST['instance']) {
     case "Liquid Feedback des Bundes":
        $mail_to = "Liquid Feedback Support <support@lqfb.piratenpartei.de>";
        break;
    case "Liquid Feedback des Landes Mecklenburg-Vorpommen":
        $mail_to = "Liquid Feedback Support <support@lqpp.de>";
        break;
    default:
        die($_REQUEST['instance']);
        die("Problem. Bitte support@piraten-mv.de melden.");
}

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

