<?php
require_once '../includes/functions.php';
redirect_if_not_logged_in();
redirect_if_not_admin();
require_once '../includes/db.php';
require_once 'reservation_rows_partial.php';

header('Content-Type: text/html; charset=UTF-8');
render_admin_reservation_rows($conn);
?>
