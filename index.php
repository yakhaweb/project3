<?php
define('admin23', true);
include("include/db_connect.php");
include("functions/functions.php");
session_start();
//include("include/auth_cookie.php");

$cat= clear_string( isset($_GET["cat"])) ?? '';
$type= clear_string( isset($_GET["type"])) ?? '';
$sorting=$_GET["sort"] ?? '';
//$page=(isset ($_GET['page'])) ?? '';

// Включение сообщений о всех ошибках
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


/*Убирает ошибку Notice: Undefined variable:
error_reporting(E_ALL & ~E_NOTICE);*/


switch($sorting){
    
    case 'price-asc';
    $sorting='price ASC';
    $sort_name='От дешовых к дорогим';
    break;
    
    case 'price-desc';
    $sorting='price DESC';
    $sort_name='От дорогих к дешовым';
    break;
    
    case 'news';
    $sorting='datetime DESC';
    $sort_name='Новинки';
    break;
    
    case 'brand';
    $sorting='brand';
    $sort_name='От А до Я';
    break;
    
    default:
    $sorting='products_id DESC';
    $sort_name='Нет сортировки';
    break;   
}	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
 	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <link href="trackbar/trackbar.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/js/jquery-3.1.1.min"></script>
    <script type="text/javascript" src="/js/ji.js"></script>
    <script type="text/javascript" src="/js/jquery.cookie.min.js"></script>
    <script type="text/javascript" src="/js/textchange.js"></script>



	<title>Гуртівня будівельних і господарських товарів</title> 
</head>

<body>

<div id="block-body">
	
	<div id="block-header">
		<?php
			include("include/block-header.php");	
		?>
	</div>
	
	<div id="block-sorting">
			
		<div class="header-flex-container">
			
			<div class="header-flex-block">				
				<ul>
					<li><img id="style-grid" src="/images/table.png"/></li>
					<li><img id="style-list" src="/images/list.png"/></li>
				</ul>
			</div>
			
			<div class="header-flex-block">				
				<ul>
					<li><p align="right" id="block-basket"><img src="/images/cart.png"/><a href="cart.php?action=oneclick">Кошик порожній</a></p></li>
				</ul>
			</div>
			
		</div>
		
		<div class="menu-flex-container">
			
			<div class="menu-flex-block">
				<ul>
					<li><img src="/images/home.png"/></li>
					<li><a href="index.php">Головна</a></li>
				</ul>
			</div>
			
			<div class="menu-flex-block">
				<ul>	
					<li><img src="/images/new.png"/></li>
					<li><a href="view_aystopper.php?go=news">Новинки</a></li>
				</ul>
			</div>
			
			<div class="menu-flex-block">				
				<ul>
					<li><img src="/images/liader.png"/></li>
					<li><a href="view_aystopper.php?go=leaders">Лідер продажу</a></li>
				</ul>	
			</div>
			
			<div class="menu-flex-block">
				<ul>
					<li><img src="/images/discount.png"/></li>
					<li><a href="view_aystopper.php?go=sale">Розпродаж</a></li>
				</ul>
			</div>
			
			<div class="menu-flex-block">				
				<ul id="options-list">
					<li><img src="/images/sorting.png"/></li>
						<li><a id="select-sort">Без сортування</a>
							<ul id="sorting-list">
								<li><a href="index.php?sort=price-asc">Від дешевих до дорогих</a></li>
								<li><a href="index.php?sort=price-desc">Від дорогих до дешевих</a></li>
								<li><a href="index.php?sort=news">Новинки</a></li>
								<li><a href="index.php?sort=brand">Від А до Я</a></li>
						</ul>
					</li>
				</ul>
			</div>
			
		</div>
		
		<p id="nav-breadcrumbs"><a href="index.php">Головна сторінка</a> \ <span>Усі товари</span></p>
			
	</div>
		
	<div id="block-right">
		<?php
			include("include/block-category.php");
			include("include/block-parameter.php");
			include("include/block-contact-right.php");
		?>
	</div>



<div id="block-content">

<!--табличный вывод товара-->

<ul id="block-tovar-grid">
<?php

//---Calculation quantity page---------
    $num=12;

    
    $count=mysqli_query($link, "SELECT COUNT(*) FROM `table-products` WHERE visible='1'");
    $temp=mysqli_fetch_array($count);
    
    if ($temp[0]>0){
        $tempcount=$temp[0];
        //Находим общее число страниц
        $total=(($tempcount-1)/$num)+1;
        $total=intval($total);
        
        if(isset($_GET['page']))
            $page=(int)$_GET['page'];
        //$page=intval($page);
        
        if(empty($page) or $page<0) $page=1;
        if($page>$total) $page=$total;
        
        $start=$page*$num-$num;
        
        $qury_start_num="LIMIT $start, $num";
        
    }
//----End calculation quantity page--------------    


	$result=mysqli_query($link, "SELECT * FROM `table-products` WHERE visible='1' ORDER BY $sorting $qury_start_num");
    if (mysqli_num_rows($result) > 0)
        {
        $row=mysqli_fetch_array($result);
        
        // -------function do size foto--------------
        do{
            
            if($row["image"] !=""&& file_exists("./upload_images/".$row["image"])){
            $img_path='./upload_images/'.$row["image"];
            $max_width=150;
            $max_height=150;
            list($width, $height)=getimagesize($img_path);
            $ratioh=$max_height/$height;
            $ratiow=$max_width/$width;
            $ratio=min($ratioh, $ratiow);
            $width=intval($ratio*$width);
            $height=intval($ratio*$height);
            }
            else{
                $img_path="/images/no-images.jpg";
                $width=150;
                $height=150;
            }        
        //------------end size foto-----------------    
                               
            echo '
				<li>

					<div class="cart-tovar-grid">
						<div class="block-images-grid">
							<img src="'.$img_path.'" width="'.$width.'" height="'.$height.'"/>
						</div>	
							</br>            
						<p class="style-tittle-grid" ><a href="view_content.php?id='.$row["products_id"].'">'.$row["title"].'</a></p>
							</br>
						<p class="style-price-grid" ><strong>'.group_numerals($row["price"]).'</strong> грн.</p>
							</br>
						<a class="add-cart-style-grid" tid="'.$row["products_id"].'">купити</a>
			
					</div>
					
				</li>
			
            ';
            
        }
        while($row=mysqli_fetch_array($result));
    }

