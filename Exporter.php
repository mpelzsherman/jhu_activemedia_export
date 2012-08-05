<?php

class Exporter
{
    public function processCollection($DAO, $collection, $destDir) {
        $collectionDir = $destDir . DIRECTORY_SEPARATOR . StringUtils::windowsFilenameSafeString($collection['NAME']);
        if (@mkdir($collectionDir)) {
            echo "Created $collectionDir\n";
        }

        $this->extractFilesForCollection($DAO, $collection, $collectionDir);

        foreach($DAO->getChildCollections($collection) as $childCollection) {
            $this->processCollection($DAO, $childCollection, $collectionDir);
        }
    }

    public function extractFilesForCollection($DAO, $collection, $collectionDir) {
        foreach ($DAO->getFilesForCollection($collection) as $file) {
            $sourcePath = $this->getFullPathForFile($file);
            $destDir = $collectionDir . DIRECTORY_SEPARATOR . $file['CATALOG_DATE'];
            if (!is_dir($destDir)) {
                mkdir($destDir);
            }
            $destPath = $destDir . DIRECTORY_SEPARATOR . $file['NAME'];
            echo "copying $sourcePath to $destPath...";
            copy($sourcePath, $destPath);
            echo "\ndone!\n";
        }

    }

    public function getFullPathForFile($file) {
        return ASSET_BASE_PATH . DIRECTORY_SEPARATOR .
                trim($file['CATEGORY_PATH'], '/') . DIRECTORY_SEPARATOR .
                $file['CATALOG_DATE'] . DIRECTORY_SEPARATOR .
                $file['FILE_PATH'];
    }
}
