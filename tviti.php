<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include "includes/init_sql.php";
include "includes/words.php";
include "includes/functions.php";
include "classify/evaluate_bayes.php";

//dabū 10 jaunākos tvītus
$latest = mysqli_query($connection, "SELECT * FROM tweets ORDER BY created_at DESC limit 0, 10");

//Load model
$model = file_get_contents("/home/baumuin/public_html/twitediens.tk/classify/model-proc2-nohash-smile-latest.json");
$classifier = new \Niiknow\Bayes();
$classifier->fromJson($model);

while($p=mysqli_fetch_array($latest)){
	$username = $p["screen_name"];
	$text = $p["text"];
	$ttime = $p["created_at"];
	$quoted_id = $p["quoted_id"];
	$quoted_text = NULL;
	$laiks = strtotime($ttime);
	$laiks = date("d.m.Y H:i", $laiks);
	
	if($quoted_id != NULL){
		$quoted = mysqli_query($connection, "SELECT text, screen_name FROM tweets WHERE id = $quoted_id");
		$qq=mysqli_fetch_array($quoted);
		if($qq){
			$quoted_text = $qq["text"];
			$quoted_screen_name = $qq["screen_name"];
		}
	}
		
	$automatic = classify($text, $classifier);
	// $automatic = "nei";
	switch ($automatic){
		case "pos":
			$color = "#00FF00";
			break;
		case "neg":
			$color = "#FF3D3D";
			break;
		case "nei":
			$color = "black";
			break;
		default:
			$color = "black";
	}
	
	#Iekrāso un izveido saiti uz katru pieminēto lietotāju tekstā
	#Šo vajadzētu visur...
	
	$text = enrich_text($text, "#229cec", $validFood);
	
?>
<div style="<?php if ((time()-StrToTime($ttime))<5){echo"opacity:".((time()-StrToTime($ttime))/5).";";}?>" class="tweet">
	<div class="lietotajs" style="border-bottom: 0.18em dashed <?php echo $color; ?>;"><?php echo '<a style="text-decoration:none;color:#658304;" href="/draugs/'.trim($username).'">@'.trim($username).'</a> ';?> ( <?php echo $laiks;?> )</div>
<?php echo $text."<br/>";

if(isset($quoted_text) && strlen($quoted_text) > 0){
	echo "<div style='border:1px dotted #000; border-radius:5px; padding:2px;'><small>";
	echo '<a style="text-decoration:none;color:#658304;" href="/draugs/'.str_replace('@','',trim($quoted_screen_name)).'">@'.trim($quoted_screen_name).'</a>: ';
	echo $quoted_text."</small></div><br/>";
}
?><br/>
</div>
<?php
}
?>