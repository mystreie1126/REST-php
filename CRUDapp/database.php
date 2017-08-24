<?php

// Database connection settings - replace with yours local
$db_host = "localhost";
$db_name = "api";
$db_user = 'root';
$db_pass = "mysql1"; // password  arlready changed

// Establish database connection PDO
try {
    $db = new PDO('mysql:host='.$db_host.';dbname='.$db_name, $db_user, $db_pass);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

// Create demo contact table if it not exist
$db->query("CREATE TABLE IF NOT EXISTS `contacts` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(128) NOT NULL DEFAULT '',
            `email` varchar(64) NOT NULL DEFAULT '',
            `phone` varchar(20) NOT NULL DEFAULT '',
            PRIMARY KEY (`id`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;");