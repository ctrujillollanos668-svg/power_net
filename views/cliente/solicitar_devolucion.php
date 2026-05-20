<?php
// Variables preparadas desde index.php (case 'solicitar_devolucion'):
// $pedidoDev, $detalleDev, $id_pedido_dev
if (!isset($pedidoDev, $detalleDev)) {
    header('Location: index.php?action=mis_pedidos');
    exit;
}
?>

<style>
/* ============ TOKENS ============ */
:root {
  --primary:#7c3aed;
  --primary-dark:#6d28d9;
  --bg:#f0f2f8;
  --card:#ffffff;
  --text:#1a1a2e;
  --muted:#6b7280;

  --radius-sm:10px;
  --radius-md:14px;
  --radius-lg:16px;

  --shadow:0 2px 12px rgba(0,0,0,.06);
}

/* ============ BASE ============ */
.dev-page {
  background:var(--bg);
  padding:32px 0 60px;
  margin:-1px;
}

/* ---- BACK ---- */
.dev-back {
  display:inline-flex;
  align-items:center;
  gap:6px;
  color:var(--primary);
  text-decoration:none;
  font-size:13px;
  font-weight:600;
  margin-bottom:20px;
  transition:opacity .2s;
}
.dev-back:hover { opacity:.7; }

/* ---- ICON BASE ---- */
.icon-box {
  display:flex;
  align-items:center;
  justify-content:center;
  background:#ede9fe;
  flex-shrink:0;
}

/* ---- HEADER ---- */
.dev-top {
  display:flex;
  align-items:center;
  justify-content:space-between;
  flex-wrap:wrap;
  gap:16px;
  margin-bottom:6px;
}

.dev-title-block {
  display:flex;
  align-items:center;
  gap:14px;
}

.dev-title-icon {
  width:52px;
  height:52px;
  border-radius:var(--radius-md);
  font-size:24px;
  composes: icon-box;
}

.dev-title-text h2 {
  font-size:22px;
  font-weight:900;
  color:var(--text);
  margin:0;
}
.dev-title-text p {
  font-size:13px;
  color:var(--muted);
  margin:2px 0 0;
}

