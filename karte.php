<?php
include "includes/laiks.php";
include "includes/config.php";
// Turn off all error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);
?>
<h2 style='margin:auto auto; text-align:center;'>Twitter gardēžu karte</h2>
<h5 style='margin:auto auto; text-align:center;font-size:12px;'>
<form method="post" action="?id=karte">
No <input value="<?php echo $nn;?>" readonly size=9 type="text" id="from" name="from"/> līdz <input value="<?php echo $ll;?>" readonly size=9 type="text" id="to" name="to"/>
<INPUT TYPE="submit" name="submit" value="Parādīt"/>
</form>
</h5>
<br/>
<?php
//Paņem dažādās vietas
$q = mysqli_query($connection, "SELECT distinct geo, count( * ) skaits FROM `tweets` WHERE geo!='' and created_at between '$no' AND '$lidz' GROUP BY geo ORDER BY count( * ) DESC");
?>
		<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=<?php echo GOOGLE_MAP_KEY;?>&sensor=false"></script>
		<script type="text/javascript">
			$(window).resize(initialize);
			function initialize() {
				var latlng = new google.maps.LatLng(56.9465363, 24.1048503);
				var settings = {
					zoom: 7,
					center: latlng,
					mapTypeId: google.maps.MapTypeId.ROADMAP};
				var map = new google.maps.Map(document.getElementById("map_canvas"), settings);
<?php
				$i=0;
				while($r=mysqli_fetch_array($q)){
				   $vieta=$r["geo"];
				   $skaits=$r["skaits"];
				   if ($skaits==1) {$tviti=" tvīts";} else {$tviti=" tvīti";}
					$irvieta = mysqli_query($connection, "SELECT * FROM vietas where nosaukums='$vieta'");
					if(mysqli_num_rows($irvieta)==0){
						//ja nav tādas vietas datu bāzē,
						//dabū vietas koordinātas
						$string = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".str_replace(" ", "%20",$vieta)."&sensor=true&key=".GOOGLE_MAP_KEY);
						$json=json_decode($string, true);
						if( isset($json["results"][0]["address_components"])){
							$gar = sizeof($json["results"][0]["address_components"]);
							for ($z = 0; $z < $gar; $z++){
								if($json["results"][0]["address_components"][$z]['types'][0] == 'country') $valsts = $json["results"][0]["address_components"][$z]['long_name'];
							}
							$lat = $json["results"][0]["geometry"]["location"]["lat"];
							$lng = $json["results"][0]["geometry"]["location"]["lng"];
							if ($lat!=0 && $lng!=0){
								$ok = mysqli_query($connection, "INSERT INTO vietas (nosaukums, lng, lat, valsts) VALUES ('$vieta', '$lng', '$lat', '$valsts')");
							}
						}
					}else{
						$arr=mysqli_fetch_array($irvieta);
						//ja ir
						$lat = $arr['lat'];
						$lng = $arr['lng'];
					}
					$irvieta = mysqli_query($connection, "SELECT * FROM vietas where nosaukums='$vieta'");
					if(mysqli_num_rows($irvieta)!=0){
					?>
					//Apraksts
					var contentString<?php echo $i;?> = '<a href="/vieta/<?php echo $vieta;?>"><?php echo $vieta." - ".$skaits.$tviti." par ēšanas tēmām";?>';
					var infowindow<?php echo $i;?> = new google.maps.InfoWindow({
						content: contentString<?php echo $i;?>
					});
						
					//Atzīmē vietu kartē
					var parkingPos = new google.maps.LatLng(<?php echo $lat;?>, <?php echo $lng;?>);
					var marker<?php echo $i;?> = new google.maps.Marker({
						position: parkingPos,
						map: map,
						title:"<?php echo $vieta;?>"
					});
					google.maps.event.addListener(marker<?php echo $i;?>, 'click', function() {
					  infowindow<?php echo $i;?>.open(map,marker<?php echo $i;?>);
					});
					<?php
					$i=$i+1;
					}
				}


$qx = mysqli_query($connection, "SELECT distinct geo, count( * ) skaits FROM `tweets` WHERE geo!='' and created_at between '$no' AND '$lidz' GROUP BY geo ORDER BY count( * ) DESC");
$reg_code = array();
while($rx=mysqli_fetch_array($qx)){
   $vieta=$rx["geo"];
   $skaits=$rx["skaits"];
   if ($skaits==1) {$tviti=" tvīts";} else {$tviti=" tvīti";}
	$irvieta = mysqli_query($connection, "SELECT * FROM vietas where nosaukums='$vieta'");
	if(mysqli_num_rows($irvieta)==0){
		//ja nav tādas vietas datu bāzē,
		//dabū vietas koordinātas
		$string = file_get_contents("http://api.positionstack.com/v1/forward?access_key=3b174120145eb00b8024eb435c65f8d2&query=".str_replace(" ", "%20",$vieta));
		$json=json_decode($string, true);
		
		$valsts = $json["data"][0]["country"];
		$lat = $json["data"][0]["latitude"];
		$lng = $json["data"][0]["longitude"];
		
		
		if ($lat!=0 && $lng!=0){
			$ok = mysqli_query($connection, "INSERT INTO vietas (nosaukums, lng, lat, valsts) VALUES ('$vieta', '$lng', '$lat', '$valsts')");
		}
	}else{
		$arr=mysqli_fetch_array($irvieta);
		//ja ir
		$lat = $arr['lat'];
		$lng = $arr['lng'];
	}
	if(strlen($lat) > 0 && strlen($lng) > 0){
		$coord_code = mysqli_query($connection, "SELECT * FROM coord_code where lat like $lat AND lng like $lng");
		if(mysqli_num_rows($coord_code)!=0){
			//get region code and add it to array
			$c_code = mysqli_fetch_array($coord_code);
			//ja ir
			$code = $c_code['code'];
			$countryCode = $c_code['countryCode'];
			$adminName1 = $c_code['adminName1'];
			$reg_code[$code] = array();
			if(!isset($reg_code[$code]['sk'])) $reg_code[$code]['sk'] = 0;
			$reg_code[$code]['sk'] = $reg_code[$code]['sk']+$skaits;
			$reg_code[$code]['cc'] = $countryCode;
			$reg_code[$code]['ad'] = $adminName1;
			$country_code[$countryCode] = $country_code[$countryCode]+$skaits;
		}else{
			//get code from coordinates and insert into db
			$string = file_get_contents("http://api.geonames.org/countrySubdivisionJSON?formatted=true&lat=".$lat."&lng=".$lng."&username=saifer&style=full");
			$json=json_decode($string, true);
			$code = $json["codes"][1]["code"];
			$countryCode = $json["countryCode"];
			$adminName1 = $json["adminName1"];
			mysqli_query($connection, "INSERT INTO coord_code (lng, lat, code, countryCode, adminName1) VALUES ('$lng', '$lat', '$code', '$countryCode', '$adminName1')");
			$reg_code[$code]['sk'] = $reg_code[$code]['sk']+$skaits;
			$reg_code[$code]['cc'] = $countryCode;
			$reg_code[$code]['ad'] = $adminName1;
			$country_code[$countryCode] = $country_code[$countryCode]+$skaits;
		}
	}
}
?>
			}
		</script>
	
		
