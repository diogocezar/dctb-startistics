<?php
	include('./Config/config.php');
	include('./Data/data.php');
	include('./DataBase/database.php');
	include('startistics.php');

	error_reporting(E_ERROR | E_PARSE);

	set_time_limit(0);

	date_default_timezone_set("America/Sao_paulo");

	$s = new Startistics();

	$debug 	    = true;
	$log_file   = false;
	$sql_log    = "";
	$log        = "";
	$start_time = "START -> [" . date("d-m-Y H:i:s") . "]" . "\n";

	if($debug) echo "<pre>";

	foreach($artists as $key => $artist){
		$facebook  = $artist['facebook'];
		$twitter   = $artist['twitter'];
		$instagram = $artist['instagram'];
		$youtube   = $artist['youtube'];

		$count_facebook    = "";
		$count_twitter     = "";
		$count_instagram   = "";
		$count_youtube_sc  = "";
		$count_youtube_tuv = "";

		$artist_name = utf8_decode($key);

		if(!empty($facebook)){
			$infos_facebook  = $s->get_facebook($facebook);
			$count_facebook  = $infos_facebook['likes'];
		}

		if(!empty($twitter)){
			$infos_twitter   = $s->get_twitter($twitter);
			$count_twitter   = $infos_twitter->followers_count;
		}

		if(!empty($instagram)){
			$infos_instagram = $s->get_instagram($instagram);
			$count_instagram = $infos_instagram->data->counts->followed_by;
		}

		if(!empty($youtube)){
			$infos_youtube     = $s->get_youtube($youtube);
			$count_youtube_sc  = $infos_youtube->entry->ytstatistics->subscriberCount;
			$count_youtube_tuv = $infos_youtube->entry->ytstatistics->totalUploadViews;
		}

		if(empty($count_facebook))
			$count_facebook = "''";
		if(empty($count_twitter))
			$count_twitter = "''";
		if(empty($count_instagram))
			$count_instagram = "''";
		if(empty($count_youtube_sc))
			$count_youtube_sc = "''";
		if(empty($count_youtube_tuv))
			$count_youtube_tuv = "''";

		$sql = "INSERT INTO startistics (artist, facebook, twitter, instagram, youtube_sc, youtube_tuv, date) VALUES ('".$artist_name."', ".$count_facebook.", ".$count_twitter.", ".$count_instagram.", ".$count_youtube_sc.", ".$count_youtube_tuv.", '".date('Y-m-d H:i:s')."')";
		$mysqli->query($sql);

		if($debug) echo "(". date("d-m-Y H:i:s") . ")" . "[INSER_INTO] " . $sql . "\n";

		$sql_log .= "\t" . "(". date("d-m-Y H:i:s") . ") " . $sql . "\n";

		sleep(5);
	}
	$log .= $start_time;
	$log .= "END   -> [" . date("d-m-Y H:i:s") . "]" . "\n";
	$log .= "\t". "[SQL]" . "\n";
	$log .= $sql_log;

	if($debug) echo "(". date("d-m-Y H:i:s") . ")" . "[GRAVANDO LOG]" . "\n";

	if($log_file){
		$filename = "./Log/log.txt";
		$file = fopen($filename, "a+");
		fwrite($file, $log); 
		fclose($file); 
	}

	if($debug) "</pre>";
?>