<?php
session_start();
session_destroy();
header('Location: controller.php?action=login&logout=1');
exit;
?>