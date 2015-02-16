<?php
	include('./Config/config.php');
	include('./DataBase/database.php');

	require './Api/vendor/autoload.php';

	date_default_timezone_set("America/Sao_paulo");

	/* Defaults */

	$height  = 800;
	$widht   = 1700;
	$limit   = 100;
	$artist  = "thaemeethiago";
	$field   = "twitter";
	$type    = "jpg";
	$x_title = "Data e Hora";
	$y_title = "Acessos";
	$line_wi = 2;
	$y_inc   = 500;
	$group   = 6;
	$orient  = "ASC";
	$have_l  = "true";
	//$min_dt  = "2014-09-01";
	//$max_dt  = "2014-09-10";

	/* Gets */

	if(!empty($_GET['height']))  $height  = $_GET['height'];
	if(!empty($_GET['width']))   $width   = $_GET['width'];
	if(!empty($_GET['artist']))  $artist  = mb_convert_encoding($_GET['artist'], 'iso-8859-1', 'utf-8');
	if(!empty($_GET['field']))   $field   = $_GET['field'];
	if(!empty($_GET['limit']))   $limit   = $_GET['limit'];
	if(!empty($_GET['type']))    $type    = $_GET['type'];
	if(!empty($_GET['x_title'])) $x_title = $_GET['x_title'];
	if(!empty($_GET['y_title'])) $y_title = $_GET['y_title'];
	if(!empty($_GET['line_wi'])) $line_wi = $_GET['line_wi'];
	if(!empty($_GET['y_inc']))   $y_inc   = $_GET['y_inc'];
	if(!empty($_GET['group']))   $group   = $_GET['group'];
	if(!empty($_GET['orient']))  $orient  = $_GET['orient'];
	if(!empty($_GET['have_l']))  $have_l  = $_GET['have_l'];
	if(!empty($_GET['min_dt']))  $min_dt  = $_GET['min_dt'];
	if(!empty($_GET['max_dt']))  $max_dt  = $_GET['max_dt'];
	if(!empty($_GET['title']))   $title   = $_GET['title'];

	$title   = strtoupper($artist) . " - " . strtoupper($field);

	$plot = new PHPlot($widht, $height);

	$plot->SetFileFormat($type);

	$plot->SetTitle($title);
	$plot->SetXTitle($x_title);
	$plot->SetYTitle($y_title);
	$sql    = "SELECT " . $field . ", date FROM startistics where artist = '" . $artist . "'";
	if(!empty($min_dt)){
		$sql .= " and date >= '" . $min_dt . "'";
	}
	if(!empty($max_dt)){
		$sql .= " and date <= '" . $max_dt . "'";
	}
	$sql .= "order by date " . $orient;
	if($have_l == "true")
	 	$sql .= " LIMIT " . $limit;
	$query  = $mysqli->query($sql);
	$data_plot  = array();
	$i = 0;
	$avg_group = array();
	while ($data = $query->fetch_array()) {
		if($data[$field] != 0){
			if(!empty($group)){
				if($i < $group){
					$avg_group[] = $data[$field];
					$date = $data['date'];
					$i++;
				}
				else{
					$i=0;
					$avg = array_sum($avg_group) / count($avg_group);
					$data_plot[] = array($date, $avg);
				}
			}
			else{
				$data_plot[] = array($data['date'], $data[$field]);
			}
		}
	}

	$plot->SetLineWidth($line_wi);
	$plot->SetYTickIncrement($y_inc);
	$plot->SetDataValues($data_plot);
	$plot->SetXDataLabelAngle(90);

	//$plot->SetPlotAreaWorld(NULL, 0, NULL, NULL);
	//$plot->TuneXAutoRange(NULL, NULL, 10);
	//$plot->SetYLabelType('data');
	//$plot->SetPlotType('lines');
	//$plot->TuneYAutoRange(1, 'I', 1);
	//$plot->SetPlotAreaWorld(10, NULL, 100);
	//$plot->SetYScaleType('linear');
	//$plot->SetNumYTicks(1);
	//$plot->TuneYAutoRange(0);

	$plot->DrawGraph();
?>