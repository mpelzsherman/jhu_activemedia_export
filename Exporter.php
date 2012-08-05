<?php

class Exporter
{
    public function processCollection($DAO, $collection, $destDir) {
        $collectionDir = $destDir . DIRECTORY_SEPARATOR . StringUtils::windowsFilenameSafeString($collection['NAME']);
        if (@mkdir($collectionDir)) {
            echo "Created $collectionDir\n";
        }

        $this->extractFilesForCollection($DAO, $collection);

        foreach($DAO->getChildCollections($collection) as $childCollection) {
            $this->processCollection($DAO, $childCollection, $collectionDir);
        }
    }

    public function extractFilesForCollection($DAO, $collection) {
        foreach ($DAO->getFilesForCollection($collection) as $file) {
            echo $this->getFullPathForFile($file) . "\n";
        }

    }

    public function getFullPathForFile($file) {
        return ASSET_BASE_PATH . DIRECTORY_SEPARATOR .
                trim($file['CATEGORY_PATH'], '/') . DIRECTORY_SEPARATOR .
                $file['CATALOG_DATE'] . DIRECTORY_SEPARATOR .
                $file['FILE_PATH'];
    }
}
