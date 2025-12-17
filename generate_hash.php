<?php
$plain_key = 'my_secret_api_key_123';
$hashed_key = password_hash($plain_key, PASSWORD_DEFAULT);

echo "Хеш для вставки в базу данных:\n\n";
echo "<pre>" . $hashed_key . "</pre>\n\n";
echo "Пароль для использования в API: <strong>my_secret_api_key_123</strong>";
?>