<?php
// Seguridad: evitar errores si no vienen variables desde controller
$pedidoDev   = $pedidoDev   ?? [];
$detalleDev  = $detalleDev  ?? [];
?>

<style>

/* =========================================================
   GENERAL
========================================================= */

.checkout-wrap{
    background:#f4f6fb;
    min-height:100vh;
    padding:40px 0 60px;
}

.checkout-title{
    font-size:28px;
    font-weight:800;
    color:#1a1a2e;
    margin-bottom:28px;
}

/* =========================================================
   SECCIONES
========================================================= */

.co-section{
    background:#fff;
    border-radius:16px;
    box-shadow:0 2px 12px rgba(0,0,0,.06);
    margin-bottom:24px;
    overflow:hidden;
}

.co-section-header{
    background:#1a1a2e;
    color:#fff;
    padding:14px 24px;
    font-size:15px;
    font-weight:700;
    display:flex;
    align-items:center;
    gap:8px;
}

.co-section-body{
    padding:24px;
}

/* =========================================================
   RESUMEN
========================================================= */

.resumen-table{
    width:100%;
    border-collapse:collapse;
}

.resumen-table th{
    font-size:12px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.5px;
    color:#6b7280;
    padding:0 0 12px;
    border-bottom:2px solid #f3f4f6;
}

.resumen-table td{
    padding:14px 0;
    border-bottom:1px solid #f3f4f6;
    vertical-align:middle;
}

/* =========================================================
   BOTONES
========================================================= */

.btn-pagar{
    width:100%;
    padding:16px;
    background:linear-gradient(135deg,#7c3aed,#6d28d9);
    color:#fff;
    border:none;
    border-radius:14px;
    font-size:18px;
    font-weight:800;
    cursor:pointer;
}

/* =========================================================
   DEVOLUCIÓN LAYOUT (tu diseño intacto)
========================================================= */

.dev-page{
    font-family: Arial, sans-serif;
}

.dev-back{
    display:inline-block;
    margin:20px 0;
    text-decoration:none;
    font-weight:700;
    color:#1a1a2e;
}

.dev-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.dev-title-text h2{
    margin:0;
    font-size:24px;
}

.dev-bar{
    height:4px;
    background:#7c3aed;
    margin-bottom:20px;
}

.dev-pedido-header{
    display:flex;
    gap:20px;
    background:#fff;
    padding:20px;
    border-radius:12px;
    margin-bottom:20px;
    box-shadow:0 2px 10px rgba(0,0,0,.05);
}

.badge-entregado{
    background:#d1fae5;
    padding:4px 10px;
    border-radius:20px;
    font-size:12px;
}

.dev-section{
    background:#fff;
    padding:20px;
    border-radius:12px;
    margin-bottom:20px;
}

.dev-section-title{
    font-weight:800;
    margin-bottom:5px;
}

.dev-section-sub{
    font-size:13px;
    color:#6b7280;
    margin-bottom:15px;
}

.prod-row{
    border:1px solid #e5e7eb;
    padding:15px;
    border-radius:12px;
    margin-bottom:12px;
    cursor:pointer;
}

.prod-row.sel{
    border-color:#7c3aed;
    background:#faf5ff;
}

.prod-img-box img{
    width:60px;
    height:60px;
    object-fit:cover;
}

.dev-input{
    width:100%;
    padding:10px;
    border:1px solid #ddd;
    border-radius:8px;
}

.dev-btn{
    width:100%;
    padding:14px;
    background:#7c3aed;
    color:#fff;
    border:none;
    border-radius:12px;
    font-weight:800;
    cursor:pointer;
}

.dev-error{
    display:none;
    color:red;
    margin-bottom:10px;
}

</style>

<div class="dev-page">
<div class="container-fluid">

    <a href="index.php?action=mis_pedidos" class="dev-back">← Volver a mis pedidos</a>

    <div class="dev-top">
        <div>
            <h2>Solicitar Devolución</h2>
        </div>
    </div>

    <div class="dev-bar"></div>

    <!-- HEADER PEDIDO -->
    <div class="dev-pedido-header">

        <div>
            <h5>
                Pedido #<?= isset($pedidoDev['id_pedido']) ? str_pad($pedidoDev['id_pedido'], 6, '0', STR_PAD_LEFT) : 'N/A' ?>
            </h5>
            <span class="badge-entregado">Entregado</span>
        </div>

        <div>
            <strong>Total:</strong>
            $<?= isset($pedidoDev['total_pedido']) ? number_format($pedidoDev['total_pedido'],0,',','.') : '0' ?>
        </div>

        <div>
            <strong>Productos:</strong>
            <?= isset($detalleDev) ? count($detalleDev) : 0 ?>
        </div>

    </div>

    <div class="row">

        <div class="col-lg-7">

            <form method="POST" action="index.php?action=procesar_devolucion" id="form-dev">

                <input type="hidden" name="id_pedido"
                       value="<?= $pedidoDev['id_pedido'] ?? '' ?>">

                <div class="dev-section">
                    <div class="dev-section-title">Selecciona productos</div>

                    <?php if (!empty($detalleDev)): ?>

                        <?php foreach ($detalleDev as $d): ?>

                        <div class="prod-row" id="row_<?= $d['id_producto'] ?? 0 ?>"
                             onclick="toggleProd(<?= $d['id_producto'] ?? 0 ?>)">

                            <input type="checkbox"
                                   class="prod-check"
                                   id="chk_<?= $d['id_producto'] ?? 0 ?>"
                                   name="productos[<?= $d['id_producto'] ?? 0 ?>][seleccionado]">

                            <div>
                                <strong><?= htmlspecialchars($d['nombre'] ?? 'Producto') ?></strong><br>
                                Cant: <?= $d['cantidad'] ?? 0 ?>
                            </div>

                        </div>

                        <?php endforeach; ?>

                    <?php else: ?>
                        <p>No hay productos en este pedido.</p>
                    <?php endif; ?>

                </div>

                <div class="dev-error" id="dev-error">
                    Debes seleccionar al menos un producto
                </div>

                <button type="button" class="dev-btn" onclick="validarYEnviar()">
                    Enviar devolución
                </button>

            </form>

        </div>

    </div>

</div>
</div>

<script>
function toggleProd(id){
    const chk = document.getElementById('chk_'+id);
    const row = document.getElementById('row_'+id);
    if(!chk || !row) return;

    chk.checked = !chk.checked;
    row.classList.toggle('sel', chk.checked);
}

function validarYEnviar(){
    const checks = document.querySelectorAll('.prod-check:checked');
    const error = document.getElementById('dev-error');

    if(checks.length === 0){
        error.style.display = 'block';
        return;
    }

    document.getElementById('form-dev').submit();
}
</script>