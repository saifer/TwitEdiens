<?php
include "includes/laiks.php";
?>
<h2 style='margin:auto auto; text-align:center;'>Ēšanas kalendārs</h2>
<h5 style='margin:auto auto; text-align:center;'>
<form method="post" action="?id=dienas">
No <input value="<?php echo $nn;?>" readonly size=9 type="text" id="from" name="from"/> līdz <input value="<?php echo $ll;?>" readonly size=9 type="text" id="to" name="to"/>
<INPUT TYPE="submit" name="submit" value="Parādīt"/>
</form>
</h5>
<br/>
<h3>Cikos tvīto visbiežāk</h3>
<?php

//jādabū visas dienas Mon-Sun...
//šitā ir pirmdiena...sāksim ar to
$theDate = '2011-10-31';
$timeStamp = StrToTime($theDate);
for($zb=0;$zb<7;$zb++) {
	$ddd = date('D', $timeStamp); 
	$timeStamp = StrToTime('+1 days', $timeStamp);
	$dienas[$ddd]['skaits'] = 0;
}

$max=0;
$maxd=0;
$maxdat=0;
for($zb=0;$zb<24;$zb++) $stundas[$zb]['skaits']=0;

$q = mysqli_query($connection, "SELECT created_at FROM `tweets` WHERE created_at between '$no' AND '$lidz' ORDER BY created_at asc");
while($r=mysqli_fetch_array($q)){
	$laiks=$r["created_at"];
	$laiks=strtotime($laiks);
	$diena=date("D", $laiks);
	$dmg=date("d.m.Y", $laiks);
	$laiks=date("G", $laiks);
	$dienas[$diena]['skaits']++;
	$stundas[$laiks]['skaits']++;
	$dmgs[$dmg]['skaits']++;
	if($stundas[$laiks]['skaits']>$max) $max=$stundas[$laiks]['skaits'];
	if($dienas[$diena]['skaits']>$maxd) $maxd=$dienas[$diena]['skaits'];
}


?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart2);
  $(window).resize(drawChart2);
	var chart2;
  function drawChart2() {	
		var data2 = new google.visualization.DataTable();
		data2.addColumn('string', 'Stunda');
		data2.addColumn('number', 'Tvīti');
		data2.addRows(24);
		<?php
		//izdrukā populārākās stundas
		for($zb=0;$zb<24;$zb++) {
			echo "data2.setValue(".$zb.", 0, '".$zb.":00-".($zb+1).":00');";
			echo "data2.setValue(".$zb.", 1, ".$stundas[$zb]['skaits'].");";
		}
		?>
		chart2 = new google.visualization.ColumnChart(document.getElementById('stats-hours'));
		chart2.draw(data2, {'backgroundColor':'transparent', hAxis: {slantedText:true, slantedTextAngle:45 }});
	  google.visualization.events.addListener(chart2, 'select', selectHandler1);
  }
	function selectHandler1() {
	  var selection2 = chart2.getSelection();
	  var item2 = selection2[0];
	  if (item2.row == 0){
	  	setTimeout("window.location = 'grupa/0'",1);
	  }else if (item2.row == 1){
	  	setTimeout("window.location = 'grupa/1'",1);
	  }else if (item2.row == 2){
	  	setTimeout("window.location = 'grupa/2'",1);
	  }else if (item2.row == 3){
	  	setTimeout("window.location = 'grupa/3'",1);
	  }else if (item2.row == 4){
	  	setTimeout("window.location = 'grupa/4'",1);
	  }else if (item2.row == 5){
	  	setTimeout("window.location = 'grupa/5'",1);
	  }else if (item2.row == 6){
	  	setTimeout("window.location = 'grupa/6'",1);
	  }else if (item2.row == 7){
	  	setTimeout("window.location = 'grupa/7'",1);
	  }else if (item2.row == 8){
	  	setTimeout("window.location = 'grupa/8'",1);
	  }else if (item2.row == 9){
	  	setTimeout("window.location = 'grupa/9'",1);
	  }else if (item2.row == 10){
	  	setTimeout("window.location = 'grupa/10'",1);
	  }else if (item2.row == 11){
	  	setTimeout("window.location = 'grupa/11'",1);
	  }else if (item2.row == 12){
	  	setTimeout("window.location = 'grupa/12'",1);
	  }else if (item2.row == 13){
	  	setTimeout("window.location = 'grupa/13'",1);
	  }else if (item2.row == 14){
	  	setTimeout("window.location = 'grupa/14'",1);
	  }else if (item2.row == 15){
	  	setTimeout("window.location = 'grupa/15'",1);
	  }else if (item2.row == 16){
	  	setTimeout("window.location = 'grupa/16'",1);
	  }else if (item2.row == 17){
	  	setTimeout("window.location = 'grupa/17'",1);
	  }else if (item2.row == 18){
	  	setTimeout("window.location = 'grupa/18'",1);
	  }else if (item2.row == 19){
	  	setTimeout("window.location = 'grupa/19'",1);
	  }else if (item2.row == 20){
	  	setTimeout("window.location = 'grupa/20'",1);
	  }else if (item2.row == 21){
	  	setTimeout("window.location = 'grupa/21'",1);
	  }else if (item2.row == 22){
	  	setTimeout("window.location = 'grupa/22'",1);
	  }else if (item2.row == 23){
	  	setTimeout("window.location = 'grupa/23'",1);
	  }
	}
</script>
	<div id="stats-hours"></div>
