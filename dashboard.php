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
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
        .input-group { margin-bottom: 20px; position: relative; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .input-group input { width: 100%; padding: 14px; border: 1px solid #ddd; border-radius: 12px; font-size: 1rem; }
        .input-group input:focus { outline: none; border-color: #000; }
        .confirm-btn { background: #000; color: white; border: none; padding: 16px; border-radius: 12px; font-size: 1.1rem; font-weight: bold; width: 100%; cursor: pointer; margin-top: 20px; }

        #map { height: 300px; width: 100%; border-radius: 12px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }

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

        .suggestions { 
            position: absolute; 
            background: white; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            width: 100%; 
            max-height: 150px; 
            overflow-y: auto; 
            z-index: 1000; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            display: none;
        }
        .suggestion-item { padding: 12px; cursor: pointer; border-bottom: 1px solid #eee; }
        .suggestion-item:hover { background: #f0f0f0; }

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

<!-- Início -->
<div class="main-content" id="mainContent">
    <div class="welcome">
        <h2>Olá, <?= $usuario ?>!</h2>
        <p>Precisa transportar algo hoje?</p>
        <button class="btn-large" onclick="showFreightRequest()">Solicitar Frete</button>
    </div>
</div>

<!-- Solicitar Frete -->
<div class="freight-screen" id="freightScreen">
    <div class="freight-header">
        <div class="back-btn" onclick="showHome()">←</div>
        <h2>Solicitar Frete</h2>
    </div>
    <div class="address-inputs">
        <div class="input-group">
            <label>Origem (A)</label>
            <input type="text" id="fromAddress" placeholder="Ex: Av. Beira Mar, Fortaleza" oninput="searchAddress(this, 'from')">
            <div id="suggestions-from" class="suggestions"></div>
        </div>
        <div class="input-group">
            <label>Destino (B)</label>
            <input type="text" id="toAddress" placeholder="Ex: Shopping Iguatemi, Fortaleza" oninput="searchAddress(this, 'to')">
            <div id="suggestions-to" class="suggestions"></div>
        </div>
        <div id="map"></div>
        <button class="confirm-btn" onclick="calculateRoute()">Buscar Motoristas</button>
    </div>
</div>

<!-- Formulário Motorista -->
<div class="driver-form" id="driverForm">
    <div class="freight-header">
        <div class="back-btn" onclick="showHome()">←</div>
        <h2>Cadastre-se como Motorista</h2>
    </div>
    <div style="padding:20px;">
        <div class="step-indicator">
            <div class="step active" id="step1">1</div>
            <div class="step" id="step2">2</div>
            <div class="step" id="step3">3</div>
        </div>

        <div class="form-step active" id="formStep1">
            <h3>Seus Dados</h3>
            <div class="input-group"><input type="text" placeholder="Nome completo"></div>
            <div class="input-group"><input type="tel" placeholder="CPF"></div>
            <div class="input-group"><input type="email" placeholder="E-mail"></div>
            <button class="next-btn" onclick="nextStep(1)">Próximo</button>
        </div>

        <div class="form-step" id="formStep2">
            <h3>Veículo</h3>
            <div class="input-group"><input type="text" placeholder="Modelo (ex: Fiat Toro)"></div>
            <div class="input-group"><input type="text" placeholder="Placa"></div>
            <div class="input-group">
                <select style="width:100%;padding:14px;border:1px solid #ddd;border-radius:12px;">
                    <option>Van</option>
                    <option>Utilitário</option>
                    <option>Caminhonete</option>
                    <option>Caminhão 3/4</option>
                </select>
            </div>
            <button class="prev-btn" onclick="prevStep(2)">Voltar</button>
            <button class="next-btn" onclick="nextStep(2)">Próximo</button>
        </div>

        <div class="form-step" id="formStep3">
            <h3>Documentos</h3>
            <p><input type="file" accept="image/*"> CNH (frente)</p>
            <p><input type="file" accept="image/*"> CRLV do veículo</p>
            <p><input type="file" accept="image/*"> Comprovante de residência</p>
            <button class="prev-btn" onclick="prevStep(3)">Voltar</button>
            <button class="next-btn" onclick="submitDriver()">Enviar</button>
        </div>
    </div>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let map, markerFrom, markerTo, routeLayer;

// Inicializa o mapa (centro em Fortaleza)
function initMap() {
    // Remove mapa antigo se existir
    if (map) {
        map.remove();
    }
    
    // Cria novo mapa
    map = L.map('map').setView([-3.7319, -38.5267], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Camada para rotas
    routeLayer = L.layerGroup().addTo(map);
    
    // Recalcula tamanho (corrige erro de div escondido)
    setTimeout(() => {
        if (map) map.invalidateSize();
    }, 100);
}

// Autocomplete com Nominatim (OSM) - busca em Fortaleza/CE
async function searchAddress(input, type) {
    const query = input.value.trim();
    const suggestionsDiv = document.getElementById(`suggestions-${type}`);
    suggestionsDiv.innerHTML = '';
    if (query.length < 3) {
        suggestionsDiv.style.display = 'none';
        return;
    }

    try {
        // Busca focada em Fortaleza, CE, Brasil
        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&city=Fortaleza&state=Ceará&country=Brazil&countrycodes=br&limit=5&addressdetails=1`);
        const data = await response.json();
        
        if (data.length === 0) {
            suggestionsDiv.style.display = 'none';
            return;
        }
        
        suggestionsDiv.style.display = 'block';
        data.forEach(item => {
            const div = document.createElement('div');
            div.className = 'suggestion-item';
            // Mostra endereço limpo
            const address = item.display_name.split(', Brazil')[0]; // Remove "Brasil" se aparecer
            div.textContent = address;
            div.onclick = () => {
                input.value = address;
                suggestionsDiv.style.display = 'none';
                // Coloca marcador no mapa
                const lat = parseFloat(item.lat);
                const lon = parseFloat(item.lon);
                placeMarker(lat, lon, type);
            };
            suggestionsDiv.appendChild(div);
        });
    } catch (e) {
        console.error('Erro no autocomplete:', e);
        // Fallback: esconde sugestões
        suggestionsDiv.style.display = 'none';
    }
}

// Coloca marcador no mapa
function placeMarker(lat, lon, type) {
    if (!map) {
        alert('Mapa não carregado. Tente novamente.');
        return;
    }
    
    const latLng = [lat, lon];
    if (type === 'from') {
        if (markerFrom) {
            markerFrom.setLatLng(latLng);
        } else {
            markerFrom = L.marker(latLng, { icon: greenIcon }).addTo(map)
                .bindPopup('Origem').openPopup();
        }
    } else {
        if (markerTo) {
            markerTo.setLatLng(latLng);
        } else {
            markerTo = L.marker(latLng, { icon: redIcon }).addTo(map)
                .bindPopup('Destino').openPopup();
        }
    }
    map.setView(latLng, 14);
}

// Ícones personalizados (verde para origem, vermelho para destino)
var greenIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

var redIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

// Calcula rota (corrigido para evitar erros)
async function calculateRoute() {
    if (!map) {
        alert('Mapa não carregado. Vá para "Solicitar Frete" e tente novamente.');
        showFreightRequest(); // Recarrega tela
        return;
    }
    
    if (!markerFrom || !markerTo) {
        alert('Selecione a origem e o destino digitando nos campos acima (use o autocomplete).');
        return;
    }

    const start = markerFrom.getLatLng();
    const end = markerTo.getLatLng();

    // Limpa rota anterior
    routeLayer.clearLayers();

    try {
        // Chave OpenRouteService (cadastre grátis em openrouteservice.org - 2000 req/dia)
        const API_KEY = '5b3ce3597851110001cf6248example1234567890ab'; // SUBSTITUA PELA SUA CHAVE REAL!
        
        // Se chave inválida, usa fallback linha reta
        if (API_KEY.includes('example')) {
            throw new Error('Chave inválida - usando rota simples');
        }
        
        const response = await fetch('https://api.openrouteservice.org/v2/directions/driving-car/json', {
            method: 'POST',
            headers: { 
                'Authorization': API_KEY, 
                'Content-Type': 'application/json' 
            },
            body: JSON.stringify({ 
                coordinates: [[start.lng, start.lat], [end.lng, end.lat]],
                instructions: false 
            })
        });
        
        if (!response.ok) {
            throw new Error('Erro na API: ' + response.status);
        }
        
        const data = await response.json();
        if (data.features && data.features[0]) {
            const coords = data.features[0].geometry.coordinates.map(c => [c[1], c[0]]);
            L.polyline(coords, { color: '#000', weight: 5 }).addTo(routeLayer);
            map.fitBounds(L.latLngBounds([start, end]));
            
            const distance = (data.features[0].properties.summary.distance / 1000).toFixed(1);
            const duration = Math.round(data.features[0].properties.summary.duration / 60);
            alert(`Rota calculada!\nDistância: ${distance} km\nTempo: ${duration} min`);
            return;
        }
    } catch (e) {
        console.warn('Erro na rota avançada, usando linha reta:', e);
    }
    
    // Fallback: linha reta simples
    L.polyline([start, end], { color: '#000', weight: 5, dashArray: '10,10' }).addTo(routeLayer);
    map.fitBounds(L.latLngBounds([start, end]));
    alert('Rota simples traçada (use uma chave OpenRouteService para rotas reais). Distância aproximada: ' + start.distanceTo(end).toFixed(1) / 1000 + ' km');
}

// Funções de navegação (corrigidas)
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
    // Inicializa mapa após mostrar div
    setTimeout(initMap, 200);
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
</script>
</body>
</html>