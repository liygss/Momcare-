<?php
session_start();
session_destroy();
header("Location: halaman_login.php");
exit();
