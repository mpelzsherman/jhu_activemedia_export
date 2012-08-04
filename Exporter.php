<?php

class Exporter
{
    public function processCollection($DAO, $collection, $destDir) {
        $collectionDir = $destDir . DIRECTORY_SEPARATOR . StringUtils::windowsFilenameSafeString($collection['NAME']);
        if (!mkdir($collectionDir)) {
            echo "Unable to create directory $collectionDir (possible duplicate?)\n";
        } else {
            echo "Created $collectionDir\n";
        }
        foreach($DAO->getChildCollections($collection) as $childCollection) {
            $this->processCollection($DAO, $childCollection, $collectionDir);
        }
    }
}
