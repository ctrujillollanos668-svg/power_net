<?php
// 🔒 Blindaje de variables (NO toca controllers)
$pedidoFac   = $pedidoFac   ?? null;
$detalleFac  = $detalleFac  ?? [];
$clienteFac  = $clienteFac  ?? [];
$usuarioFac  = $usuarioFac  ?? [];
?>

<?php if (!$pedidoFac): ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura no encontrada</title>
</head>
<body style="font-family:Arial;text-align:center;padding:60px;background:#f3f4f6;">

    <h2>⚠️ No se encontró la factura</h2>
    <p>Es posible que el pedido no exista o haya ocurrido un error.</p>

    <a href="index.php?action=mis_pedidos"
       style="display:inline-block;margin-top:20px;padding:10px 20px;background:#1a1a2e;color:#fff;border-radius:10px;text-decoration:none;">
        ← Volver a mis pedidos
    </a>

</body>
</html>
<?php exit; ?>
<?php endif; ?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #<?= $pedidoFac['id_pedido'] ?? 0 ?> — Power Net</title>

    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family:'Segoe UI', Arial, sans-serif;
            background:#f0f2f5;
            color:#1a1a2e;
            padding:30px 20px;
        }

        .acciones-factura {
            max-width:800px;
            margin:0 auto 20px;
            display:flex;
            gap:12px;
        }

        .btn-imprimir {
            padding:10px 24px;
            background:#1a1a2e;
            color:#fff;
            border:none;
            border-radius:10px;
            font-weight:700;
            cursor:pointer;
        }

        .btn-imprimir:hover { background:#7c3aed; }

        .btn-volver {
            padding:10px 24px;
            background:#fff;
            border:2px solid #e5e7eb;
            border-radius:10px;
            text-decoration:none;
            font-weight:600;
            color:#1a1a2e;
        }

        .factura {
            max-width:800px;
            margin:0 auto;
            background:#fff;
            border-radius:16px;
            box-shadow:0 4px 24px rgba(0,0,0,0.08);
            overflow:hidden;
        }

        .factura-header {
            background:linear-gradient(135deg,#1a1a2e,#302b63);
            color:#fff;
            padding:36px 40px;
            display:flex;
            justify-content:space-between;
        }

        .empresa-nombre { font-size:28px; font-weight:900; }

        .factura-numero { text-align:right; }

        .numero {
            font-size:32px;
            font-weight:900;
        }

        .estado-badge {
            display:inline-block;
            padding:4px 12px;
            border-radius:20px;
            font-size:12px;
            font-weight:700;
            margin-top:8px;
        }

        .estado-entregado { background:#d1fae5; color:#065f46; }
        .estado-enviado { background:#dbeafe; color:#1e40af; }

        .factura-body { padding:36px 40px; }

        .factura-info {
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:20px;
            margin-bottom:30px;
        }

        .info-box {
            background:#f9fafb;
            padding:20px;
            border-radius:12px;
        }

        .info-box-title {
            font-size:12px;
            font-weight:800;
            color:#7c3aed;
            margin-bottom:10px;
        }

        .info-row {
            display:flex;
            justify-content:space-between;
            font-size:13px;
            margin-bottom:6px;
        }

        table {
            width:100%;
            border-collapse:collapse;
        }

        thead th {
            background:#1a1a2e;
            color:#fff;
            padding:10px;
            font-size:12px;
        }

        tbody td {
            padding:10px;
            border-bottom:1px solid #eee;
            font-size:13px;
        }

        .totales {
            margin-top:20px;
            display:flex;
            justify-content:flex-end;
        }

        .totales-box {
            width:280px;
        }

        .total-row {
            display:flex;
            justify-content:space-between;
            padding:6px 0;
        }

        .total-final {
            font-weight:900;
            font-size:18px;
            color:#7c3aed;
        }

        .factura-footer {
            background:#f9fafb;
            padding:20px 40px;
            display:flex;
            justify-content:space-between;
        }

        @media print {
            .acciones-factura { display:none; }
            body { background:#fff; }
        }
    </style>
</head>

<body>

<!-- BOTONES -->
<div class="acciones-factura">
    <button class="btn-imprimir" onclick="window.print()">🖨️ Imprimir / PDF</button>
    <a href="index.php?action=mis_pedidos" class="btn-volver">← Volver</a>
</div>

<!-- FACTURA -->
<div class="factura">

    <div class="factura-header">
        <div>
            <div class="empresa-nombre">⚡ Power Net</div>
        </div>

        <div class="factura-numero">
            <div class="numero">
                #<?= str_pad($pedidoFac['id_pedido'] ?? 0, 6, '0', STR_PAD_LEFT) ?>
            </div>

            <span class="estado-badge <?= (($pedidoFac['estado_pedido'] ?? '') === 'entregado') ? 'estado-entregado' : 'estado-enviado' ?>">
                <?= ucfirst($pedidoFac['estado_pedido'] ?? 'N/A') ?>
            </span>
        </div>
    </div>

    <div class="factura-body">

        <!-- CLIENTE -->
        <div class="factura-info">

            <div class="info-box">
                <div class="info-box-title">Cliente</div>

                <div class="info-row">
                    <span>Nombre</span>
                    <span><?= htmlspecialchars($usuarioFac['nombre'] ?? 'N/A') ?></span>
                </div>

                <div class="info-row">
                    <span>Email</span>
                    <span><?= htmlspecialchars($usuarioFac['email'] ?? 'N/A') ?></span>
                </div>
            </div>

            <div class="info-box">
                <div class="info-box-title">Pedido</div>

                <div class="info-row">
                    <span>Número</span>
                    <span>#<?= $pedidoFac['id_pedido'] ?? 0 ?></span>
                </div>

                <div class="info-row">
                    <span>Método</span>
                    <span><?= ucfirst($pedidoFac['metodo_pago'] ?? 'N/A') ?></span>
                </div>
            </div>

        </div>

        <!-- PRODUCTOS -->
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cant</th>
                    <th>Precio</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($detalleFac as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['nombre'] ?? 'Producto') ?></td>
                    <td><?= $d['cantidad'] ?? 0 ?></td>
                    <td>$<?= number_format($d['precio_unitario'] ?? 0, 0, ',', '.') ?></td>
                    <td>$<?= number_format($d['subtotal'] ?? 0, 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- TOTALES -->
        <div class="totales">
            <div class="totales-box">

                <div class="total-row">
                    <span>Total</span>
                    <span class="total-final">
                        $<?= number_format($pedidoFac['total_pedido'] ?? 0, 0, ',', '.') ?>
                    </span>
                </div>

            </div>
        </div>

    </div>

    <div class="factura-footer">
        <div>Gracias por tu compra</div>
    </div>

</div>

</body>
</html>