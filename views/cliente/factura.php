<?php
if (!isset($pedidoFac, $detalleFac, $usuarioFac, $clienteFac)) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #<?= $pedidoFac['id_pedido'] ?> — Power Net</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f0f2f5;
            color: #1a1a2e;
            padding: 30px 20px;
        }

        /* ---- BOTONES (solo en pantalla, no en impresión) ---- */
        .acciones-factura {
            max-width: 800px;
            margin: 0 auto 20px;
            display: flex;
            gap: 12px;
        }

        .btn-imprimir {
            padding: 10px 24px;
            background: #1a1a2e;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s;
        }

        .btn-imprimir:hover { background: #7c3aed; }

        .btn-volver {
            padding: 10px 24px;
            background: #fff;
            color: #1a1a2e;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-volver:hover { border-color: #7c3aed; color: #7c3aed; }

        /* ---- FACTURA ---- */
        .factura {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        /* Cabecera */
        .factura-header {
            background: linear-gradient(135deg, #1a1a2e, #302b63);
            color: #fff;
            padding: 36px 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .empresa-nombre {
            font-size: 28px;
            font-weight: 900;
            letter-spacing: -0.5px;
        }

        .empresa-sub {
            font-size: 12px;
            color: #a78bfa;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .empresa-datos {
            font-size: 12px;
            color: #c4b5fd;
            margin-top: 12px;
            line-height: 1.6;
        }

        .factura-numero {
            text-align: right;
        }

        .factura-numero .label {
            font-size: 11px;
            color: #a78bfa;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .factura-numero .numero {
            font-size: 32px;
            font-weight: 900;
            color: #fff;
        }

        .factura-numero .codigo {
            font-size: 13px;
            color: #c4b5fd;
            margin-top: 4px;
        }

        /* Estado badge */
        .estado-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-top: 8px;
        }

        .estado-entregado { background: #d1fae5; color: #065f46; }
        .estado-enviado   { background: #dbeafe; color: #1e40af; }

        /* Cuerpo */
        .factura-body { padding: 36px 40px; }

        /* Datos cliente / pedido */
        .factura-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 32px;
        }

        .info-box {
            background: #f9fafb;
            border-radius: 12px;
            padding: 20px;
        }

        .info-box-title {
            font-size: 11px;
            font-weight: 800;
            color: #7c3aed;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 6px;
            color: #374151;
        }

        .info-row span:first-child { color: #9ca3af; }
        .info-row span:last-child  { font-weight: 600; }

        /* Tabla de productos */
        .tabla-titulo {
            font-size: 13px;
            font-weight: 800;
            color: #1a1a2e;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        thead th {
            background: #1a1a2e;
            color: #fff;
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        thead th:last-child,
        thead th:nth-child(2),
        thead th:nth-child(3) { text-align: right; }

        tbody tr { border-bottom: 1px solid #f3f4f6; }
        tbody tr:last-child { border-bottom: none; }

        tbody td {
            padding: 14px 16px;
            color: #374151;
        }

        tbody td:nth-child(2),
        tbody td:nth-child(3),
        tbody td:last-child { text-align: right; }

        tbody tr:hover { background: #faf5ff; }

        /* Totales */
        .totales {
            margin-top: 24px;
            display: flex;
            justify-content: flex-end;
        }

        .totales-box {
            width: 280px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
        }

        .total-row:last-child {
            border-bottom: none;
            padding-top: 12px;
            font-size: 18px;
            font-weight: 900;
            color: #1a1a2e;
        }

        .total-row:last-child span:last-child { color: #7c3aed; }

        /* Pie de factura */
        .factura-footer {
            background: #f9fafb;
            border-top: 2px solid #f3f4f6;
            padding: 24px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-nota {
            font-size: 12px;
            color: #9ca3af;
            max-width: 400px;
            line-height: 1.5;
        }

        .footer-gracias {
            font-size: 16px;
            font-weight: 800;
            color: #7c3aed;
        }

        /* ---- IMPRESIÓN ---- */
        @media print {
            body { background: #fff; padding: 0; }
            .acciones-factura { display: none !important; }
            .factura {
                box-shadow: none;
                border-radius: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- BOTONES (no se imprimen) -->
    <div class="acciones-factura">
        <button class="btn-imprimir" onclick="window.print()">
            🖨️ Imprimir / Guardar PDF
        </button>
        <a href="index.php?action=mis_pedidos" class="btn-volver">
            ← Volver a mis pedidos
        </a>
    </div>

    <!-- FACTURA -->
    <div class="factura">

        <!-- CABECERA -->
        <div class="factura-header">
            <div>
                <div class="empresa-nombre">⚡ Power Net</div>
                <div class="empresa-sub">Tecnología &amp; Redes</div>
                <div class="empresa-datos">
                    Bogotá, Colombia<br>
                    contacto@powernet.com<br>
                    +57 300 000 0000
                </div>
            </div>
            <div class="factura-numero">
                <div class="label">Factura</div>
                <div class="numero">#<?= str_pad($pedidoFac['id_pedido'], 6, '0', STR_PAD_LEFT) ?></div>
                <div class="codigo"><?= htmlspecialchars($pedidoFac['factura'] ?? 'FAC-' . $pedidoFac['id_pedido']) ?></div>
                <div>
                    <span class="estado-badge <?= strtolower($pedidoFac['estado_pedido']) === 'entregado' ? 'estado-entregado' : 'estado-enviado' ?>">
                        <?= ucfirst($pedidoFac['estado_pedido']) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- CUERPO -->
        <div class="factura-body">

            <!-- INFO CLIENTE Y PEDIDO -->
            <div class="factura-info">

                <div class="info-box">
                    <div class="info-box-title">👤 Datos del cliente</div>
                    <div class="info-row">
                        <span>Nombre</span>
                        <span><?= htmlspecialchars($usuarioFac['nombre'] ?? $_SESSION['usuario']['nombre']) ?></span>
                    </div>
                    <div class="info-row">
                        <span>Correo</span>
                        <span><?= htmlspecialchars($usuarioFac['email'] ?? $_SESSION['usuario']['email']) ?></span>
                    </div>
                    <?php if (!empty($clienteFac['direccion'])): ?>
                    <div class="info-row">
                        <span>Dirección</span>
                        <span><?= htmlspecialchars($clienteFac['direccion']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="info-box">
                    <div class="info-box-title">📦 Datos del pedido</div>
                    <div class="info-row">
                        <span>Número</span>
                        <span>#<?= $pedidoFac['id_pedido'] ?></span>
                    </div>
                    <div class="info-row">
                        <span>Fecha pedido</span>
                        <span>
                            <?php
                            $fp = $pedidoFac['fecha_pedido'] ?? $pedidoFac['fecha_pago'] ?? null;
                            echo $fp ? date('d/m/Y', strtotime($fp)) : 'N/A';
                            ?>
                        </span>
                    </div>
                    <?php if (!empty($pedidoFac['fecha_pago'])): ?>
                    <div class="info-row">
                        <span>Fecha pago</span>
                        <span><?= date('d/m/Y', strtotime($pedidoFac['fecha_pago'])) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="info-row">
                        <span>Método pago</span>
                        <span><?= ucfirst(htmlspecialchars($pedidoFac['metodo_pago'] ?? 'N/A')) ?></span>
                    </div>
                    <div class="info-row">
                        <span>Estado pago</span>
                        <span><?= ucfirst(htmlspecialchars($pedidoFac['estado_pago'] ?? 'N/A')) ?></span>
                    </div>
                </div>

            </div>

            <!-- TABLA DE PRODUCTOS -->
            <div class="tabla-titulo">Detalle de productos</div>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cant.</th>
                        <th>Precio unit.</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalleFac as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['nombre'] ?? 'Producto') ?></td>
                        <td><?= $d['cantidad'] ?></td>
                        <td>$<?= number_format($d['precio_unitario'], 0, ',', '.') ?></td>
                        <td>$<?= number_format($d['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- TOTALES -->
            <div class="totales">
                <div class="totales-box">
                    <div class="total-row">
                        <span>Subtotal</span>
                        <span>$<?= number_format($pedidoFac['total_pedido'], 0, ',', '.') ?></span>
                    </div>
                    <div class="total-row">
                        <span>Envío</span>
                        <span>Incluido</span>
                    </div>
                    <div class="total-row">
                        <span>TOTAL</span>
                        <span>$<?= number_format($pedidoFac['total_pedido'], 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>

        </div><!-- /factura-body -->

        <!-- PIE -->
        <div class="factura-footer">
            <div class="footer-nota">
                Esta factura es un comprobante de tu compra en Power Net.<br>
                Guárdala para cualquier reclamación o garantía.
            </div>
            <div class="footer-gracias">¡Gracias por tu compra! 🎉</div>
        </div>

    </div><!-- /factura -->

</body>
</html>
