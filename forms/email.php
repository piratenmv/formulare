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

hiermit beantrage ich die E-Mail-Adresse %s@piraten-mv.de:

* Antragssteller: %s
* bisherige E-Mail-Adresse: %s
* Mitgliedschaft: %s

----

Begründung:

%s

----

Ich akzeptiere folgende Benutzungsrichtlinien von offiziellen E-Mail-Adressen:

Benutzungsrichtlinien von offiziellen E-Mail-Adressen
=====================================================

1. Die Piratenpartei Mecklenburg Vorpommern stellt den Vorstandsmitglieder und den Mitgliedern der Organe des Landesverbandes oder der Untergliederungen kostenlos eine persönliche E-Mail-Adresse zur Verfügung, die für die offizielle Kommunikation im Rahmen der Tätigkeiten innerhalb der Piratenpartei Deutschland benutzt wird. Die E-Mail-Adresse setzt sich aus dem persönlichen Vor- und Nachnamen (vorname.nachname@piraten-mv.de) zusammen.

2. Generell besteht kein Anspruch auf die ständige Verfügbarkeit von E-Mail über den Dienst des Landesverbandes. Sollte der Dienst aus Gründen, welche die Administration des Landesverbandes nicht zu vertreten haben oder aus wartungstechnischen Gründen nicht oder nicht mit dem vollen Leistungsumfang verfügbar sein, so haftet der Landesverband Mecklenburg-Vorpommern nicht für Schäden oder Folgeschäden, die einem Benutzer daraus entstehen können. Im Falle eines Systemausfalls besteht kein Anspruch auf Rücksicherung. Der Nutzer stellt dens Landesverband Mecklenburg-Vorpommern von jeglicher Haftung für die von ihm übermittelten Inhalte frei.

3. Du kannst jederzeit die Nutzung der persönlichen E-Mail-Adresse durch eine Mitteilung per E-Mail an support@piraten-mv.de beenden. Der Vorstand der Piratenpartei Mecklenburg-Vorpommern ist berechtigt, die Nutzung der Adresse aus wichtigem Grund zu sperren. Wichtige Gründe für das Sperren der Zugangsberechtigung sind insbesondere ein Verstoß gegen die Ziffer 4 dieser Benutzungsrichtlinien.

4. Du verpflichtest dich, die E-Mail-Adresse ausschließlich für Kommunikation zu nutzen, welche im Einklang mit den Grundsätzen oder der Satzung der Piratenpartei Deutschlands und des Landesverbandes Mecklenburg-Vorpommern steht. Die E-Mail-Adresse ist ausschließlich für den Einsatz im Rahmen der Tätigkeit für und im Sinne der Piratenpartei vorgesehen. Jegliche Nutzung für rein private Zwecke ist nicht gestattet. Bekanntgewordene Verstöße werden dem Landesvorstand gemeldet. Die Administration hat im Sinne des Vorstandes des Landesverbandes zu handeln.

5. Die Administration unterliegt den einschlägigen Datenschutzbestimmungen und hat sämtliche, im Zusammenhang mit ihrer Tätigkeit bekannt gewordenen, persönlichen Daten vertraulich zu behandeln. Dies gilt auch über den Zeitraum der Tätigkeit für die Administration des Landesverbandes Mecklenburg-Vorpommern der Piratenpartei Deutschland hinaus. Die Administratoren wurden darüber in Kenntnis gesetzt, was mit der Unterzeichnung einer Geheimhaltungsvereinbarung zu dokumentieren ist.

Beste Grüße,
%s

-- 
Diese E-Mail wurde automatisch über das Formular %s generiert. Rückfragen zu diesem Formular beantworten wir gerne unter support@piraten-mv.de.
';

$body = sprintf($format, $_REQUEST['wished_email'], $_REQUEST['name'], $_REQUEST['email'], $_REQUEST['reason'], $_REQUEST['description'], $_REQUEST['name'], $_SERVER['HTTP_REFERER']);

// create the mail
$subject = "Antrag zur E-Mail-Adresse " . $_REQUEST['wished_email'] . "@piraten-mv.de";
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
