<?php
// Inicializar $puntos para evitar undefined variable
$puntos = [];

if (isset($_SESSION['negocio']) && $_SESSION['negocio']) {
    $negocio_id = (int) $_SESSION['negocio'];  // Cast a entero para seguridad
    
    // Filtrar solo domiciliarios (rol_id = 3) del negocio asignado
    $stmt = $conn->prepare("SELECT id, nombre, latitud, longitud, apellido FROM usuarios WHERE conductor_negocio = ? AND rol_id = 3");
    $stmt->bind_param("i", $negocio_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $puntos[] = $row;
    }
}
?>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Estilos de Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height: 600px;
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .map-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .map-header h1 {
            margin: 0;
        }
        
        .refresh-info {
            background: #e3f2fd;
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 14px;
            color: #1976d2;
        }
        
        .domiciliario-count {
            background: #4CAF50;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            margin-left: 10px;
        }
        
        .no-domiciliarios {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="map-header">
        <div>
            <h1>
                Listado de domiciliarios
                <span class="domiciliario-count" id="domiciliarioCount"><?php echo count($puntos); ?></span>
            </h1>
        </div>
        <div class="refresh-info">
            🔄 Actualización automática cada 10 segundos
        </div>
    </div>
    
    <?php if (empty($puntos)): ?>
    <div class="no-domiciliarios">
        ⚠️ No hay domiciliarios asignados a este negocio o ninguno ha compartido su ubicación.
    </div>
    <?php endif; ?>
    
    <div id="map"></div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let mapa;
        let markers = [];
        
        function initMap() {
            // Inicializar mapa
            if (!mapa) {
                mapa = L.map('map').setView([7.8891, -72.4967], 13);
                
                // Capa de mapa base (OpenStreetMap)
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a>',
                    maxZoom: 19
                }).addTo(mapa);
            }
            
            // Limpiar marcadores anteriores
            markers.forEach(marker => mapa.removeLayer(marker));
            markers = [];
            
            // Marcadores desde PHP
            const puntos = <?php echo json_encode($puntos); ?>;
            
            if (puntos.length > 0) {
                let bounds = [];
                
                puntos.forEach(p => {
                    if (p.latitud && p.longitud) {
                        const lat = parseFloat(p.latitud);
                        const lng = parseFloat(p.longitud);
                        
                        // Crear icono personalizado para domiciliarios
                        const deliveryIcon = L.divIcon({
                            className: 'custom-delivery-icon',
                            html: `
                                <div style="
                                    background: #2196F3;
                                    color: white;
                                    border-radius: 50%;
                                    width: 40px;
                                    height: 40px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-size: 20px;
                                    border: 3px solid white;
                                    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                                ">
                                    🏍️
                                </div>
                            `,
                            iconSize: [40, 40],
                            iconAnchor: [20, 20]
                        });
                        
                        const marker = L.marker([lat, lng], { icon: deliveryIcon })
                            .addTo(mapa)
                            .bindPopup(`
                                <div style="text-align: center;">
                                    <strong style="font-size: 16px;">📍 ${p.nombre} ${p.apellido || ''}</strong><br>
                                    <small style="color: #666;">Domiciliario</small><br>
                                    <small style="color: #999;">Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}</small>
                                </div>
                            `);
                        
                        markers.push(marker);
                        bounds.push([lat, lng]);
                    }
                });
                
                // Ajustar vista del mapa para mostrar todos los marcadores
                if (bounds.length > 0) {
                    if (bounds.length === 1) {
                        mapa.setView(bounds[0], 15);
                    } else {
                        mapa.fitBounds(bounds, { padding: [50, 50] });
                    }
                }
            }
            
            // Actualizar contador
            document.getElementById('domiciliarioCount').textContent = puntos.length;
        }
        
        // Inicializar mapa al cargar
        initMap();
        
        // Actualizar mapa cada 10 segundos
        setInterval(() => {
            location.reload();
        }, 10000);
    </script>
</body>
</html>
