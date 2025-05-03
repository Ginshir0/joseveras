<?php
$password = '';  // Replace with your desired password
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: $password\n";
echo "Generated Hash: $hash\n";
echo "\nSQL Statement to update admin:\n";
echo "UPDATE admins SET password_hash = '$hash' WHERE username = 'jveras';\n";
