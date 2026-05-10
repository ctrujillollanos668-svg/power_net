<?php
// Variables preparadas desde index.php (case 'solicitar_devolucion'):
// $pedidoDev, $detalleDev, $id_pedido_dev

// 🔴 FIX IMPORTANTE: evitar undefined variables
$pedidoDev  = $pedidoDev  ?? [];
$detalleDev = $detalleDev ?? [];
?>

<style>
/* ============ BASE ============ */
.dev-page {
    background: #f0f2f8;
    padding: 32px 0 60px;
    margin: -1px;
}

/* ---- BACK ---- */
.dev-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #7c3aed;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 20px;
    transition: opacity .2s;
}

.dev-back:hover { opacity: .7; }

/* ---- HEADER ---- */
.dev-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
    flex-wrap: wrap;
    gap: 16px;
}

.dev-title-block { display: flex; align-items: center; gap: 14px; }

.dev-title-icon {
    width: 52px;
    height: 52px;
    background: #ede9fe;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.dev-title-text h2 {
    font-size: 22px;
    font-weight: 900;
    color: #1a1a2e;
    margin: 0;
}

.dev-title-text p {
    font-size: 13px;
    color: #6b7280;
    margin: 2px 0 0;
}

/* ---- STEPPER ---- */
.dev-stepper { display: flex; align-items: center; }

.dev-step-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
}

.dev-step-circle.active { background: #7c3aed; color: #fff; }
.dev-step-circle.inactive { background: #e5e7eb; color: #9ca3af; }

/* ---- BARRA ---- */
.dev-bar {
    height: 4px;
    background: linear-gradient(90deg,#7c3aed,#a78bfa);
    border-radius: 4px;
    margin-bottom: 24px;
}

/* ---- PEDIDO ---- */
.dev-pedido-header {
    background: #fff;
    border-radius: 16px;
    padding: 20px 28px;
    margin-bottom: 20px;
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

/* ---- INPUTS ---- */
.dev-input {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    background: #fafafa;
}

/* ---- BOTÓN ---- */
.dev-btn {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg,#7c3aed,#6d28d9);
    color: #fff;
    border: none;
    border-radius: 14px;
    font-weight: 800;
    cursor: pointer;
}

</style>

<div class="dev-page">
<div class="container-fluid" style="padding:0 40px;">

    <a href="index.php?action=mis_pedidos" class="dev-back">← Volver a mis pedidos</a>

    <div class="dev-top">
        <div class="dev-title-block">
            <div class="dev-title-icon">↩️</div>
            <div class="dev-title-text">
                <h2>Solicitar Devolución</h2>
                <p>Estamos aquí para ayudarte 💜</p>
            </div>
        </div>
    </div>

    <div class="dev-bar"></div>

    <!-- PEDIDO -->
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
            <?= !empty($detalleDev) ? count($detalleDev) : 0 ?>
        </div>

    </div>

    <div class="row">

        <div class="col-lg-7">

            <form method="POST" action="index.php?action=procesar_devolucion" id="form-dev">

                <input type="hidden" name="id_pedido" value="<?= $pedidoDev['id_pedido'] ?? '' ?>">

                <div class="dev-section">
                    <div class="dev-section-title">Selecciona productos</div>

                    <?php if (!empty($detalleDev)): ?>

                        <?php foreach ($detalleDev as $d): ?>

                        <div class="prod-row"
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
                        <p>No hay productos disponibles.</p>
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
    const row = chk?.parentElement;
    if(!chk) return;

    chk.checked = !chk.checked;

    if(row){
        row.classList.toggle('sel', chk.checked);
    }
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