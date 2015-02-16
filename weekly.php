<?php

	$debug = false;

	include('./Config/config.php');
	include('./DataBase/database.php');
	require('./Api/vendor/autoload.php');

	date_default_timezone_set("America/Sao_paulo");

	$today = date("Y-m-d");
	$last_week = date("Y-m-d", strtotime("-7 day"));
	$today_plot = date("d/m/Y");
	$last_week_plot = date("d/m/Y", strtotime("-7 day"));
	$fields = array(
		'facebook',
		'twitter',
		'instagram',
		'youtube_sc',
		'youtube_tuv'
	);
	$labels = array(
		'facebook'    => 'Facebook',
		'twitter'     => 'Twitter',
		'instagram'   => 'Instagram',
		'youtube_sc'  => 'Inscritos Youtube',
		'youtube_tuv' => 'Visualizações Youtube'
	);


	$html = "<html><head><meta charset=\"UTF-8\"></head><body>" . "\n";
	$html .= "<style>table{border-spacing: 5px;}td{text-align:center;}table, th, td {border: 1px solid black;border-collapse: collapse;}</style>" . "\n";
	$html .= "<h1>Lorem StArtistics - Relatório Semanal</h1>". "\n";
	$html .= "<h2>Crescimento entre ".$last_week_plot. " e ".$today_plot ."</h2>". "\n";

	foreach($fields as $field){
		$sql_sum = "SELECT (max(".$field.")-min(".$field.")) as diff FROM startistics WHERE date BETWEEN '$last_week' AND '$today' GROUP BY artist";
		$query_sum = $mysqli->query($sql_sum);
		$sum = 0;
		while ($data_sum = $query_sum->fetch_array()){
			$sum += $data_sum['diff'];
		}
		if(!empty($sum)){
			$sql  = "SELECT ";
			$sql .= "artist, ";
				$sql .= "min(".$field.") as min_value, ";
				$sql .= "max(".$field.") as max_value, ";
				$sql .= "(max(".$field.")-min(".$field.")) as diff, ";
				$sql .= "(((max(".$field.")-min(".$field.")) * 100) / max(".$field.")) as percent, ";
				$sql .= "((max(".$field.")-min(".$field."))*100) / $sum as index_artists ";
			$sql .= "FROM startistics ";
			$sql .= "WHERE ";
				$sql .= "date BETWEEN '$last_week' ";
				$sql .= "AND '$today' ";
			$sql .= "GROUP BY artist ";
			$sql .= "ORDER BY index_artists DESC";
			$query = $mysqli->query($sql);
			$html .= "<h3>".$labels[$field]."</h3>" . "\n";
			$html .= "<table style=\"width:100%\">" . "\n";
			$html .= "<tr>" . "\n";
				$html .= "<th>Artista</th>" . "\n";
				$html .= "<th>Índice</th>" . "\n";
				$html .= "<th>Valor Anterior</th>" . "\n";
				$html .= "<th>Novo Valor</th>" . "\n";
				$html .= "<th>Crescimento Numérico</th>" . "\n";
				$html .= "<th>Crescimento Percentual</th>" . "\n";
			$html .= "</tr>" . "\n";
			while ($data = $query->fetch_array()){
				if(!empty($data['min_value'])){
					$html .= "<tr>" . "\n";
						$html .= "<td>".utf8_encode($data['artist'])."</td>" . "\n";
						if(!empty($data['min_value']) && $data['min_value'] != "0")
							$min_value = number_format($data['min_value'],0,',','.');
						else
							$min_value = "-";
						if(!empty($data['max_value']) && $data['max_value'] != "0")
							$max_value = number_format($data['max_value'],0,',','.');
						else
							$max_value = "-";
						if(!empty($data['diff']) && $data['diff'] != "0")
							$diff = number_format($data['diff'],0,',','.');
						else
							$diff = "-";
						if(!empty($data['percent']) && $data['percent'] != "0")
							$percent = number_format($data['percent'],3,',','.') . "%";
						else
							$percent = "-";
						if(!empty($data['index_artists']) && $data['index_artists'] != "0")
							$index_artists = number_format($data['index_artists'],3,',','.');
						else
							$index_artists = "-";
						if($data['index_artists'] > 5){
							$plot_index_artists = "<span style=\"color:blue\">$index_artists</span>";
						}
						else if($data['index_artists'] < 1){
							$plot_index_artists = "<span style=\"color:red\">$index_artists</span>";
						}
						else{
							$plot_index_artists = "<span style=\"color:green\">$index_artists</span>";
						}
						$html .= "<td>".$plot_index_artists."</td>" . "\n";
						$html .= "<td>".$min_value."</td>" . "\n";
						$html .= "<td>".$max_value."</td>" . "\n";
						$html .= "<td>".$diff."</td>" . "\n";
						$html .= "<td>".$percent."</td>" . "\n";
					$html .= "</tr>" . "\n";
				}			
			}
			$html .= "</table>" . "\n";
			$html .= "<br/>" . "\n";
		}
		else{
			$html .= "<h4>Não foram encontrados dados no período especificado</h4>" . "\n";
		}
	}

	$html .= "</body></html>";

	if($debug) echo $html;

	try {
		$mandrill = new Mandrill(MANDRILL_KEY);
		$message  = array(
					'html' 		 => $html,
					'subject' 	 => 'Servidor Diogo - StArtistics - ' . date("d-m-Y H:i:s"),
					'from_email' => 'xgordo@gmail.com',
					'from_name'  => 'Servidor Diogo Cezar',
					'headers' 	 => array('Reply-To' => 'xgordo@gmail.com'),
					'to' 		 => array(
								 	   array(
								            'email' => 'diogo@lorem.cc',
								            'type' 	=> 'to'
								       ),
								 	   array(
								            'email' => 'xgordo@gmail.com',
								            'type' 	=> 'to'
								       )
								    )
					);

    	$async 	= false;
    	$result = $mandrill->messages->send($message, $async);

    	if($result[0]['status'] == 'sent' && $debug){
    		echo "Email enviado com sucesso";
    	}
	}
	catch(Mandrill_Error $e) {
		if($debug){
			echo "Não foi possível enviar o e-mail";
		}
	}
?>