<?php

class Exporter
{
    protected $collectionCount;

    public function Exporter() {
        $this->collectionCount = 0;
    }

    public function processCollection($DAO, $collection, $destDir) {
        $collectionDir = $destDir . DIRECTORY_SEPARATOR . StringUtils::windowsFilenameSafeString($collection['NAME']);
        if (@mkdir($collectionDir)) {
            echo "Created $collectionDir\n";
        }

        $this->extractFilesForCollection($DAO, $collection, $collectionDir);
        $DAO->updateCollectionExportDate($collection);

        if (TESTING) {
            if ($this->collectionCount > 20) {
                echo "test mode - exiting.\n";
                exit(0);
            }
        }
        $this->collectionCount++;

        foreach($DAO->getChildCollections($collection) as $childCollection) {
            $this->processCollection($DAO, $childCollection, $collectionDir);
        }
    }

    public function extractFilesForCollection($DAO, $collection, $collectionDir) {
        $this->writeCollectionMetadata($DAO, $collection, $collectionDir);
	  $files = $DAO->getFilesForCollection($collection);
        foreach ($files as $file) {
            $sourcePath = $this->getFullPathForFile($file);
            $destDir = $collectionDir . DIRECTORY_SEPARATOR . $file['CATALOG_DATE'];
            if (!is_dir($destDir)) {
                mkdir($destDir);
            }
            $destPath = $destDir . DIRECTORY_SEPARATOR . $file['NAME'];
            echo "copying $sourcePath to $destPath...";
            copy($sourcePath, $destPath);
            echo "\ndone!\n";
            $this->extractMetadata($DAO, $file, $destDir);
            $DAO->updateAssetExportDate($file);
        }
    }

    public function extractMetadata($DAO, $file, $destDir) {
        $data = "KEY\tVALUE_TYPE\tVALUE\n";
        $metadata = $DAO->getMetadataForFile($file);
        foreach($metadata as $row) {
            $data .= $row['name'] . "\t" . $row['type'] . "\t" . $row['value'] . "\n";
        }
        file_put_contents($destDir . DIRECTORY_SEPARATOR . '_' . $file['NAME'] . '.tsv', $data);
    }

    public function writeCollectionMetadata($DAO, $collection, $collectionDir) {
        $header = "NAME\tCREATION_DATE\tCREATED_BY\tNOTES\n";
        $creator = $DAO->getUser($collection['WM_USER_ID']);
        $creator = $creator[0];
        $creatorString = $creator['FIRST_NAME'] . ' ' . $creator['LAST_NAME'] .
                ' (' . $creator['EMAIL'] . ')';
        $data = $header . $collection['NAME'] . "\t" . $collection['CREATION_DATE']->format('Y-m-d') . "\t" .
                $creatorString . "\t" . $collection['NOTES'] . "\n";
        file_put_contents($collectionDir . DIRECTORY_SEPARATOR . '_collection_data.tsv', $data);
    }

    public function getFullPathForFile($file) {
        return ASSET_BASE_PATH . DIRECTORY_SEPARATOR .
                trim($file['CATEGORY_PATH'], '/') . DIRECTORY_SEPARATOR .
                $file['CATALOG_DATE'] . DIRECTORY_SEPARATOR .
                $file['FILE_PATH'];
    }
}
