<?php
// remove die() and change first value to create its hash
die();
echo password_hash('password123', PASSWORD_DEFAULT);
?>