/* ---- STEPPER ---- */
.dev-stepper {
  display:flex;
  align-items:center;
  gap:0;
}
.dev-step-item {
  display:flex;
  align-items:center;
  gap:8px;
}
.dev-step-circle {
  width:32px;
  height:32px;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:13px;
  font-weight:800;
}
.dev-step-circle.active {
  background:var(--primary);
  color:#fff;
}
.dev-step-circle.inactive {
  background:#e5e7eb;
  color:#9ca3af;
}
.dev-step-label {
  font-size:12px;
  font-weight:600;
}
.dev-step-label.active { color:var(--primary); }
.dev-step-label.inactive { color:#9ca3af; }

.dev-step-line {
  width:40px;
  height:2px;
  background:#e5e7eb;
  margin:0 4px;
}

/* ---- BAR ---- */
.dev-bar {
  height:4px;
  background:linear-gradient(90deg,var(--primary),#a78bfa);
  border-radius:4px;
  margin-bottom:24px;
}

/* ---- PEDIDO HEADER ---- */
.dev-pedido-header {
  background:var(--card);
  border-radius:var(--radius-lg);
  box-shadow:var(--shadow);
  padding:20px 28px;
  margin-bottom:20px;
  display:flex;
  align-items:center;
  gap:20px;
  flex-wrap:wrap;
}

.dev-pedido-icon {
  width:52px;
  height:52px;
  border-radius:var(--radius-md);
  font-size:26px;
  composes: icon-box;
}

.dev-pedido-id {
  flex:1;
  min-width:120px;
}
.dev-pedido-id h5 {
  font-size:17px;
  font-weight:800;
  color:var(--text);
  margin:0 0 4px;
}

.badge-entregado {
  display:inline-block;
  background:#d1fae5;
  color:#065f46;
  font-size:11px;
  font-weight:700;
  padding:3px 10px;
  border-radius:20px;
}

.dev-pedido-stat {
  display:flex;
  align-items:center;
  gap:10px;
  padding:0 20px;
  border-left:1px solid #f3f4f6;
}

.dev-pedido-stat .stat-icon {
  font-size:20px;
  color:var(--primary);
}

.dev-pedido-stat .stat-info .stat-label {
  font-size:11px;
  color:#9ca3af;
  font-weight:600;
}
.dev-pedido-stat .stat-info .stat-value {
  font-size:15px;
  font-weight:800;
  color:var(--text);
}
.dev-pedido-stat .stat-info .stat-value.green {
  color:#10b981;
}

/* ---- SECTION ---- */
.dev-section {
  background:var(--card);
  border-radius:var(--radius-lg);
  box-shadow:var(--shadow);
  padding:24px;
  margin-bottom:16px;
}

.dev-section-title {
  display:flex;
  align-items:center;
  gap:10px;
  font-size:15px;
  font-weight:800;
  color:var(--text);
  margin-bottom:6px;
}

.dev-section-title .s-icon {
  width:32px;
  height:32px;
  border-radius:8px;
  composes: icon-box;
  font-size:15px;
}

.dev-section-sub {
  font-size:12px;
  color:#9ca3af;
  margin-bottom:18px;
  margin-left:42px;
}

/* ---- PRODUCT ROW ---- */
.prod-row {
  border:2px solid #e5e7eb;
  border-radius:12px;
  padding:14px 16px;
  margin-bottom:10px;
  cursor:pointer;
  transition:all .25s ease;
  display:flex;
  align-items:center;
  gap:14px;
}

.prod-row:hover {
  border-color:#a78bfa;
  background:#fcfaff;
}

.prod-row.sel {
  border-color:var(--primary);
  background:#faf5ff;
  box-shadow:0 6px 18px rgba(124,58,237,.15);
  transform:translateY(-1px);
}

.prod-row:focus-within {
  outline:3px solid rgba(124,58,237,.25);
  outline-offset:2px;
}

.prod-check {
  width:20px;
  height:20px;
  accent-color:var(--primary);
  cursor:pointer;
}

.prod-img-box {
  width:52px;
  height:52px;
  border-radius:10px;
  overflow:hidden;
  background:#f3f4f6;
  display:flex;
  align-items:center;
  justify-content:center;
}

.prod-img-box img {
  width:100%;
  height:100%;
  object-fit:cover;
}

.prod-img-box span {
  font-size:22px;
}

.prod-info {
  flex:1;
}

.prod-info .pname {
  font-weight:700;
  font-size:14px;
  color:var(--text);
}
.prod-info .pqty {
  font-size:12px;
  color:var(--muted);
  margin-top:2px;
}
.prod-info .pprice {
  font-size:11px;
  color:#9ca3af;
  margin-top:1px;
}

.prod-total {
  text-align:right;
}
.prod-total .pt-val {
  font-weight:800;
  font-size:15px;
  color:var(--primary);
}
.prod-total .pt-label {
  font-size:10px;
  color:#9ca3af;
  text-transform:uppercase;
  font-weight:600;
}

.prod-opciones {
  display:none;
  margin-top:14px;
  padding-top:14px;
  border-top:1px solid #f3f4f6;
}

.prod-row.sel .prod-opciones {
  display:block;
}

/* ---- INPUTS ---- */
.dev-label {
  font-size:12px;
  font-weight:700;
  color:#374151;
  margin-bottom:5px;
  display:block;
}

.dev-input {
  width:100%;
  padding:10px 14px;
  border:2px solid #e5e7eb;
  border-radius:10px;
  font-size:14px;
  background:#fafafa;
  transition:border-color .2s;
  outline:none;
}

.dev-input:focus {
  border-color:var(--primary);
  background:#fff;
}

/* ---- BUTTON ---- */
.dev-btn {
  width:100%;
  padding:16px;
  background:linear-gradient(135deg,var(--primary),var(--primary-dark));
  color:#fff;
  border:none;
  border-radius:14px;
  font-size:16px;
  font-weight:800;
  cursor:pointer;
  transition:all .2s;
  box-shadow:0 4px 16px rgba(124,58,237,.3);
  display:flex;
  align-items:center;
  justify-content:center;
  gap:8px;
}

.dev-btn:hover {
  transform:translateY(-2px);
  box-shadow:0 8px 24px rgba(124,58,237,.4);
}

.dev-btn:active {
  transform:scale(0.98);
}

/* ---- SIDEBAR ---- */
.dev-sidebar-card {
  background:var(--card);
  border-radius:var(--radius-lg);
  box-shadow:var(--shadow);
  padding:24px;
  margin-bottom:16px;
}

/* ---- RESPONSIVE ---- */
@media (max-width:768px) {
  .dev-top {
    flex-direction:column;
    align-items:flex-start;
  }

  .dev-stepper {
    flex-wrap:wrap;
    gap:10px;
  }

  .dev-pedido-header {
    flex-direction:column;
    align-items:flex-start;
    gap:12px;
  }

  .dev-pedido-stat {
    border-left:none;
    padding:0;
  }

  .prod-row {
    flex-wrap:wrap;
  }

  .prod-total {
    width:100%;
    text-align:left;
  }

  .dev-section-sub {
    margin-left:0;
  }
}
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

                    <?php
                    // Intentar obtener imagen del producto
                    require_once __DIR__ . '/../../models/Product.php';
                    $pModel = new Product();
                    ?>
                    <?php foreach ($detalleDev as $d): ?>
                    <?php
                    $imgs = $pModel->obtenerImagenes($d['id_producto']);
                    $img  = $imgs[0]['imagen'] ?? null;
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
<<<<<<< HEAD
                                <img src="<?= UPLOADS_URL ?>/<?= htmlspecialchars($img) ?>"
=======
                                <img src="/power-net/public/uploads/<?= htmlspecialchars($img) ?>"
>>>>>>> 5d405ce413be4185aafbe4bd95866b0cad3f7dc5
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
