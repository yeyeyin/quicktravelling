<?php

header("Content-Type: text/html; charset=utf-8");

$cls = $_GET['cls'];
$inAjax = $_GET['inAjax'];
$dase = $_GET['dase'];
//$id = $id ? $id : "default";
if (!$inAjax)
    return false;

include_once "db.class.php";

	if($cls == 'waterfall'){ //加载图片
		$range1 = $_GET['page']*5;
		$range2 = $range1 + 5;
	    $sql = "SELECT * FROM $dase WHERE id>$range1 and id <=$range2";
	    //$sql="SELECT 8 from product where id>='$range1' order by id";

	    $result = $dbObj->getAll($sql);
	    echo (!empty($result)) ? json_encode($result,JSON_UNESCAPED_SLASHES) : "null";	

	}else if($cls == 'rank'){ //排行
		$sql = "SELECT location,like_nums FROM $dase ORDER BY like_nums  DESC LIMIT 5";
		$result = $dbObj->getAll($sql);
	    echo (!empty($result)) ? json_encode($result,JSON_UNESCAPED_SLASHES) : "null";	
	}


   
	   
?>



