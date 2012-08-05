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
        return $this->query("select * from wm_collection where company_id = " . JHU_COMPANY_ID . " and parent_collection_id is null");
    }

    function getChildCollections($collection)
    {
        return $this->query("select * from wm_collection where company_id = " . JHU_COMPANY_ID . " and parent_collection_id = {$collection['COLLECTION_ID']}");
    }

    function getFilesForCollection($collection) {
        return $this->query("select f.file_id as FILE_ID, f.file_path as FILE_PATH, f.name as NAME, convert(varchar,convert(date, catalog_date)) as CATALOG_DATE, ac.path as CATEGORY_PATH " .
            "from wm_collection_to_asset wca " .
            "join asset a on wca.asset_id = a.asset_id " .
            "join wm_file f on a.current_file_id = f.file_id " .
            "join asset_category ac on a.asset_category_id = ac.asset_category_id " .
            "where wca.collection_id = " . $collection['COLLECTION_ID']);
    }
}