<!-- new google map chart -->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
	google.load('visualization', '1', {'packages': ['geochart']});
	google.setOnLoadCallback(drawVisualization2);
	$(window).resize(drawVisualization2);

	function drawVisualization2() {var data = new google.visualization.DataTable();

	 data.addColumn('string', 'Province');
	 data.addColumn('number', 'Value');  
	 data.addColumn({type:'string', role:'tooltip'});
<?php
$maxVal = 0;
foreach ($reg_code as $key => $value){
   if ($value['sk']==1) {$tviti=" tvīts";} else {$tviti=" tvīti";}
   if($key != "RIX"){
	if ($value['sk'] > $maxVal) $maxVal = $value['sk'];
		echo "data.addRows([[ '".$value['cc']."-".$key."',".$value['sk'].",'".str_replace("'","",$value['ad'])." - ".$value['sk'].$tviti."']]);";
	}else{
		$rixcc=$value['cc'];
		$rixad=$value['ad'];
	}
}
	?>
		data.addRows([[ '<?php echo $rixcc."-RIX";?>',<?php echo $maxVal*1.3;?>,'<?php echo str_replace("'","",$rixad)." - daudz tvītu";?>']]);


        var options = {
			resolution: 'provinces',
			region:'LV'
		};

        var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));

        chart.draw(data, options);
      }
    </script>

		
<!-- new google map chart -->
    <script type="text/javascript">
	google.load('visualization', '1', {'packages': ['geochart']});
	google.setOnLoadCallback(drawVisualization);
	$(window).resize(drawVisualization);

	function drawVisualization() {var data = new google.visualization.DataTable();

	 data.addColumn('string', 'Province');
	 data.addColumn('number', 'Value');  
	 data.addColumn({type:'string', role:'tooltip'});
<?php
$maxVal = 0;
foreach ($country_code as $key => $value){
   if ($value['sk']==1) {$tviti=" tvīts";} else {$tviti=" tvīti";}
   if($key != "LV"){
		echo "data.addRows([[ '".$key."', ".$value.",'".$value.$tviti."']]);";
		if ($value > $maxVal) $maxVal = $value;
	}
} 
	?>
		data.addRows([[ 'LV',<?php echo $maxVal*1.3;?>,'<?php echo "Daudz tvītu";?>']]);

        var options = {
		};

        var chart = new google.visualization.GeoChart(document.getElementById('fullregions_div'));

        chart.draw(data, options);
      }
    </script>
	
	
	
	
<div id="map_canvas"></div>
<br/>
<div id="regions_div"></div>
<br/>
<div id="fullregions_div"></div>
