<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

if (!ValidatePOSTChars("ucip")) {
    echo "FAILED";
    return;
}

$user = seekPOST('u');
$cookie = seekPOST('c');
$achievementID = seekPOST('i');
$problemType = seekPOST('p');

if (isset($_POST['note'])) {
    $appendNote = $_POST['note']['description'];

    if (!empty(trim($_POST['note']['checksum']))) {
        $appendNote .= "<br>MD5: " . $_POST['note']['checksum'];
    }

    if (!empty(trim($_POST['note']['emulator']))) {
        $appendNote .= "<br>Emulator: " . $_POST['note']['emulator'];

        if ($_POST['note']['emulator'] == "RetroArch" || $_POST['note']['emulator'] == "RALibRetro") {
            $appendNote .= " (" . $_POST['note']['core'] . ")";
        }
    }

    $note = $appendNote;
}

if (validateUser_cookie($user, $cookie, 0) == true) {
    $success = submitNewTickets($user, $achievementID, $problemType, $note, $msgOut);
    if ($msgOut == "FAILED!") {
        header("Location: " . getenv('APP_URL') . "/Achievement/$achievementID?e=issue_failed");
    } else {
        header("Location: " . getenv('APP_URL') . "/Achievement/$achievementID?e=issue_submitted");
    }

    echo $msgOut;
} else {
    echo "FAILED: Cannot validate user! Try logging out and back in, or confirming your email.";
}
