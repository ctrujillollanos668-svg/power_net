<?php
// Variables preparadas desde index.php (case 'solicitar_devolucion'):
// $pedidoDev, $detalleDev, $id_pedido_dev
?>

<style>
/* ============ BASE ============ */
.dev-page { background:#f0f2f8; padding:32px 0 60px; margin:-1px; }

/* ---- BACK ---- */
.dev-back {
    display:inline-flex; align-items:center; gap:6px;
    color:#7c3aed; text-decoration:none; font-size:13px; font-weight:600;
    margin-bottom:20px; transition:opacity .2s;
}
.dev-back:hover { opacity:.7; }

/* ---- HEADER TÍTULO + STEPPER ---- */
.dev-top {
    display:flex; align-items:center; justify-content:space-between;
    margin-bottom:6px; flex-wrap:wrap; gap:16px;
}

.dev-title-block { display:flex; align-items:center; gap:14px; }

.dev-title-icon {
    width:52px; height:52px; background:#ede9fe; border-radius:14px;
    display:flex; align-items:center; justify-content:center; font-size:24px; flex-shrink:0;
}

.dev-title-text h2 { font-size:22px; font-weight:900; color:#1a1a2e; margin:0; }
.dev-title-text p  { font-size:13px; color:#6b7280; margin:2px 0 0; }

/* Stepper */
.dev-stepper { display:flex; align-items:center; gap:0; }
.dev-step-item { display:flex; align-items:center; gap:8px; }
.dev-step-circle {
    width:32px; height:32px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:13px; font-weight:800; flex-shrink:0;
}
.dev-step-circle.active { background:#7c3aed; color:#fff; }
.dev-step-circle.inactive { background:#e5e7eb; color:#9ca3af; }
.dev-step-label { font-size:12px; font-weight:600; }
.dev-step-label.active { color:#7c3aed; }
.dev-step-label.inactive { color:#9ca3af; }
.dev-step-line { width:40px; height:2px; background:#e5e7eb; margin:0 4px; }

/* Barra morada */
.dev-bar { height:4px; background:linear-gradient(90deg,#7c3aed,#a78bfa); border-radius:4px; margin-bottom:24px; }

/* ---- PEDIDO HEADER ---- */
.dev-pedido-header {
    background:#fff; border-radius:16px;
    box-shadow:0 2px 12px rgba(0,0,0,.06);
    padding:20px 28px; margin-bottom:20px;
    display:flex; align-items:center; gap:20px; flex-wrap:wrap;
}
.dev-pedido-icon {
    width:52px; height:52px; background:#ede9fe; border-radius:14px;
    display:flex; align-items:center; justify-content:center; font-size:26px; flex-shrink:0;
}
.dev-pedido-id { flex:1; min-width:120px; }
.dev-pedido-id h5 { font-size:17px; font-weight:800; color:#1a1a2e; margin:0 0 4px; }
.badge-entregado {
    display:inline-block; background:#d1fae5; color:#065f46;
    font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px;
}
.dev-pedido-stat { display:flex; align-items:center; gap:10px; padding:0 20px; border-left:1px solid #f3f4f6; }
.dev-pedido-stat .stat-icon { font-size:20px; color:#7c3aed; }
.dev-pedido-stat .stat-info .stat-label { font-size:11px; color:#9ca3af; font-weight:600; }
.dev-pedido-stat .stat-info .stat-value { font-size:15px; font-weight:800; color:#1a1a2e; }
.dev-pedido-stat .stat-info .stat-value.green { color:#10b981; }

/* ---- SECCIÓN ---- */
.dev-section { background:#fff; border-radius:16px; box-shadow:0 2px 12px rgba(0,0,0,.06); padding:24px; margin-bottom:16px; }
.dev-section-title {
    display:flex; align-items:center; gap:10px;
    font-size:15px; font-weight:800; color:#1a1a2e; margin-bottom:6px;
}
.dev-section-title .s-icon {
    width:32px; height:32px; background:#ede9fe; border-radius:8px;
    display:flex; align-items:center; justify-content:center; font-size:15px; flex-shrink:0;
}
.dev-section-sub { font-size:12px; color:#9ca3af; margin-bottom:18px; margin-left:42px; }

/* ---- PRODUCTO ROW ---- */
.prod-row {
    border:2px solid #e5e7eb; border-radius:12px; padding:14px 16px;
    margin-bottom:10px; cursor:pointer; transition:all .2s;
    display:flex; align-items:center; gap:14px;
}
.prod-row:hover { border-color:#c4b5fd; }
.prod-row.sel { border-color:#7c3aed; background:#faf5ff; }

.prod-check {
    width:20px; height:20px; accent-color:#7c3aed; flex-shrink:0; cursor:pointer;
}
.prod-img-box {
    width:52px; height:52px; border-radius:10px; overflow:hidden;
    background:#f3f4f6; flex-shrink:0; display:flex; align-items:center; justify-content:center;
}
.prod-img-box img { width:100%; height:100%; object-fit:cover; }
.prod-img-box span { font-size:22px; }

.prod-info { flex:1; }
.prod-info .pname { font-weight:700; font-size:14px; color:#1a1a2e; }
.prod-info .pqty  { font-size:12px; color:#6b7280; margin-top:2px; }
.prod-info .pprice { font-size:11px; color:#9ca3af; margin-top:1px; }

.prod-total { text-align:right; flex-shrink:0; }
.prod-total .pt-val { font-weight:800; font-size:15px; color:#7c3aed; }
.prod-total .pt-label { font-size:10px; color:#9ca3af; font-weight:600; text-transform:uppercase; }

.prod-opciones { display:none; margin-top:14px; padding-top:14px; border-top:1px solid #f3f4f6; }
.prod-row.sel .prod-opciones { display:block; }

/* ---- INPUTS ---- */
.dev-label { font-size:12px; font-weight:700; color:#374151; margin-bottom:5px; display:block; }
.dev-input {
    width:100%; padding:10px 14px; border:2px solid #e5e7eb;
    border-radius:10px; font-size:14px; background:#fafafa;
    transition:border-color .2s; outline:none;
}
.dev-input:focus { border-color:#7c3aed; background:#fff; }

/* Contador textarea */
.textarea-wrap { position:relative; }
.textarea-count { position:absolute; bottom:8px; right:12px; font-size:11px; color:#9ca3af; }

/* ---- BOTÓN ---- */
.dev-btn {
    width:100%; padding:16px;
    background:linear-gradient(135deg,#7c3aed,#6d28d9);
    color:#fff; border:none; border-radius:14px;
    font-size:16px; font-weight:800; cursor:pointer;
    transition:all .2s; box-shadow:0 4px 16px rgba(124,58,237,.3);
    display:flex; align-items:center; justify-content:center; gap:8px;
}
.dev-btn:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(124,58,237,.4); }

.dev-btn-note { text-align:center; font-size:12px; color:#9ca3af; margin-top:10px; }

/* ---- ERROR ---- */
.dev-error {
    display:none; background:#fef2f2; border:2px solid #fca5a5;
    border-radius:10px; padding:12px 16px; color:#dc2626;
    font-size:13px; font-weight:600; margin-bottom:12px;
}

/* ---- SIDEBAR ---- */
.dev-sidebar-card {
    background:#fff; border-radius:16px;
    box-shadow:0 2px 12px rgba(0,0,0,.06); padding:24px; margin-bottom:16px;
}

.dev-sidebar-title { font-size:16px; font-weight:800; color:#1a1a2e; margin-bottom:20px; }

.dev-benefit {
    display:flex; align-items:flex-start; gap:14px; margin-bottom:18px;
}
.dev-benefit:last-of-type { margin-bottom:0; }
.dev-benefit .b-icon {
    width:38px; height:38px; background:#ede9fe; border-radius:10px;
    display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;
}
.dev-benefit .b-text h6 { font-size:13px; font-weight:800; color:#1a1a2e; margin:0 0 2px; }
.dev-benefit .b-text p  { font-size:12px; color:#6b7280; margin:0; line-height:1.4; }

.dev-contact-box {
    background:#f9fafb; border-radius:12px; padding:16px; margin-top:16px;
}
.dev-contact-box h6 {
    display:flex; align-items:center; gap:8px;
    font-size:13px; font-weight:800; color:#1a1a2e; margin-bottom:8px;
}
.dev-contact-box h6 .q-icon {
    width:26px; height:26px; background:#7c3aed; color:#fff;
    border-radius:50%; display:flex; align-items:center; justify-content:center;
    font-size:13px; font-weight:900; flex-shrink:0;
}
.dev-contact-box p { font-size:12px; color:#6b7280; margin-bottom:12px; }
.dev-contact-btn {
    width:100%; padding:10px; background:#fff; color:#7c3aed;
    border:2px solid #7c3aed; border-radius:10px; font-size:13px; font-weight:700;
    cursor:pointer; transition:all .2s; display:flex; align-items:center; justify-content:center; gap:6px;
}
.dev-contact-btn:hover { background:#7c3aed; color:#fff; }
</style>

<div class="dev-page">
<div class="container-fluid" style="padding:0 40px;">

    <!-- VOLVER -->
    <a href="index.php?action=mis_pedidos" class="dev-back">← Volver a mis pedidos</a>

    <!-- TÍTULO + STEPPER -->
    <div class="dev-top">
        <div class="dev-title-block">
            <div class="dev-title-icon">↩️</div>
            <div class="dev-title-text">
                <h2>Solicitar Devolución</h2>
                <p>Estamos aquí para ayudarte 💜</p>
            </div>
        </div>
        <div class="dev-stepper">
            <div class="dev-step-item">
                <div class="dev-step-circle active">1</div>
                <span class="dev-step-label active">Seleccionar</span>
            </div>
            <div class="dev-step-line"></div>
            <div class="dev-step-item">
                <div class="dev-step-circle inactive">2</div>
                <span class="dev-step-label inactive">Motivo</span>
            </div>
            <div class="dev-step-line"></div>
            <div class="dev-step-item">
                <div class="dev-step-circle inactive">3</div>
                <span class="dev-step-label inactive">Confirmar</span>
            </div>
        </div>
    </div>

    <!-- BARRA MORADA -->
    <div class="dev-bar"></div>

    <!-- PEDIDO HEADER -->
    <div class="dev-pedido-header">
        <div class="dev-pedido-icon">📦</div>
        <div class="dev-pedido-id">
            <h5>Pedido #<?= str_pad($pedidoDev['id_pedido'], 6, '0', STR_PAD_LEFT) ?></h5>
            <span class="badge-entregado">Entregado</span>
        </div>
        <div class="dev-pedido-stat">
            <span class="stat-icon">📅</span>
            <div class="stat-info">
                <div class="stat-label">Fecha de compra</div>
                <div class="stat-value">
                    <?php
                    $fp = $pedidoDev['fecha_pedido'] ?? $pedidoDev['fecha_pago'] ?? null;
                    echo $fp ? date('d/m/Y', strtotime($fp)) : 'N/A';
                    ?>
                </div>
            </div>
        </div>
        <div class="dev-pedido-stat">
            <span class="stat-icon">💳</span>
            <div class="stat-info">
                <div class="stat-label">Total pagado</div>
                <div class="stat-value green">$<?= number_format($pedidoDev['total_pedido'], 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="dev-pedido-stat">
            <span class="stat-icon">🛍️</span>
            <div class="stat-info">
                <div class="stat-label">Productos</div>
                <div class="stat-value"><?= count($detalleDev) ?> <?= count($detalleDev) === 1 ? 'producto' : 'productos' ?></div>
            </div>
        </div>
    </div>

    <!-- LAYOUT DOS COLUMNAS -->
    <div class="row g-4">

        <!-- IZQUIERDA: FORMULARIO -->
        <div class="col-lg-7">

            <form method="POST" action="index.php?action=procesar_devolucion" id="form-dev">
                <input type="hidden" name="id_pedido" value="<?= $pedidoDev['id_pedido'] ?>">

                <!-- PASO 1: PRODUCTOS -->
                <div class="dev-section">
                    <div class="dev-section-title">
                        <div class="s-icon">🛍️</div>
                        1. Selecciona los productos a devolver
                    </div>
                    <div class="dev-section-sub">Elige el producto que deseas devolver y cuéntanos el motivo.</div>

                    <?php foreach ($detalleDev as $d): ?>
                    <?php
                    // Intentar obtener imagen del producto
                    require_once __DIR__ . '/../../models/Product.php';
                    $pModel = new Product();
                    $imgs   = $pModel->obtenerImagenes($d['id_producto']);
                    $img    = $imgs[0]['imagen'] ?? null;
                    ?>
                    <div class="prod-row" id="row_<?= $d['id_producto'] ?>"
                         onclick="toggleProd(<?= $d['id_producto'] ?>)">

                        <input type="checkbox"
                               class="prod-check"
                               name="productos[<?= $d['id_producto'] ?>][seleccionado]"
                               value="1"
                               id="chk_<?= $d['id_producto'] ?>"
                               onclick="event.stopPropagation();"
                               onchange="toggleProd(<?= $d['id_producto'] ?>)">

                        <div class="prod-img-box">
                            <?php if ($img): ?>
                                <img src="/power-net/public/uploads/<?= htmlspecialchars($img) ?>"
                                     alt="<?= htmlspecialchars($d['nombre'] ?? '') ?>">
                            <?php else: ?>
                                <span>📦</span>
                            <?php endif; ?>
                        </div>

                        <div class="prod-info">
                            <div class="pname"><?= htmlspecialchars($d['nombre'] ?? 'Producto') ?></div>
                            <div class="pqty">Cantidad comprada: <strong><?= $d['cantidad'] ?> unidades</strong></div>
                            <div class="pprice">$ <?= number_format($d['precio_unitario'], 0, ',', '.') ?> c/u</div>
                        </div>

                        <div class="prod-total">
                            <div class="pt-val">$<?= number_format($d['subtotal'], 0, ',', '.') ?></div>
                            <div class="pt-label">Total</div>
                        </div>

                        <!-- Opciones al seleccionar -->
                        <div class="prod-opciones" style="width:100%;">
                            <div class="row g-3">
                                <div class="col-4">
                                    <label class="dev-label">Cantidad a devolver</label>
                                    <input type="number"
                                           name="productos[<?= $d['id_producto'] ?>][cantidad]"
                                           class="dev-input" value="<?= $d['cantidad'] ?>"
                                           min="1" max="<?= $d['cantidad'] ?>"
                                           onclick="event.stopPropagation()">
                                </div>
                                <div class="col-8">
                                    <label class="dev-label">Motivo</label>
                                    <select name="productos[<?= $d['id_producto'] ?>][motivo]"
                                            class="dev-input" onclick="event.stopPropagation()">
                                        <option value="">Selecciona un motivo</option>
                                        <option value="Producto defectuoso">🔧 Producto defectuoso</option>
                                        <option value="No coincide con la descripción">📝 No coincide con la descripción</option>
                                        <option value="Llegó dañado">📦 Llegó dañado</option>
                                        <option value="Producto incorrecto">❌ Producto incorrecto</option>
                                        <option value="Ya no lo necesito">🙅 Ya no lo necesito</option>
                                        <option value="Otro">💬 Otro</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- PASO 2: MOTIVO GENERAL -->
                <div class="dev-section">
                    <div class="dev-section-title">
                        <div class="s-icon">🏷️</div>
                        2. Motivo de devolución
                    </div>
                    <div class="dev-section-sub">¿Por qué deseas devolver este producto?</div>

                    <select name="motivo_select" class="dev-input mb-3">
                        <option value="">Selecciona un motivo</option>
                        <option value="Producto defectuoso">🔧 Producto defectuoso</option>
                        <option value="No coincide con la descripción">📝 No coincide con la descripción</option>
                        <option value="Llegó dañado">📦 Llegó dañado</option>
                        <option value="Producto incorrecto">❌ Producto incorrecto</option>
                        <option value="Ya no lo necesito">🙅 Ya no lo necesito</option>
                        <option value="Otro">💬 Otro</option>
                    </select>
                </div>

                <!-- PASO 3: COMENTARIOS -->
                <div class="dev-section">
                    <div class="dev-section-title">
                        <div class="s-icon">💬</div>
                        3. Comentarios adicionales
                        <span style="font-size:12px;color:#9ca3af;font-weight:400;">(opcional)</span>
                    </div>
                    <div class="dev-section-sub">Cuéntanos más detalles sobre lo que sucedió.</div>

                    <div class="textarea-wrap">
                        <textarea name="motivo_general" class="dev-input" rows="4"
                                  placeholder="Describe el problema con más detalle..."
                                  maxlength="500"
                                  oninput="document.getElementById('char-count').textContent = this.value.length + '/500'"></textarea>
                        <span class="textarea-count" id="char-count">0/500</span>
                    </div>
                </div>

                <!-- ERROR -->
                <div class="dev-error" id="dev-error">
                    ⚠️ Debes seleccionar al menos un producto para continuar.
                </div>

                <!-- BOTÓN -->
                <button type="button" class="dev-btn" onclick="validarYEnviar()">
                    ↩️ Enviar solicitud de devolución
                </button>
                <div class="dev-btn-note">🔒 Tu solicitud será revisada y te contactaremos pronto.</div>

            </form>
        </div>

        <!-- DERECHA: SIDEBAR -->
        <div class="col-lg-5">

            <div class="dev-sidebar-card">
                <div class="dev-sidebar-title">Tu satisfacción es nuestra prioridad</div>

                <div class="dev-benefit">
                    <div class="b-icon">⚡</div>
                    <div class="b-text">
                        <h6>Proceso fácil y rápido</h6>
                        <p>Solicita tu devolución en solo unos pasos.</p>
                    </div>
                </div>

                <div class="dev-benefit">
                    <div class="b-icon">🔒</div>
                    <div class="b-text">
                        <h6>Reembolso seguro</h6>
                        <p>Te reembolsaremos tu dinero de forma segura al mismo método de pago.</p>
                    </div>
                </div>

                <div class="dev-benefit">
                    <div class="b-icon">🎧</div>
                    <div class="b-text">
                        <h6>Atención personalizada</h6>
                        <p>Nuestro equipo está aquí para ayudarte en todo momento.</p>
                    </div>
                </div>

                <div class="dev-benefit">
                    <div class="b-icon">📅</div>
                    <div class="b-text">
                        <h6>30 días para devolver</h6>
                        <p>Tienes hasta 30 días desde la entrega para solicitar tu devolución.</p>
                    </div>
                </div>

                <div class="dev-contact-box">
                    <h6>
                        <span class="q-icon">?</span>
                        ¿Tienes dudas?
                    </h6>
                    <p>Contáctanos y estaremos encantados de ayudarte.</p>
                    <button class="dev-contact-btn">
                        🎧 Contactar soporte
                    </button>
                </div>
            </div>

        </div>

    </div>
</div>
</div>

<script>
function toggleProd(id) {
    const row = document.getElementById('row_' + id);
    const chk = document.getElementById('chk_' + id);
    if (chk.checked) {
        row.classList.add('sel');
    } else {
        row.classList.remove('sel');
        chk.checked = false;
    }
}

function validarYEnviar() {
    const checks = document.querySelectorAll('.prod-check:checked');
    const error  = document.getElementById('dev-error');
    if (checks.length === 0) {
        error.style.display = 'block';
        error.scrollIntoView({ behavior:'smooth', block:'center' });
        return;
    }
    error.style.display = 'none';
    document.getElementById('form-dev').submit();
}
</script>
