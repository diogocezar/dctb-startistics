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
?>
<!doctype html>
<html lang="pt-BR">
    <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gerador de Gráficos</title>
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

        <section id="form-graphs">
            <div class="container">
                <div class="col-sm-12">
                    <div class="col-sm-offset-2 col-sm-10">
                    	<h2 class="text-center">Gerador de Gráficos de Estatísticas</h2>
                    </div>
                    <form role="form" class="form-horizontal" action="plot.php" method="GET">
                        <div class="form-group">
                            <label for="artist" class="col-sm-2 control-label">Artista:</label>
                            <div class="col-sm-10">
                                <select id="artist" name="artist" class="form-control">
									<?php
										foreach ($artists as $value) {
											$value = mb_convert_encoding($value, 'utf-8', 'iso-8859-1');
											echo "<option value=\"$value\">$value</option>";
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
											echo "<option value=\"$value\">$key</option>";
										}
									?>
								</select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field" class="col-sm-2 control-label">Altura:</label>
                            <div class="col-sm-4">
                            	<input type="text" class="form-control" id="height" name="height" value="800">
                            </div>
                            <label for="field" class="col-sm-2 control-label">Largura:</label>
                            <div class="col-sm-4">
                            	<input type="text" class="form-control" id="width" name="width" value="1700">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field" class="col-sm-2 control-label">Limite:</label>
                            <div class="col-sm-10">
                            	<input type="text" class="form-control" id="limit" name="limit" value="100">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field" class="col-sm-2 control-label">Extensão:</label>
                            <div class="col-sm-10">
								<select id="type" name="type" class="form-control">
									<option value="jpg" selected="selected">JPG</option>
									<option value="png">PNG</option>
								</select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field" class="col-sm-2 control-label">Título Eixo X:</label>
                            <div class="col-sm-4">
                            	<input type="text" class="form-control" id="x_title" name="x_title" value="Data e Hora">
                            </div>
                            <label for="field" class="col-sm-2 control-label">Título Eixo Y:</label>
                            <div class="col-sm-4">
                            	<input type="text" class="form-control" id="y_title" name="y_title" value="Acessos">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field" class="col-sm-2 control-label">Expessura da Linha:</label>
                            <div class="col-sm-10">
								<select id="line_wi" name="line_wi" class="form-control">
									<option value="1">1</option>
									<option value="2" selected="selected">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="4">5</option>
								</select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field" class="col-sm-2 control-label">Incremento Exito Y:</label>
                            <div class="col-sm-4">
                            	<input type="text" class="form-control" id="y_inc" name="y_inc" value="500">
                            </div>
                            <label for="field" class="col-sm-2 control-label">Orientação Data:</label>
                            <div class="col-sm-4">
                            	<select id="orient" name="orient" class="form-control">
									<option value="ASC" selected="selected">Crescente</option>
									<option value="DESC">Decrescente</option>
								</select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field" class="col-sm-2 control-label">Agrupar Registros:</label>
                            <div class="col-sm-10">
								<select id="group" name="group" class="form-control">
									<option value="3">3h</option>
									<option value="6" selected="selected">6h</option>
									<option value="12">12h</option>
									<option value="24">24h</option>
								</select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field" class="col-sm-2 control-label">Data Início:</label>
                            <div class="col-sm-4">
                            	<input type="text" class="form-control" id="x_title" name="x_title" placeHolder="YYYY-MM-DD">
                            </div>
                            <label for="field" class="col-sm-2 control-label">Data Fim:</label>
                            <div class="col-sm-4">
                            	<input type="text" class="form-control" id="y_title" name="y_title" placeHolder="YYYY-MM-DD">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button id="send-contact" class="btn btn-default center">Gerar Gráfico</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <script src="Content/js/components/jquery/jquery-2.1.1.min.js"></script>
        <script src="Content/js/components/bootstrap/bootstrap.min.js"></script>
    </body>
</html>