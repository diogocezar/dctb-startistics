<?php
	include('./Config/config.php');
	include('./DataBase/database.php');
	date_default_timezone_set("America/Sao_paulo");
	$sql    = "SELECT DISTINCT artist from startistics ORDER BY artist ASC";
	$query  = $mysqli->query($sql);
	$artists  = array();
	while ($data = $query->fetch_array()) {
		$artists[] = $data['artist'];
	}
	$fields = array(
		'Facebook'          => 'facebook',
		'Twitter'           => 'twitter',
		'Instagram'         => 'instagram',
		'Youtube Inscritos' => 'youtube_sc',
		'Youtube Views'     => 'youtube_tuv'
	);

	$value_min_dt = date("Y-m-d", strtotime("-1 day"));
	$value_max_dt = date("Y-m-d");

	$have_data = false;

	if(!empty($_GET['artist'])){

		$have_data = true;
		$artist = $_GET['artist'];
		$field  = $_GET['field'];
		$min_dt = $_GET['min_dt'];
		$max_dt = $_GET['max_dt'];

		if(!empty($field) && !empty($min_dt) && !empty($max_dt) && !empty($artist)){

			$value_min_dt = $min_dt;
			$value_max_dt = $max_dt;

			$sql_sum = "SELECT (max(".$field.")-min(".$field.")) as diff FROM startistics WHERE date BETWEEN '$min_dt' AND '$max_dt' GROUP BY artist";
			$query_sum = $mysqli->query($sql_sum);
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
					$sql .= "date BETWEEN '$min_dt' ";
					$sql .= "AND '$max_dt' ";
					$sql .= "AND artist = '$artist' ";
					$sql .= "AND ".$field." > 0 ";
				$query = $mysqli->query($sql);
				$data = $query->fetch_array();

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
				if($data['index_artists'] > 1){
					$plot_index_artists = "<span style=\"color:blue\">$index_artists</span>";
				}
				else if($data['index_artists'] < 0.2){
					$plot_index_artists = "<span style=\"color:red\">$index_artists</span>";
				}
				else{
					$plot_index_artists = "<span style=\"color:green\">$index_artists</span>";
				}

				$sql_all_max  = "SELECT ";
				$sql_all_max .= "artist, ";
					$sql_all_max .= "min(".$field.") as min_value, ";
					$sql_all_max .= "max(".$field.") as max_value, ";
					$sql_all_max .= "(max(".$field.")-min(".$field.")) as diff, ";
					$sql_all_max .= "(((max(".$field.")-min(".$field.")) * 100) / max(".$field.")) as percent, ";
					$sql_all_max .= "((max(".$field.")-min(".$field."))*100) / $sum as index_artists ";
				$sql_all_max .= "FROM startistics ";
				$sql_all_max .= "WHERE ";
					$sql_all_max .= "date BETWEEN '$min_dt' ";
					$sql_all_max .= "AND '$max_dt' ";
					$sql_all_max .= "AND ".$field." > 0 ";
				$sql_all_max .= "GROUP BY artist ";
				$sql_all_max .= "ORDER BY index_artists DESC LIMIT 3";
				$query_all_max = $mysqli->query($sql_all_max);

				$sql_all_min  = "SELECT ";
				$sql_all_min .= "artist, ";
					$sql_all_min .= "min(".$field.") as min_value, ";
					$sql_all_min .= "max(".$field.") as max_value, ";
					$sql_all_min .= "(max(".$field.")-min(".$field.")) as diff, ";
					$sql_all_min .= "(((max(".$field.")-min(".$field.")) * 100) / max(".$field.")) as percent, ";
					$sql_all_min .= "((max(".$field.")-min(".$field."))*100) / $sum as index_artists ";
				$sql_all_min .= "FROM startistics ";
				$sql_all_min .= "WHERE ";
					$sql_all_min .= "date BETWEEN '$min_dt' ";
					$sql_all_min .= "AND '$max_dt' ";
					$sql_all_min .= "AND ".$field." > 0 ";
				$sql_all_min .= "GROUP BY artist ";
				$sql_all_min .= "ORDER BY index_artists ASC LIMIT 3";
				$query_all_min = $mysqli->query($sql_all_min);
			}
		}
	}
