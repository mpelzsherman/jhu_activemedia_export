<?php

class DAO
{

    protected $conn;

    function DAO($serverName, $uid, $pwd, $db)
    {
        $connectionInfo = array(
            'UID' => $uid,
            'PWD' => $pwd,
            'Database' => $db);
        /* Connect using SQL Server Authentication. */
        $this->conn = sqlsrv_connect($serverName, $connectionInfo);
        if ($this->conn === false) {
            echo "Unable to connect.</br>";
            die(print_r(sqlsrv_errors(), true));
        }
    }

    function query($sql)
    {
        $stmt = sqlsrv_query($this->conn, $sql);
        if ($stmt === false) {
            echo "Error in executing query.</br>";
            die(print_r(sqlsrv_errors(), true));
        }

        $result = array();
        /* Retrieve and display the results of the query. */
        while ($row = sqlsrv_fetch_array($stmt)) {
            $result[] = $row;
        }

        sqlsrv_free_stmt($stmt);
        return $result;
    }

    function getTopLevelCollections()
    {
        return $this->query("select * from wm_collection where company_id = " . JHU_COMPANY_ID .
            " and parent_collection_id is null and export_date is null");
    }

    function getChildCollections($collection)
    {
        return $this->query("select * from wm_collection where company_id = " . JHU_COMPANY_ID .
            " and parent_collection_id = {$collection['COLLECTION_ID']} and export_date is null");
    }

    function getFilesForCollection($collection) {
        return $this->query("select f.file_id as FILE_ID, f.file_path as FILE_PATH, f.name as NAME, convert(varchar,convert(date, catalog_date)) as CATALOG_DATE, ac.path as CATEGORY_PATH, f.notes as NOTES " .
            "from wm_collection_to_asset wca " .
            "join asset a on wca.asset_id = a.asset_id " .
            "join wm_file f on a.current_file_id = f.file_id " .
            "join asset_category ac on a.asset_category_id = ac.asset_category_id " .
            "where wca.collection_id = " . $collection['COLLECTION_ID'] .
            " and a.export_date is null");
    }

    function getUser($id) {
        return $this->query("select * from wm_user where wmuser_id = $id");
    }

    function getMetadataForFile($file) {
        $rawrows = $this->query("select mdl.name as name, md.string_object as value, " .
        "mdl.data_type as type from wm_meta_data md " .
        "join wm_meta_data_label mdl on md.meta_data_label_id = mdl.meta_data_label_id " .
        "where md.object_id = {$file['FILE_ID']} and md.class_name = 'WMAssetMetaData'");
        $result = array();
        foreach ($rawrows as $item) {
            if ($item['value']) {
                $item['type'] = ($item['type'] == 3) ? 'date' : 'string';
                $result[] = $item;
            }
        }
        // add additional items:
        if ($file['NOTES']) {
            $result[] = array('name' => 'Notes', 'value' => $file['NOTES'], 'type' => 'string');
        }
        $result[] = array('name' => 'Date', 'value' => $file['CATALOG_DATE'], 'type' => 'date');
        return $result;
    }

    function updateAssetExportDate($file) {
        $this->query("update asset set export_date = CURRENT_TIMESTAMP where current_file_id = {$file['FILE_ID']}");
    }

    function updateCollectionExportDate($collection) {
        $this->query("update wm_collection set export_date = CURRENT_TIMESTAMP where collection_id = {$collection['COLLECTION_ID']}");
    }
}
