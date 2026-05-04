<?php
require_once "config.php";

$id_promocion = $_GET['id'] ?? null;
$error = null;
$success = null;
$promo = null;

if (!$id_promocion) {
    die("Cupón no válido.");
}

$enlace = dbConectar();

// Obtener info de la promoción y el negocio
$sql = "SELECT p.*, n.nombre_negocio, n.codigo_canje 
        FROM promociones p 
        INNER JOIN negocios n ON p.ID_Negocio = n.ID_Negocio 
        WHERE p.ID_Promocion = ?";
$stmt = $enlace->prepare($sql);
$stmt->bind_param("i", $id_promocion);
$stmt->execute();
$promo = $stmt->get_result()->fetch_assoc();

if (!$promo) {
    die("Promoción no encontrada.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = $_POST['codigo'] ?? '';
    
    if ($codigo === $promo['codigo_canje']) {
        if ($promo['cantidad'] > 0) {
            // Restar cupón
            $sqlRestar = "UPDATE promociones SET cantidad = cantidad - 1, Canjeados = Canjeados + 1 WHERE ID_Promocion = ?";
            $stmtRestar = $enlace->prepare($sqlRestar);
            $stmtRestar->bind_param("i", $id_promocion);
            if ($stmtRestar->execute()) {
                $success = "¡Cupón canjeado con éxito!";
                $promo['cantidad']--; // Actualizar para la vista
            } else {
                $error = "Error al procesar el canje.";
            }
        } else {
            $error = "Lo sentimos, ya no quedan cupones disponibles.";
        }
    } else {
        $error = "Código de negocio incorrecto.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canje de Cupón - Yo Local</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --ui-primary: #6366f1;
            --ui-bg: #f8fafc;
        }
        body {
            background-color: var(--ui-bg);
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .canje-card {
            background: white;
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        .icon-box {
            width: 80px;
            height: 80px;
            background: #eef2ff;
            color: var(--ui-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin: 0 auto 20px;
        }
        .promo-title {
            font-weight: 700;
            margin-bottom: 5px;
        }
        .business-name {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 12px;
            padding: 12px;
            text-align: center;
            font-size: 1.2rem;
            letter-spacing: 2px;
            border: 2px solid #e2e8f0;
        }
        .btn-primary {
            background: var(--ui-primary);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            margin-top: 15px;
            width: 100%;
        }
        .status-msg {
            padding: 15px;
            border-radius: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="canje-card">
    <div class="icon-box">
        <i class="bi bi-ticket-perforated"></i>
    </div>
    
    <h2 class="promo-title"><?= htmlspecialchars($promo['titulo']) ?></h2>
    <p class="business-name"><?= htmlspecialchars($promo['nombre_negocio']) ?></p>
    
    <div class="alert alert-info py-2" style="border-radius: 10px; font-size: 0.85rem;">
        Cupones disponibles: <strong><?= $promo['cantidad'] ?></strong>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success status-msg">
            <i class="bi bi-check-circle-fill me-2"></i> <?= $success ?>
        </div>
        <button class="btn btn-outline-secondary mt-3 w-100" onclick="window.close()">Cerrar</button>
    <?php else: ?>
        <form method="POST">
            <div class="mb-3">
                <label for="codigo" class="form-label text-secondary small fw-bold">CÓDIGO DEL NEGOCIO</label>
                <input type="password" name="codigo" id="codigo" class="form-control" placeholder="****" required autocomplete="off">
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger py-2 small" style="border-radius: 10px;">
                    <i class="bi bi-exclamation-circle me-1"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary shadow-sm">Validar Canje</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
<?php $enlace->close(); ?>
