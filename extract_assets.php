<?php
require_once('constants.inc');
require_once('DAO.php');
require_once('StringUtils.php');
require_once('Exporter.php');

if (is_dir(DEST_DIR)) {
    exec('rm -rf ' . DEST_DIR);
}
mkdir(DEST_DIR);

$DAO = new DAO(DB_SERVER_NAME, DB_UID, DB_PWD, DB_DATABASE);

$exporter = new Exporter();

foreach($DAO->getTopLevelCollections() as $collection) {
    $exporter->processCollection($DAO, $collection, DEST_DIR);
}



?>