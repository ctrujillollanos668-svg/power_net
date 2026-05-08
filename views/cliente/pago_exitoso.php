<?php
// Variables desde index.php (case 'pago_exitoso'):
// $id_pedido_ok, $factura_ok, $total_ok
?>

<style>
.exito-page {
    background: #f4f6fb;
    min-height: 60vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
}

.exito-card {
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.08);
    padding: 48px 40px;
    text-align: center;
    max-width: 520px;
    width: 100%;
}

.exito-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #10b981, #059669);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    margin: 0 auto 24px;
    box-shadow: 0 8px 24px rgba(16,185,129,0.3);
}

.exito-title {
    font-size: 26px;
    font-weight: 900;
    color: #1a1a2e;
    margin-bottom: 8px;
}

.exito-sub {
    font-size: 15px;
    color: #6b7280;
    margin-bottom: 28px;
}

.exito-info {
    background: #f9fafb;
    border-radius: 14px;
    padding: 20px;
    margin-bottom: 28px;
    text-align: left;
}

.exito-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f3f4f6;
    font-size: 14px;
}

.exito-info-row:last-child { border-bottom: none; }
.exito-info-row .label { color: #9ca3af; font-weight: 600; }
.exito-info-row .value { font-weight: 700; color: #1a1a2e; }
.exito-info-row .value.green { color: #10b981; }

.exito-btns {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-exito-primary {
    padding: 12px 28px;
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    transition: all .2s;
}

.btn-exito-primary:hover {
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(124,58,237,0.3);
}

.btn-exito-secondary {
    padding: 12px 28px;
    background: #f3f4f6;
    color: #374151;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    transition: all .2s;
}

.btn-exito-secondary:hover {
    background: #e5e7eb;
    color: #1a1a2e;
}
</style>

<div class="exito-page">
    <div class="exito-card">

        <div class="exito-icon">✅</div>

        <div class="exito-title">¡Pago exitoso!</div>
        <div class="exito-sub">Tu pedido fue registrado y está siendo procesado.</div>

        <div class="exito-info">
            <div class="exito-info-row">
                <span class="label">Número de pedido</span>
                <span class="value">#<?= str_pad($id_pedido_ok ?? 0, 6, '0', STR_PAD_LEFT) ?></span>
            </div>
            <div class="exito-info-row">
                <span class="label">Factura</span>
                <span class="value"><?= htmlspecialchars($factura_ok ?? '—') ?></span>
            </div>
            <div class="exito-info-row">
                <span class="label">Total pagado</span>
                <span class="value green">$<?= number_format($total_ok ?? 0, 0, ',', '.') ?></span>
            </div>
            <div class="exito-info-row">
                <span class="label">Estado</span>
                <span class="value" style="color:#f59e0b;">⏳ Pendiente de envío</span>
            </div>
        </div>

        <div class="exito-btns">
            <a href="index.php?action=mis_pedidos" class="btn-exito-primary">
                📦 Ver mis pedidos
            </a>
            <a href="index.php" class="btn-exito-secondary">
                🛍️ Seguir comprando
            </a>
        </div>

    </div>
</div>
