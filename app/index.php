
<!DOCTYPE HTML>
<html lang="pt-BR">
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>NoPonto</title>

    <!-- CSS -->
    <link rel="stylesheet" href="css/master.css">
    <link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="plugins/jquery/jquery-ui.css">
    <link rel="stylesheet" href="plugins/jquery/jquery-ui.structure.css">
    <link rel="stylesheet" href="plugins/jquery/jquery-ui.theme.css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
</head>
<body>

  <header>
    <nav class="navbar navbar-default">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">
          <img src="images/noponto-logo2.png" class="logo" alt=""/>
          </a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
          <form class="navbar-form navbar-right" method="post" action="" id="" role="search">
            <div class="input-group">
              <input type="text" class="form-control" id="txtEndereco" name="txtEndereco" placeholder="Digite um local...">
              <span class="input-group-btn">
                <button class="btn btn-info" type="button" id="btnEndereco" name="btnEndereco" onclick="getAddress()">Ir</button>
              </span>
              <div class="current-location"></div>
              <span class="input-group-btn">
                <button type="button" id="search" class="btn btn-success" onclick="getCurrentLocation()">
                  <span class="glyphicon glyphicon-screenshot" aria-hidden="true"></span>
                </button>
              </span>
            </div><!-- /input-group -->
          </form>
          <ul class="nav navbar-nav navbar-left">
            <li><a href="#" data-toggle="modal" data-target="#noPontoSobre">Sobre</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Menu <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#" data-toggle="modal" data-target="#noPontoRotas">Rotas</a></li>
                <li><a href="#" data-toggle="modal" data-target="#noPontoLinhas">Linhas</a></li>
                <li class="divider"></li>
              </ul>
            </li>
          </ul>
        </div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>
  </header>


  <div class="pac-container" id="msgDiv"></div>
  <div class="mapas" id="mapa"></div>

  <!-- Modal Sobre -->
  <div class="modal fade" id="noPontoSobre" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Sobre</h4>
        </div>
        <div class="modal-body">
          <h2 class="text-center"><img src="images/noponto-logo-login.png" class="" alt=""/></h2>
          <h4 class="text-center">Sua plataforma para monitoramento de transporte coletivo</h4><br>
          <p class="text-center">
            <b>Projeto da disciplina de Projeto Integrador 1
              <br> Professor: Felipe Alencar
              <br> Equipe: Bruno Eris, Allan Denis, Hercilio Júnior, Ademar.
              <br>
              <br> Instituto Federal de Alagoas - 2015
              <br>
              <br> <img src="images/git.ico" alt=""><a href="https://github.com/AllanDenis/NoPonto" target="_blank"> Fork me on Github!</a>
            </b>
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Calc Rotas -->
  <div class="modal fade" id="noPontoRotas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog pac-container">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Rotas <span class="glyphicon glyphicon-road" aria-hidden="true"></span></h4>
        </div>

        <div class="modal-body">

          <div id="panel"></div>
          <br>
          <input type="search" class="form-control" id="start" placeholder="Insira o local do ponto A" required><br>
          <input type="search" class="form-control" id="end" placeholder="Insira o local do ponto B" required><br>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-info" onclick="calcRoute();" data-dismiss="modal">Ver Rota</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Linhas -->
  <div class="modal fade" id="noPontoLinhas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog pac-container">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Linhas <span class="glyphicon glyphicon-retweet" aria-hidden="true"></span></h4>
        </div>

        <div class="modal-body">

          <div id="panel"></div>
          <div class="form-group">
            <label for="sel1">Selecione a linha:</label>
            <select class="form-control" id="line">
              <option value="Shopping Miramar - Avenida Jucá Sampaio - Feitosa, Maceió - AL/Estacionamento Catedral - Centro, Maceió - AL">Feitosa / Centro - IDA</option>
              <option value="Shopping Miramar - Avenida Jucá Sampaio - Feitosa, Maceió - AL/Estacionamento Catedral - Centro, Maceió - AL">Feitosa / Centro - VOLTA</option>
              <option value="Terminal Rodoviário do Benedito Bentes - Benedito Bentes, Maceió - AL/Estacionamento Catedral - Centro, Maceió - AL">Benedito Bentes / Centro - IDA</option>
              <option value="Terminal Rodoviário do Benedito Bentes - Benedito Bentes, Maceió - AL/Estacionamento Catedral - Centro, Maceió - AL">Benedito Bentes / Centro - VOLTA</option>
              <option value="Av. Dr. Fernando do Couto Malta, Maceió - AL/Estacionamento Catedral - Centro, Maceió - AL">Graciliano Ramos / Centro - IDA</option>
              <option value="Av. Dr. Fernando do Couto Malta, Maceió - AL/Estacionamento Catedral - Centro, Maceió - AL">Graciliano Ramos / Centro - VOLTA</option>
            </select>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-info" onclick="calcLines();" data-dismiss="modal">Ver Linha</button>
        </div>
      </div>
    </div>
  </div>


  <!-- script -->
  <!--<script src="plugins/jquery/jquery-1.11.3.min.js"></script>-->
  <script src="plugins/jquery/jquery-2.1.4.js"></script>
  <script src="plugins/jquery/jquery-ui.custom.min.js"></script>
  <script src="js/react.js"></script>
  <script src="js/JSXTransformer.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places"></script>
  <script src="plugins/bootstrap/js/bootstrap.min.js"></script>
  <script src="js/application.js"></script>
</body>
</html>