?>
</ul>
</br>
<!--конец табличного вывода товаров*/-->

<!--списочный вывод товара-->

<ul id="block-tovar-list">
<?php
	$result=mysqli_query($link, "SELECT * FROM `table-products` WHERE visible='1' ORDER BY $sorting $qury_start_num");
    if (mysqli_num_rows($result) > 0){
        
        $row=mysqli_fetch_array($result);
        
        
        do{
            // -------function do size foto--------------
            if ($row["image"] !=""&& file_exists("./upload_images/".$row["image"])){
            $img_path='./upload_images/'.$row["image"];
            $max_width=100;
            $max_height=100;
            list($width, $height)=getimagesize($img_path);
            $ratioh=$max_height/$height;
            $ratiow=$max_width/$width;
            $ratio=min($ratioh, $ratiow);
            $width=intval($ratio*$width);
            $height=intval($ratio*$height);
            }
            else{
                $img_path="/images/no-images.jpg";
                $width=100;
                $height=100;
            }        
        //-------------------------------------             
                       
            echo '
            <li>

					<div class="cart-tovar-list">
						<div class="flex-tovar-list">
						<div class="block-images-list">
							<img src="'.$img_path.'" width="'.$width.'" height="'.$height.'"/>
						</div>
						</div>

						<div class="flex-tovar-list">
						          
						<p class="style-tittle-list" ><a href="view_content.php?id='.$row["products_id"].'">'.$row["title"].'</a></p>


						</div>
						
						<div class="flex-tovar-list">

						<p class="style-price-list" ><strong>'.group_numerals($row["price"]).'</strong> грн.</p>

						</div>
						
						<div class="flex-tovar-list">

						<a class="add-cart-style-list" tid="'.$row["products_id"].'">купити</a>
						
						</div>
					</div>
					
				</li>
				</br>
            ';
            
        }
        while($row=mysqli_fetch_array($result));
    }
    echo '</ul>';
    
    //нижняя навигация по страницам
    if($page!=1){$pstr_prev='<li><a class="pstr-prev" href="index.php?page='.($page-1).'">&lt;</a></li>';}
    if($page!=$total) $pstr_next='<li><a class="pstr-next" href="index.php?page='.($page+1).'">&gt;</a></li>';
    
    if($page-5>0) $page5left='<li><a href="index.php?page='.($page-5).'">'.($page-5).'</a></li>';
    if($page-4>0) $page4left='<li><a href="index.php?page='.($page-4).'">'.($page-4).'</a></li>';
    if($page-3>0) $page3left='<li><a href="index.php?page='.($page-3).'">'.($page-3).'</a></li>';
    if($page-2>0) $page2left='<li><a href="index.php?page='.($page-2).'">'.($page-2).'</a></li>';
    if($page-1>0) $page1left='<li><a href="index.php?page='.($page-1).'">'.($page-1).'</a></li>';
    
    if($page+5<=$total) $page5right='<li><a href="index.php?page='.($page+5).'">'.($page+5).'</a></li>';
    if($page+4<=$total) $page4right='<li><a href="index.php?page='.($page+4).'">'.($page+4).'</a></li>';
    if($page+3<=$total) $page3right='<li><a href="index.php?page='.($page+3).'">'.($page+3).'</a></li>';
    if($page+2<=$total) $page2right='<li><a href="index.php?page='.($page+2).'">'.($page+2).'</a></li>';
    if($page+1<=$total) $page1right='<li><a href="index.php?page='.($page+1).'">'.($page+1).'</a></li>';
    
    
    if($page+5<$total){
        $strtotal='<li><p class="nav-point">...</p></li><li><a href="index.php?page='.$total.'">'.$total.'</a></li>';
    }
    else{
        $strtotal="";
    }
    
    if($total>=1){
        echo '
        <div class="pstrnav">
        <ul>
        ';
		if(!isset($pstr_prev))
			$pstr_prev = '';
		if(!isset($page5left))
			$page5left = '';
		if(!isset($page4left))
			$page4left = '';
		if(!isset($page3left))
			$page3left = '';
		if(!isset($page2left))
			$page2left = '';
		if(!isset($page1left))
			$page1left = '';
		if(!isset($page1right))
			$page1right = '';
		if(!isset($page2right))
			$page2right = '';
		if(!isset($page3right))
			$page3right = '';
		if(!isset($page4right))
			$page4right = '';
		if(!isset($page5right))
			$page5right = '';
		if(!isset($pstr_next))
			$pstr_next = '';
		
        
        echo $pstr_prev.$page5left.$page4left.$page3left.$page2left.$page1left."<li><a class='pstr-active' href='index.php?page=".$page."'>".$page."</a></li>".$page1right.$page2right.$page3right.$page4right.$page5right.$strtotal.$pstr_next;
       echo '
        </ul>
        </div>
		</br>
        ';
    }
    //--------------------------------------------
?>

<!--конец списочного вывода товаров-->
</div>

</div>

<div id="block-random">
	<?php
		include("include/block-random.php");
	?>
</div>

<div id="block-footer">
	<?php
		include("include/block-footer.php");	
	?>
</div>


</body>
</html>