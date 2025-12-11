<?php
session_start();
session_destroy();
header('Location: fiche.html');
exit;
?>