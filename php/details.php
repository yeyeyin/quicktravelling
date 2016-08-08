
<?php
include_once "db.class.php";
if ($_SERVER["REQUEST_METHOD"] == "GET") {

	$id = $_GET['id'];
	// $id = 10;
	if (!$id)
	    return false;

	// // 查询location并返回
	$sql1 = "SELECT * FROM location WHERE id=$id";
	$detail = $dbObj->getOne($sql1);//此处用getOne则返回一条记录，对应js对象不用加[]
	// $key =$detail['location'];
	// $key = iconv('utf-8','gbk',$key);//转换格式,此处key的编码格式问题导致无法查询
	// $sql2 = "SELECT * FROM attraction WHERE location=$key ";

	$sql2 = "SELECT attraction.attraction,imgsrc,detailsrc  FROM attraction,location WHERE (attraction.location= location.location) AND (location.id=$id) ";
	$attraction = $dbObj->getAll($sql2);
	// print_r($attraction) ;

	$sql3 = "SELECT foods.food_name,imgsrc  FROM foods,location WHERE (foods.location= location.location) AND (location.id=$id) ";
	$foods = $dbObj->getAll($sql3);
	// print_r($foods) ;


	//获取introduction的值，拼接为路径，读取txt内容
	$introduction = $detail['introduction'];
	// print_r($introduction);
	$src_intro = "../$introduction";//最终采用相对路径
	@$fp_intro = file_get_contents($src_intro,'rb');//将文件读入到字符串中
	$fp_intro = iconv('gbk','utf-8',$fp_intro);//转换格式

	//获取routes的值，拼接为路径，读取txt内容
	$routes = $detail['routes'];
	$src_routes = "../$routes";//最终采用相对路径
	@$fp_routes = file_get_contents($src_routes,'rb');//将文件读入到字符串中
	$fp_routes = iconv('gbk','utf-8',$fp_routes);//转换格式

	//获取detailsrc的值，拼接为路径，读取txt内容
	$attr=Array();
	$len_attr=count($attraction);
	for($i=1;$i<=$len_attr;$i++){
		$attr[$i]= $attraction[$i]['detailsrc'];
		$src_attr[$i] = "../$attr[$i]";//最终采用相对路径
		@$fp_attr[$i] = file_get_contents($src_attr[$i],'rb');//将文件读入到字符串中
		$fp_attr[$i] = iconv('gbk','utf-8',$fp_attr[$i]);//转换格式
	}
	// print_r($fp_attr);

	//获取imgsrc的值
	for($i=1;$i<=$len_attr;$i++){
		$imgsrc[$i]= $attraction[$i]['imgsrc'];
	}

	$len_food=count($foods);
	$name_foods=Array();
	for($j=1;$j<=$len_food;$j++){
		$name_foods[$j]= $foods[$j]['food_name'];
	}

	//获取food_imgsrc的值
	$src_foods=Array();
	for($j=1;$j<=$len_food;$j++){
		$src_foods[$j]= $foods[$j]['imgsrc'];
	}

	//将数据库搜索的内容与获取的文本拼接在一起
	$detail['introtxt'] = $fp_intro;
	$detail['routestxt'] = $fp_routes;
	$detail['attrtxt'] = $fp_attr;
	$detail['imgtxt'] = $imgsrc;
	$detail['attrlen'] = $len_attr;
	$detail['foodsname'] = $name_foods;
	$detail['foodsimg'] = $src_foods;
	$detail['foodlen'] = $len_food;
	// echo $routes;
	echo (!empty($detail)) ? json_encode($detail,JSON_UNESCAPED_SLASHES) : "null";

}else if($_SERVER["REQUEST_METHOD"] == "POST"){ //更新赞

	$num = $_POST['num'];
	$id = $_POST['id'];
	$dase = $_POST['dase'];

	$sql = 'id ='.$id;  

    $result=$dbObj->update($dase,array("like_nums"=>$num),$sql);
	if($result){
		echo(1);
	}
}


//已可以实现读取location
// $sql1 = "SELECT * FROM location WHERE id=$id";
// $result = $dbObj->getOne($sql1);//此处用getOne则返回一条记录，对应js对象不用加[]
// echo (!empty($result)) ? json_encode($result,JSON_UNESCAPED_SLASHES) : "null";
?>



