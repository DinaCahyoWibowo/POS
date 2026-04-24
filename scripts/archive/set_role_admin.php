<?php
$host = '127.0.0.1';
$port = 3306;
$user = 'root';
$pass = '';
$dbName = 'udkasemi';
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbName;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->prepare("SELECT id, role FROM users WHERE username = :u OR email = :e LIMIT 1");
    $stmt->execute([':u' => 'admin', ':e' => 'admin@example.com']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        echo "No user with username 'admin' or email 'admin@example.com' found.\n";
        exit(0);
    }
    echo "Found user id={$row['id']} role={$row['role']}\n";
    if ($row['role'] !== 'admin') {
        $update = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = :id");
        $update->execute([':id' => $row['id']]);
        echo "Updated user id {$row['id']} role to admin.\n";
    } else {
        echo "Role already set to admin.\n";
    }
} catch (PDOException $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
    exit(1);
}
