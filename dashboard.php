<?php
session_start();

if (!isset($_SESSION['logo_vista'])) {
    $_SESSION['logo_vista'] = true;
    header("Location: index.php?redirect=dashboard");
    exit;
}




if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}
$usuario = htmlspecialchars($_SESSION['usuario']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fretemax - Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Uber+Move:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Uber Move', sans-serif; background: #f5f5f5; color: #000; overflow-x: hidden; }

        .header { background: white; padding: 15px 20px; display: flex; align-items: center; box-shadow: 0 1px 5px rgba(0,0,0,0.1); }
        .menu-toggle { font-size: 1.5rem; cursor: pointer; margin-right: 15px; }
        .logo { font-weight: 700; font-size: 1.4rem; }

        .sidebar { position: fixed; top: 0; left: -320px; width: 320px; height: 100%; background: white; box-shadow: 2px 0 15px rgba(0,0,0,0.1); transition: 0.3s; z-index: 100; overflow-y: auto; }
        .sidebar.active { left: 0; }
        .profile { padding: 20px; display: flex; align-items: center; border-bottom: 1px solid #eee; }
        .profile-img { width: 60px; height: 60px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.4rem; margin-right: 12px; }
        .rating { display: flex; align-items: center; font-size: 0.95rem; color: #666; margin-top: 4px; }
        .stars { color: #ffd700; margin-right: 5px; }

        .menu-item { padding: 16px 20px; display: flex; align-items: center; cursor: pointer; border-bottom: 1px solid #f0f0f0; }
        .menu-item:hover { background: #f9f9f9; }
        .menu-item i { width: 28px; font-size: 1.2rem; color: #000; }
        .become-driver { background: #000; color: white; margin: 15px 20px; border-radius: 12px; font-weight: 600; }
        .become-driver i { color: white; }

        .logout { padding: 20px; color: #d32f2f; font-weight: 600; text-align: center; cursor: pointer; }

        .overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 99; display: none; }
        .overlay.active { display: block; }

        .main-content { padding: 20px; height: calc(100vh - 70px); overflow-y: auto; }
        .welcome { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center; margin-bottom: 20px; }
        .btn-large { background: #000; color: white; border: none; padding: 16px; border-radius: 12px; font-size: 1.1rem; font-weight: bold; width: 100%; cursor: pointer; margin-top: 15px; }

        .freight-screen { display: none; height: 100%; background: white; }
        .freight-header { background: #000; color: white; padding: 20px; text-align: center; position: relative; }
        .back-btn { position: absolute; left: 15px; top: 18px; font-size: 1.5rem; cursor: pointer; }
        .address-inputs { padding: 20px; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .input-group input { width: 100%; padding: 14px; border: 1px solid #ddd; border-radius: 12px; font-size: 1rem; }
        .input-group input:focus { outline: none; border-color: #000; }
        .confirm-btn { background: #000; color: white; border: none; padding: 16px; border-radius: 12px; font-size: 1.1rem; font-weight: bold; width: 100%; cursor: pointer; margin-top: 20px; }

        #map { height: 300px; border-radius: 12px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }

        .driver-form { display: none; padding: 20px; background: white; height: 100%; overflow-y: auto; }
        .form-step { display: none; }
        .form-step.active { display: block; animation: fadeIn 0.4s; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .step-indicator { display: flex; justify-content: center; margin-bottom: 20px; }
        .step { width: 30px; height: 30px; background: #ddd; color: #666; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; margin: 0 5px; }
        .step.active { background: #000; color: white; }
        .next-btn, .prev-btn { background: #000; color: white; border: none; padding: 12px 20px; border-radius: 50px; cursor: pointer; margin-top: 20px; }
        .prev-btn { background: #eee; color: #000; float: left; }
        .next-btn { float: right; }

        @media (max-width: 480px) {
            .sidebar { width: 280px; }
            .header { padding: 12px 15px; }
        }
    </style>
</head>
<body>
<div class="header">
    <div class="menu-toggle" onclick="toggleSidebar()">☰</div>
    <div class="logo">Fretemax</div>
</div>

<div class="sidebar" id="sidebar">
    <div class="profile">
        <div class="profile-img"><?= strtoupper(substr($usuario, 0, 2)) ?></div>
        <div>
            <h3><?= $usuario ?></h3>
            <div class="rating"><span class="stars">★★★★★</span> 4.9</div>
        </div>
    </div>
    <div class="menu-item" onclick="showHome()"><i class="fas fa-home"></i> Início</div>
    <div class="menu-item" onclick="showFreightRequest()"><i class="fas fa-truck"></i> Solicitar Frete</div>
    <div class="menu-item"><i class="fas fa-history"></i> Histórico</div>
    <div class="menu-item"><i class="fas fa-credit-card"></i> Pagamentos</div>
    <div class="menu-item become-driver" onclick="showDriverForm()"><i class="fas fa-user-plus"></i> Tornar-se Motorista</div>
    <div class="logout" onclick="window.location='logout.php'">Sair</div>
</div>

<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

<!--front -->
<div class="main-content" id="mainContent">
    <div class="welcome">
        <h2>Olá, <?= $usuario ?>!</h2>
        <p>Precisa transportar algo hoje?</p>
        <button class="btn-large" onclick="showFreightRequest()">Solicitar Frete</button>
    </div>
</div>

<!-- frete -->
<div class="freight-screen" id="freightScreen">
    <div class="freight-header">
        <div class="back-btn" onclick="showHome()">←</div>
        <h2>Solicitar Frete</h2>
    </div>
    <div class="address-inputs">
        <div class="input-group">
            <label>Origem (A)</label>
            <input type="text" id="fromAddress" placeholder="Rua, número, cidade">
        </div>
        <div class="input-group">
            <label>Destino (B)</label>
            <input type="text" id="toAddress" placeholder="Rua, número, cidade">
        </div>
        <div id="map"></div>
        <button class="confirm-btn" onclick="searchDrivers()">Buscar Motoristas</button>
    </div>
</div>

<!-- form motoristas -->
<div class="driver-form" id="driverForm">
    <div class="freight-header">
        <div class="back-btn" onclick="showHome()">←</div>
        <h2>Cadastre-se como Motorista</h2>
    </div>

    <form id="formMotorista" style="padding:20px;" enctype="multipart/form-data">
        <div class="step-indicator">
            <div class="step active" id="step1">1</div>
            <div class="step" id="step2">2</div>
            <div class="step" id="step3">3</div>
        </div>

        <!-- PASSO 1 -->
        <div class="form-step active" id="formStep1">
            <h3>Seus Dados</h3>
            <div class="input-group"><input type="text" name="nome" placeholder="Nome completo" required></div>
            <div class="input-group"><input type="tel" name="telefone" placeholder="Telefone" required></div>
            <div class="input-group"><input type="text" name="cnh" placeholder="CNH" required></div>
            <div class="input-group"><input type="text" name="categoria" placeholder="Categoria (B, C, D...)" required></div>

            <!-- email vem da sessão, mas deixei se quiser mudar -->
            <input type="hidden" name="email" value="<?= $usuario ?>">

            <button class="next-btn" type="button" onclick="nextStep(1)">Próximo</button>
        </div>

        <!-- PASSO 2 -->
        <div class="form-step" id="formStep2">
            <h3>Veículo</h3>
            <div class="input-group"><input type="text" name="modelo" placeholder="Modelo do veículo" required></div>
            <div class="input-group"><input type="text" name="placa" placeholder="Placa" required></div>

            <button class="prev-btn" type="button" onclick="prevStep(2)">Voltar</button>
            <button class="next-btn" type="button" onclick="nextStep(2)">Próximo</button>
        </div>

        <!-- PASSO 3 -->
        <div class="form-step" id="formStep3">
            <h3>Documentos</h3>
            <p><input type="file" name="doc_cnh" accept="image/*" required> CNH (frente)</p>
            <p><input type="file" name="doc_crlv" accept="image/*" required> CRLV do veículo</p>
            <p><input type="file" name="doc_comprovante" accept="image/*" required> Comprovante de residência</p>

            <button class="prev-btn" type="button" onclick="prevStep(3)">Voltar</button>
            <button class="next-btn" type="button" onclick="enviarMotorista()">Enviar</button>
        </div>
    </form>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=SUA_CHAVE_AQUI&libraries=places"></script>
<script>
let map;
function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: -23.5505, lng: -46.6333 },
        zoom: 12
    });
}

function searchDrivers() {
    const from = document.getElementById('fromAddress').value;
    const to = document.getElementById('toAddress').value;
    if (!from || !to) return alert('Preencha origem e destino!');
    alert(`Buscando motoristas para:\n${from} → ${to}`);
    initMap();
}

function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
    document.getElementById('overlay').classList.toggle('active');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('active');
    document.getElementById('overlay').classList.remove('active');
}

function showHome() {
    closeSidebar();
    document.getElementById('mainContent').style.display = 'block';
    document.getElementById('freightScreen').style.display = 'none';
    document.getElementById('driverForm').style.display = 'none';
}
function showFreightRequest() {
    closeSidebar();
    document.getElementById('mainContent').style.display = 'none';
    document.getElementById('freightScreen').style.display = 'block';
    document.getElementById('driverForm').style.display = 'none';
    initMap();
}
function showDriverForm() {
    closeSidebar();
    document.getElementById('mainContent').style.display = 'none';
    document.getElementById('freightScreen').style.display = 'none';
    document.getElementById('driverForm').style.display = 'block';
}

let currentStep = 1;
function nextStep(step) {
    if (currentStep < 3) currentStep++;
    updateSteps();
}
function prevStep(step) {
    if (currentStep > 1) currentStep--;
    updateSteps();
}
function updateSteps() {
    document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
    document.getElementById(`formStep${currentStep}`).classList.add('active');
    document.querySelectorAll('.step').forEach((s, i) => s.classList.toggle('active', i + 1 === currentStep));
}
function submitDriver() {
    alert('Cadastro enviado! Entraremos em contato.');
    showHome();
}

function enviarMotorista() {
    const form = document.getElementById("formMotorista");
    const dados = new FormData(form);

    fetch("salvar_motorista.php", {
        method: "POST",
        body: dados
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "sucesso") {
            alert("Cadastro de motorista enviado com sucesso!");
            showHome();
        } else {
            alert("Erro: " + data.mensagem);
        }
    })
    .catch(err => alert("Erro inesperado: " + err));
}

</script>
</body>
</html>
