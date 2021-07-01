<?php


$q = $_GET["q"];//选择横图或竖图 q=1时,横图;q=2时,竖图;q=0时默认不添加条件
$m = $_GET["m"];//选择是否为nsfw模式 m=1时,开启nsfw模式,m=2时,强制nsfw模式,默认m=0,不开启
$r = $_GET["r"];//自定义参数
$return = $_GET["return"];//返回格式(img/https/json)
$album = $_GET["album"];//传入相册名字

$return==NULL?$return = "img":$return;//默认返回图片
$r==NULL?$r=1:$r;
$album==NULL?$album=-1:$album;//album==-1=>无限制条件

$img_url="yourcheveretoURL/images/";

//连接至你所使用的chevereto图床的数据库
$con = new mysqli("127.0.0.1","root","password","chevereto","3307");

if (!$con){
    die('Could not connect: ' . mysqli_error($con));
}

mysqli_set_charset($con, "utf8");

$q_str="";$m_str="";$r_str="";

//此为自定义参数, 不需要可以删掉
switch($r){
    case 0:
        $r_str="(image_album_id !=17 AND image_album_id !=10 AND image_album_id !=26 AND image_album_id !=36 AND image_album_id !=22 AND image_album_id !=39)";
        break;
    case 1:
        $r_str="(image_album_id >= 31 AND image_album_id != 36 AND image_album_id != 39)";
        break;
    case 2:
        $r_str="(image_likes = 1)";
        break;
}

switch($q){
    case 0:
        $q_str="(image_width != -1) ";
        break;
    case 1:
        $q_str="(image_width >= image_height) ";
        break;
    case 2:
        $q_str="(image_width <= image_height) ";
        break;
}

switch($m){
    case 0:
        $m_str="(image_nsfw = 0) ";
        break;
    case 1:
        $m_str="(image_nsfw != -1) ";
        break;
    case 2:
        $m_str="(image_nsfw = 1) ";
        break;
}

$sql="SELECT 
image_id, image_date, image_name, image_extension ,image_width, image_height
FROM chv_images
WHERE ".$q_str." AND ".$m_str." AND ".$r_str."
ORDER BY RAND() LIMIT 1";

if($album!=-1){
    $sql="SELECT 
            image_id, image_date, image_name, image_extension ,image_width, image_height
            FROM chv_images
            LEFT JOIN chv_albums ON chv_albums.album_name = \"".$album."\"
            WHERE ".$q_str." AND ".$m_str." AND chv_images.image_album_id = chv_albums.album_id
            ORDER BY RAND() LIMIT 1";
}

$result=mysqli_query($con,$sql);

$row=$result->fetch_all(MYSQLI_BOTH);
mysqli_close($con);
//echo $row;
$img_obj=$row[0];


$date=substr($img_obj["image_date"],0,10);
$date=str_replace("-","/",$date);

$img_url=$img_url . $date ."/". $img_obj["image_name"] .".". $img_obj["image_extension"];
 
if($return == 'img'){

}
// $img = file_get_contents($img_url,true);
// header("Content-Type: image/jpeg;text/html; charset=utf-8");
// echo $img;

switch($return){
    case 'img':
        $img = file_get_contents($img_url,true);
        header("Content-Type: image/jpeg;text/html; charset=utf-8");
        echo $img;
        break;
    case 'https':
        echo "<script language='javascript' 
        type='text/javascript'>";  
        echo "window.location.href='$img_url'";  
        echo "</script>"; 
        break; 
    case 'json':    
        $jsondata['url']=$img_url;
        $jsondata['width']=$img_obj["image_width"];
        $jsondata['height']=$img_obj["image_height"];
        $jsondata=json_encode($jsondata);
        echo $jsondata;
        break;
}


?>
