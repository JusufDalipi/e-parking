<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vendosni zonën kohore të saktë (p.sh. Europe/Tirane ose Europe/Prishtine)
date_default_timezone_set('Europe/Tirane');

// Konfiguro kredencialet e sakta të databazës suaj nga paneli i InfinityFree
$host = 'sql202.infinityfree.com';          // Hostname i përditësuar
$dbUser = 'if0_42031629';                   // MySQL User Name i ri 
$dbPass = 'wnKRyYh5za';  // Fjalëkalimi juaj i vPanel
$dbName = 'if0_42031629_parking_management';          // Vendosni emrin e saktë te db qe keni krijuar ne infinityfree

// Aktivizojmë përjashtimet (exceptions) për gabimet në mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $mysqli = new mysqli($host, $dbUser, $dbPass, $dbName);
    $mysqli->set_charset('utf8mb4');
    
    // Sinkronizojmë zonën kohore të MySQL me atë të PHP
    $offset = date('P');
    $mysqli->query("SET time_zone = '$offset'");
} catch (Exception $e) {
    // Kjo do të shfaqë gabimin e saktë në vend të faqes së bardhë 500 (p.sh. fjalëkalim i gabuar, host i gabuar etj.)
    echo "<h3>Gabim në lidhjen me Databazën:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Ju lutemi kontrolloni kredencialet në <strong>php/config.php</strong>.</p>";
    exit;
}


// Auto-expire subscriptions that have passed their end_date
$mysqli->query("UPDATE subscriptions SET status = 'expired' WHERE status = 'active' AND end_date < CURDATE()");

define('JWT_SECRET', 'super_secret_parking_management_key_2026'); 
?>