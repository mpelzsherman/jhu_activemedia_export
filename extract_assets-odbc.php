$<?php
echo "connecting to database...\n";
$server = 'jhsql03.win.ad.jhu.edu';
$database = 'webware';
$user = 'webware';
$password = 'w3bwar3';
$connection = odbc_connect("Driver={SQL Server Native Client 10.0};Server=$server;Database=$database;", $user, $password);

if (!$connection) {
	die("unable to connect to server"); 
}

echo "DB connection successful!\n";
