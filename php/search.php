<?php
// wyy
header("Content-Type: text/html; charset=utf-8");
$keywords = $_GET['keywords'];
$where=$keywords?"WHERE (location LIKE '%{$keywords}%') OR (province LIKE '%{$keywords}%')":null;
if (!$keywords){
    return false;
}else{
include_once "db.class.php";
    $sql = "SELECT * FROM location $where";
    $result = $dbObj->getAll($sql);
    $len_search=count($result);
    $result['len_search'] = $len_search;
    echo (!empty($result)) ? json_encode($result,JSON_UNESCAPED_SLASHES) : "null";
}
?>