?>
<!doctype html>
<html lang="pt-BR">
    <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gerador de Análises</title>
    <link rel="stylesheet" href="Content/css/components/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="Content/css/public/style.css">
    <!--[if lt IE 9]>
        <script src="Content/js/components/bootstrap/ie-fix/ie8-responsive-file-warning.js"></script>
    <![endif]-->
    <script src="Content/js/components/bootstrap/ie-fix/ie-emulation-modes-warning.js"></script>
    <script src="Content/js/components/bootstrap/ie-fix/ie10-viewport-bug-workaround.js"></script>
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    </head>
    <body>

		<div class="navbar navbar-default navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<img class="navbar-logo" src="./Content/images/lorem.jpg" />
				</div>
			</div>
		</div>

        <section id="form-analytics">
            <div class="container">
                <div class="col-sm-12">
                    <div class="col-sm-offset-2 col-sm-10">
                    	<h2 class="text-center">Gerador de Análises</h2>
                    </div>
                    <form role="form" class="form-horizontal" action="analytics.php" method="_GET">
                        <div class="form-group">
                            <label for="artist" class="col-sm-2 control-label">Artista:</label>
                            <div class="col-sm-10">
                                <select id="artist" name="artist" class="form-control">
									<?php
										foreach ($artists as $value) {
											$value = mb_convert_encoding($value, 'utf-8', 'iso-8859-1');
											$selected = "";
											if($value == mb_convert_encoding($artist, 'utf-8', 'iso-8859-1')){
												$selected = " selected=\"selected\"";
											}
											echo "<option value=\"$value\"".$selected.">$value</option>";
										}
									?>
								</select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field" class="col-sm-2 control-label">Rede Social:</label>
                            <div class="col-sm-10">
								<select id="field" name="field" class="form-control">
									<?php
										foreach ($fields as $key => $value) {
											$selected = "";
											if($value == mb_convert_encoding($field, 'utf-8', 'iso-8859-1')){
												$selected = " selected=\"selected\"";
											}
											echo "<option value=\"$value\"".$selected.">$key</option>";
										}
									?>
								</select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field" class="col-sm-2 control-label">Data Início:</label>
                            <div class="col-sm-4">
                            	<input type="text" class="form-control" id="min_dt" name="min_dt" placeHolder="YYYY-MM-DD" value="<?= $value_min_dt ?>">
                            </div>
                            <label for="field" class="col-sm-2 control-label">Data Fim:</label>
                            <div class="col-sm-4">
                            	<input type="text" class="form-control" id="max_dt" name="max_dt" placeHolder="YYYY-MM-DD" value="<?= $value_max_dt ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button id="send-contact" class="btn btn-default center">Consultar Dados</button>
                            </div>
                        </div>
                        <hr/>
                    </form>
                </div>
            </div>
        </section>

		<section id="result-analytics">
            <div class="container">
                <?php
                	if($have_data){
                		?>
                		<h1 class="text-center">Período de análise: entre <?= $min_dt ?> e <?= $max_dt ?></h1>
                		<div class="row">
                			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                				<h2 class="text-center"><?= $artist ?> - <?= array_search($field, $fields) ?></h2>
                				<div class="data-index"><?= $plot_index_artists ?></div>
                				<div class="data-min-value">Valor Anterior: <?= $min_value ?></div>
                				<div class="data-max-value">Novo Valor: <?= $max_value ?></div>
                				<div class="data-numeric">Crescimento Numérico: <?= $diff ?></div>
            					<div class="data-percent">Crescimento Percentual: <?= $percent ?></div>
                			</div>
                		</div>

                		<hr/>

                		<div class="row">
                			<h2 class="text-center">Comparativos</h2>
                		</div>

                		<div class="row">
                			<?php 
                				while ($data = $query_all_max->fetch_array()){
                					$artist = utf8_encode($data['artist']);
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
									if($data['index_artists'] > 1){
										$plot_index_artists = "<span style=\"color:blue\">$index_artists</span>";
									}
									else if($data['index_artists'] < 0.2){
										$plot_index_artists = "<span style=\"color:red\">$index_artists</span>";
									}
									else{
										$plot_index_artists = "<span style=\"color:green\">$index_artists</span>";
									}
                			?>
                    			<div class="col-xs-12 col-sm-5 col-md-4 col-lg-4">
                					<h3 class="text-center"><?= $artist ?></h3>
                    				<div class="data-index"><?= $plot_index_artists ?></div>
                    				<div class="data-min-value">Valor Anterior: <?= $min_value ?></div>
                    				<div class="data-max-value">Novo Valor: <?= $max_value ?></div>
                    				<div class="data-numeric">Crescimento Numérico: <?= $diff ?></div>
                					<div class="data-percent">Crescimento Percentual: <?= $percent ?></div>
                    			</div>
                			<?php } ?>
                		</div>

                		<hr/>

                		<div class="row">
                			<?php 
                				while ($data = $query_all_min->fetch_array()){
                					$artist = utf8_encode($data['artist']);
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
									if($data['index_artists'] > 1){
										$plot_index_artists = "<span style=\"color:blue\">$index_artists</span>";
									}
									else if($data['index_artists'] < 0.2){
										$plot_index_artists = "<span style=\"color:red\">$index_artists</span>";
									}
									else{
										$plot_index_artists = "<span style=\"color:green\">$index_artists</span>";
									}
                			?>
                    			<div class="col-xs-12 col-sm-5 col-md-4 col-lg-4">
                					<h3 class="text-center"><?= $artist ?></h3>
                    				<div class="data-index"><?= $plot_index_artists ?></div>
                    				<div class="data-min-value">Valor Anterior: <?= $min_value ?></div>
                    				<div class="data-max-value">Novo Valor: <?= $max_value ?></div>
                    				<div class="data-numeric">Crescimento Numérico: <?= $diff ?></div>
                					<div class="data-percent">Crescimento Percentual: <?= $percent ?></div>
                    			</div>
                			<?php } ?>
                		</div>
                		<?php
                	}
                ?>
            </div>
        </section>

        <script src="Content/js/components/jquery/jquery-2.1.1.min.js"></script>
        <script src="Content/js/components/bootstrap/bootstrap.min.js"></script>
    </body>
</html>