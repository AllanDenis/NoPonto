<!DOCTYPE HTML>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>NoPonto - Login</title>

    <!-- CSS -->
    <link rel="stylesheet" href="css/master.css">
    <link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.css">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
</head>
<body>

  <div class="container width-login">
        <div class="row">
             <div class="col-md-12">
               <div class="row"> 
                    <h1 class="text-center"><img src="images/noponto-logo-login.png" alt=""></h1>
               </div>
                
                <form class="form-signin text-center text-shadow" action="" role="form" method="post">                    
                    <input type="text" name="login" class="form-control" placeholder="Usuário" required autofocus>
                    <input type="password" name="password" class="form-control" placeholder="Senha" required>
                    <button class="btn btn-lg btn-primary btn-block text-shadow" type="submit">Entrar</button>
                    <a class="btn btn-lg btn-link text-shadow" href="#">Cadastre-se</a>
                </form>
            </div>
        </div>  
    </div>  
      
    <video autoplay loop poster="images/bg.png" id="bgvid">
    <!-- <source src="images/videobg.mp4" type="video/mp4"> -->
    <source src="" type="video/mp4">
    </video>

  <!-- script -->
  <script src="plugins/jquery/jquery-1.11.3.min.js"></script>
  <script src="plugins/bootstrap/js/bootstrap.min.js"></script>
  <script src="js/application.js" type="text/jsx" ></script>
</body>
</html>