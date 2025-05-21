<?php
$password = 'Marstop';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password hash for 'Marstop': " . $hash;
?> 