<br/>
<h3>Kuros datumos tvīto visbiežāk</h3>
<!-- datumi -->
<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart1);
  $(window).resize(drawChart1);
  function drawChart1() {	
	var data3 = new google.visualization.DataTable();
	data3.addColumn('string', 'Datums');
	data3.addColumn('number', 'Tvīti');
	data3.addRows(<?php echo count($dmgs);?>);
<?php
//izdrukā populārākās stundas
$i=0;
foreach($dmgs as $key => $dmg) {
	echo "data3.setValue(".$i.", 0, '".$key."');";
	echo "data3.setValue(".$i.", 1, ".$dmg['skaits'].");";
	$i++;
}
?>
	var chart3 = new google.visualization.ColumnChart(document.getElementById('stats-dates'));
	chart3.draw(data3, {'backgroundColor':'transparent', vAxis: {viewWindow: {min: 0} }});
  }
</script>
	<div id="stats-dates"></div>
<br/>
<h3>Kurās dienās tvīto visbiežāk</h3>
<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart);
  $(window).resize(drawChart);
  var chart;
  function drawChart() {
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Diena');
		data.addColumn('number', 'Tvīti');
		data.addRows(7);			
		data.setValue(6, 0, "Svētdiena");data.setValue(6, 1, <?php echo $dienas['Sun']['skaits'];?>);
		data.setValue(0, 0, "Pirmdiena");data.setValue(0, 1, <?php echo $dienas['Mon']['skaits'];?>);
		data.setValue(1, 0, "Otrdiena");data.setValue(1, 1, <?php echo $dienas['Tue']['skaits'];?>);
		data.setValue(2, 0, "Trešdiena");data.setValue(2, 1, <?php echo $dienas['Wed']['skaits'];?>);
		data.setValue(3, 0, "Ceturtdiena");data.setValue(3, 1, <?php echo $dienas['Thu']['skaits'];?>);
		data.setValue(4, 0, "Piektdiena");data.setValue(4, 1, <?php echo $dienas['Fri']['skaits'];?>);
		data.setValue(5, 0, "Sestdiena");data.setValue(5, 1, <?php echo $dienas['Sat']['skaits'];?>);
		chart = new google.visualization.ColumnChart(document.getElementById('stats-wdays'));
		chart.draw(data, {'backgroundColor':'transparent', vAxis: {viewWindow: {min: 0} }});
	  google.visualization.events.addListener(chart, 'select', selectHandler);
	}
	function selectHandler() {
	  var selection = chart.getSelection();
	  var item = selection[0];
	  if (item.row == 0){
	  	setTimeout("window.location = 'grupa/Mon'",1);
	  }else if (item.row == 1){
	  	setTimeout("window.location = 'grupa/Tue'",1);
	  }else if (item.row == 2){
	  	setTimeout("window.location = 'grupa/Wed'",1);
	  }else if (item.row == 3){
	  	setTimeout("window.location = 'grupa/Thu'",1);
	  }else if (item.row == 4){
	  	setTimeout("window.location = 'grupa/Fri'",1);
	  }else if (item.row == 5){
	  	setTimeout("window.location = 'grupa/Sat'",1);
	  }else if (item.row == 6){
	  	setTimeout("window.location = 'grupa/Sun'",1);
	  }
	}
</script>
	<div id="stats-wdays"></div>
	
<br/>
<h3>Kuros mēnešos tvīto visbiežāk</h3>
	

<?php

include "includes/init_sql.php";
	
for ($month=date('m');$month>0;$month--){
	$year = (date('Y'));
	if($month==12) {
		$next_month = 1;
		$next_year = (date('Y')+1);
	}else{
		$next_month = $month+1;
		$next_year = $year;
	}
	
	if($month < 10 && $month[0]!="0") $month = "0".$month;
	if($next_month < 10 && $month[0]!="0") $next_month = "0".$next_month;
	
	$query .= "SELECT '".$year.".".$month."' AS 'datums', COUNT(*) AS 'skaits' FROM `tweets` WHERE `created_at` BETWEEN '".$year."-".$month."-01 00:00:00.000000' AND '".$next_year."-".$next_month."-01 00:00:00.000000' UNION ";
}
for ($month=12;$month>0;$month--){
	$year = (date('Y')-1);
	if($month==12) {
		$next_month = 1;
		$next_year = date('Y');
	}else{
		$next_month = $month+1;
		$next_year = $year;
	}
	if($month < 10) $month = "0".$month;
	if($next_month < 10) $next_month = "0".$next_month;
	
	$query .= "SELECT '".$year.".".$month."' AS 'datums', COUNT(*) AS 'skaits' FROM `tweets` WHERE `created_at` BETWEEN '".$year."-".$month."-01 00:00:00.000000' AND '".$next_year."-".$next_month."-01 00:00:00.000000' ";
	if($month > 1) $query .= "UNION ";
}

$menesi = mysqli_query($connection, $query);
$menesis = date('m');
while($p=mysqli_fetch_array($menesi)){
	$menesi_skaiti[$p["datums"]] = $p["skaits"];
}

?>
<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart1);
  $(window).resize(drawChart1);
  function drawChart1() {	
	var data4 = new google.visualization.DataTable();
	data4.addColumn('string', 'Mēnesis');
	data4.addColumn('number', 'Tvīti');
	data4.addRows(<?php echo count($menesi_skaiti);?>);
<?php
//izdrukā populārākās stundas
$i=0;
foreach(array_reverse($menesi_skaiti) as $key => $dmg) {
	echo "data4.setValue(".$i.", 0, '".$key."');";
	echo "data4.setValue(".$i.", 1, ".$dmg.");";
	$i++;
}
?>
	var chart4 = new google.visualization.ColumnChart(document.getElementById('stats-months'));
	chart4.draw(data4, {'backgroundColor':'transparent', vAxis: {viewWindow: {min: 0, max: 15000} } });
  }
</script>
	<div id="stats-months"></div>
<br/>