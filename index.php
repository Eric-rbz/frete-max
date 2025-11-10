<?php
session_start();


$destino = 'login.php';


if (isset($_SESSION['usuario']) && isset($_GET['redirect']) && $_GET['redirect'] === 'dashboard') {
    $destino = 'dashboard.php';
}

elseif (isset($_SESSION['usuario'])) {
    $destino = 'dashboard.php';
}


header("refresh:3;url=$destino");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Carregando...</title>
  <style>
    body {
      margin: 0; padding: 0;
      height: 100vh;
      display: flex; justify-content: center; align-items: center;
      font-family: 'Poppins', sans-serif;
      background-color: black;
      overflow: hidden;
    }
    #loading {
      display: flex; flex-direction: column;
      align-items: center; color: white;
      animation: fadeIn 1s ease-in;
    }
    .logo-loading {
      width: 180px; height: auto;
      animation: girar 2.5s linear infinite;
      filter: drop-shadow(0 0 15px rgba(255,255,255,0.5));
    }
    #loading p {
      margin-top: 20px; font-size: 1.2em; letter-spacing: 1px;
      animation: piscar 1.5s infinite;
    }
    @keyframes girar { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    @keyframes piscar { 0%,100% { opacity: 1; } 50% { opacity: 0.4; } }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
  </style>
</head>
<body>
  <div id="loading">
    <img src="src/camiao.jpg" alt="Logo" class="logo-loading">
    <p>Carregando...</p>
  </div>
</body>
</html>
