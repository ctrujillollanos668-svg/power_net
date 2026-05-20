<?php
// Todas las variables vienen preparadas desde index.php (case 'procesar_pago'):
// $clientePago, $id_cliente_pago, $metodosPago, $metodoModelPago
// $direccionPago, $tieneDireccion
// $itemsCarritoPago, $totalCarritoPago
if (!isset($itemsCarritoPago, $totalCarritoPago, $metodosPago, $direccionPago, $tieneDireccion)) {
    header('Location: index.php');
    exit;
}
?>

<style>
/* ── Checkout ── */
.checkout-wrap {
    background:#f4f6fb;
    min-height:100vh;
    padding:40px 0 60px;
}

.checkout-title {
    font-size:26px;
    font-weight:800;
    color:#1a1a2e;
    margin-bottom:28px;
}

.co-section {
    background:#fff;
    border-radius:16px;
    box-shadow:0 2px 12px rgba(0,0,0,.06);
    margin-bottom:24px;
    overflow:hidden;
}

.co-section-header {
    background:#1a1a2e;
    color:#fff;

    padding:14px 24px;

    font-size:15px;
    font-weight:700;

    display:flex;
    align-items:center;
    gap:8px;
}

.co-section-body {
    padding:24px;
}

/* ── Tabla resumen ── */
.resumen-table {
    width:100%;
    border-collapse:collapse;
}

.resumen-table th {
    font-size:12px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.5px;

    color:#6b7280;

    padding:0 0 12px;
    border-bottom:2px solid #f3f4f6;
}

.resumen-table td {
    padding:14px 0;
    border-bottom:1px solid #f3f4f6;
    vertical-align:middle;
}

.resumen-table tr:last-child td {
    border-bottom:none;
}

.prod-img {
    width:52px;
    height:52px;

    object-fit:cover;
    border-radius:10px;

    margin-right:12px;
    flex-shrink:0;
}

.prod-name {
    font-weight:600;
    font-size:14px;
    color:#1a1a2e;
}

.prod-price {
    font-size:12px;
    color:#9ca3af;
    margin-top:2px;
}

.resumen-total {
    display:flex;
    justify-content:space-between;
    align-items:center;

    padding:16px 24px;

    background:#f9fafb;
    border-top:2px solid #f3f4f6;
}

.resumen-total span:first-child {
    font-size:14px;
    font-weight:600;
    color:#6b7280;

    text-transform:uppercase;
    letter-spacing:.5px;
}

.resumen-total span:last-child {
    font-size:24px;
    font-weight:900;
    color:#7c3aed;
}

/* ── Dirección ── */
.dir-alert {
    display:flex;
    align-items:flex-start;
    gap:14px;

    padding:18px 20px;

    background:#fff7ed;
    border:2px solid #fb923c;
    border-radius:16px;

    margin-bottom:0;
}

.dir-alert strong {
    display:block;
    font-size:15px;
    color:#9a3412;
    margin-bottom:4px;
}

.dir-alert p {
    font-size:13px;
    color:#c2410c;
    margin:0;
}

/* ── Métodos ── */
.metodo-item {
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;

    border:2px solid #e5e7eb;
    border-radius:14px;

    padding:16px 18px;
    margin-bottom:12px;

    cursor:pointer;

    transition:
        border-color .2s,
        background .2s,
        box-shadow .2s;
}

.metodo-item:hover {
    border-color:#c4b5fd;
    box-shadow:0 4px 12px rgba(124,58,237,.08);
}

.metodo-item:has(input[type="radio"]:checked) {
    border-color:#7c3aed;
    background:#faf5ff;
    box-shadow:0 4px 16px rgba(124,58,237,.12);
}

.metodo-label {
    display:flex;
    align-items:center;
    gap:14px;

    flex:1;
    cursor:pointer;
    margin:0;
}

.metodo-label input[type="radio"] {
    width:20px;
    height:20px;

    accent-color:#7c3aed;
    flex-shrink:0;
}

.metodo-icon {
    font-size:28px;
    flex-shrink:0;
}

.metodo-info {
    display:flex;
    flex-direction:column;
    gap:3px;
}

.metodo-tipo {
    font-weight:800;
    font-size:13px;
    color:#1a1a2e;

    letter-spacing:.5px;
    text-transform:uppercase;
}

.metodo-numero {
    font-size:15px;
    color:#374151;

    font-family:'Courier New',monospace;
    letter-spacing:2px;
}

.metodo-titular {
    font-size:12px;
    color:#9ca3af;
}

.metodo-acciones {
    display:flex;
    gap:8px;
    flex-shrink:0;
}

.btn-mac {
    width:38px;
    height:38px;

    border:none;
    border-radius:10px;

    font-size:16px;

    cursor:pointer;

    display:flex;
    align-items:center;
    justify-content:center;

    transition:all .2s;
}

.btn-mac-edit {
    background:#eff6ff;
    color:#2563eb;
}

.btn-mac-edit:hover {
    background:#2563eb;
    color:#fff;
}

.btn-mac-del {
    background:#fef2f2;
    color:#dc2626;
}

.btn-mac-del:hover {
    background:#dc2626;
    color:#fff;
}

/* ── Botón pagar ── */
.btn-pagar {
    width:100%;
    padding:16px;

    background:linear-gradient(135deg,#7c3aed,#6d28d9);
    color:#fff;

    border:none;
    border-radius:14px;

    font-size:18px;
    font-weight:800;

    cursor:pointer;
    margin-top:20px;

    transition:all .2s;

    box-shadow:0 4px 16px rgba(124,58,237,.3);
}

.btn-pagar:hover {
    background:linear-gradient(135deg,#6d28d9,#5b21b6);
    transform:translateY(-2px);
    box-shadow:0 8px 24px rgba(124,58,237,.4);
}

.btn-pagar:disabled {
    background:#e5e7eb;
    color:#9ca3af;

    cursor:not-allowed;

    box-shadow:none;
    transform:none;
}

/* ── Formulario ── */
.form-label {
    font-size:13px;
    font-weight:700;
    color:#374151;

    margin-bottom:6px;
    display:block;
}

.form-control,
.form-select {
    border:2px solid #e5e7eb !important;
    border-radius:10px !important;

    padding:10px 14px !important;

    font-size:14px !important;

    transition:border-color .2s !important;
    background:#fafafa !important;
}

.form-control:focus,
.form-select:focus {
    border-color:#7c3aed !important;
    background:#fff !important;

    box-shadow:0 0 0 3px rgba(124,58,237,.1) !important;
}

.btn-guardar {
    width:100%;
    padding:13px;

    background:#1a1a2e;
    color:#fff;

    border:none;
    border-radius:12px;

    font-size:15px;
    font-weight:700;

    cursor:pointer;

    transition:all .2s;

    margin-top:8px;
}

.btn-guardar:hover {
    background:#7c3aed;
    transform:translateY(-1px);
}

.seguridad-badge {
    display:flex;
    align-items:center;
    justify-content:center;
    gap:6px;

    margin-top:16px;

    font-size:12px;
    color:#9ca3af;
    font-weight:500;
}
</style>

<div class="checkout-wrap">
<div class="container" style="max-width:1100px;">

    <div class="checkout-title">💳 Finalizar Compra</div>

    <div class="row g-4">

        <!-- IZQUIERDA -->
        <div class="col-lg-7">

            <!-- RESUMEN -->
            <div class="co-section">
                <div class="co-section-header">🛒 Resumen del pedido</div>
                <div class="co-section-body" style="padding-bottom:0;">
                    <table class="resumen-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th style="text-align:center;">Cant.</th>
                                <th style="text-align:right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($itemsCarritoPago as $item): ?>
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;">
                                        <?php if ($item['imagen']): ?>
<<<<<<< HEAD
                                            <img src="<?= UPLOADS_URL ?>/<?= htmlspecialchars($item['imagen']) ?>"
=======
                                            <img src="/power-net/public/uploads/<?= htmlspecialchars($item['imagen']) ?>"
>>>>>>> 5d405ce413be4185aafbe4bd95866b0cad3f7dc5
                                                 class="prod-img" alt="">
                                        <?php endif; ?>
                                        <div>
                                            <div class="prod-name"><?= htmlspecialchars($item['nombre']) ?></div>
                                            <div class="prod-price">$<?= number_format($item['precio'],0,',','.') ?> c/u</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align:center;">
                                    <span style="background:#f3f4f6;padding:4px 12px;border-radius:20px;font-weight:700;font-size:14px;">
                                        <?= $item['cantidad'] ?>
                                    </span>
                                </td>
                                <td style="text-align:right;font-weight:700;font-size:15px;color:#1a1a2e;">
                                    $<?= number_format($item['subtotal'],0,',','.') ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="resumen-total">
                    <span>Total a pagar</span>
                    <span>$<?= number_format($totalCarritoPago,0,',','.') ?></span>
                </div>
            </div>

            <!-- DIRECCIÓN -->
            <div class="co-section">
                <div class="co-section-header">📦 Dirección de envío</div>
                <div class="co-section-body">
                    <?php if ($tieneDireccion): ?>
                        <div class="metodo-item">
                            <div style="display:flex;align-items:center;gap:14px;flex:1;">
                                <span style="font-size:28px;">🏠</span>
                                <div>
                                    <div style="font-weight:700;font-size:14px;color:#1a1a2e;">
                                        <?= htmlspecialchars($direccionPago) ?>
                                    </div>
                                    <div style="font-size:12px;color:#10b981;font-weight:600;margin-top:2px;">
                                        ✅ Dirección confirmada
                                    </div>
                                </div>
                            </div>
                            <div class="metodo-acciones">
                                <button type="button" class="btn-mac btn-mac-edit" title="Editar"
                                        onclick="abrirEditarDir('<?= htmlspecialchars($direccionPago, ENT_QUOTES) ?>')">✏️</button>
                                <button type="button" class="btn-mac btn-mac-del" title="Eliminar"
                                        onclick="confirmarEliminarDir()">🗑️</button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="dir-alert">
                            <span style="font-size:28px;">⚠️</span>
                            <div>
                                <strong>Sin dirección de envío</strong>
                                <p>Agrega una dirección en el formulario de la derecha antes de pagar.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- MÉTODOS DE PAGO -->
            <div class="co-section">
                <div class="co-section-header">💳 Selecciona tu método de pago</div>
                <div class="co-section-body">

<<<<<<< HEAD
                    <?php
                    $metodosAdmin = $GLOBALS['metodosAdmin'] ?? [];
                    $iconosAdmin  = ['tarjeta'=>'💳','transferencia'=>'🏦','efectivo'=>'💵','nequi'=>'🟣','daviplata'=>'🔴','otro'=>'💰'];
                    $hayMetodos   = !empty($metodosPago) || !empty($metodosAdmin);
                    ?>

                    <?php if ($hayMetodos && $tieneDireccion): ?>
                        <form method="POST" action="index.php?action=confirmar_pago">
                            <!-- Campos ocultos para datos del método seleccionado -->
                            <input type="hidden" name="dato_numero"  id="hidden_numero">
                            <input type="hidden" name="dato_titular" id="hidden_titular">

                            <?php if (!empty($metodosAdmin)): ?>
                            <p style="font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">
                                Métodos disponibles
                            </p>
                            <?php foreach ($metodosAdmin as $idxA => $ma): ?>
                            <div class="metodo-item"
                                 onclick="document.getElementById('madmin_<?= $idxA ?>').click()">
                                <label class="metodo-label" for="madmin_<?= $idxA ?>"
                                       onclick="event.stopPropagation()">
                                    <input type="radio" name="metodo_guardado"
                                           value="admin_<?= $idxA ?>"
                                           id="madmin_<?= $idxA ?>" required>
                                    <span class="metodo-icon"><?= $ma['icono'] ?? '💰' ?></span>
                                    <div class="metodo-info">
                                        <span class="metodo-tipo"><?= htmlspecialchars($ma['nombre']) ?></span>
                                        <?php if ($ma['descripcion']): ?>
                                        <span class="metodo-titular"><?= htmlspecialchars($ma['descripcion']) ?></span>
                                        <?php endif; ?>
                                        <?php if ($ma['instrucciones']): ?>
                                        <span style="font-size:11px;color:#7c3aed;margin-top:2px;">
                                            📋 <?= htmlspecialchars($ma['instrucciones']) ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </label>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if (!empty($metodosPago)): ?>
                            <p style="font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin:16px 0 10px;">
                                Mis métodos guardados
                            </p>
=======
                    <?php if (!empty($metodosPago) && $tieneDireccion): ?>

                        <form method="POST" action="index.php?action=confirmar_pago">
>>>>>>> 5d405ce413be4185aafbe4bd95866b0cad3f7dc5
                            <?php foreach ($metodosPago as $m): ?>
                            <div class="metodo-item"
                                 onclick="document.getElementById('metodo_<?= $m['id_metodo'] ?>').click()">
                                <label class="metodo-label" for="metodo_<?= $m['id_metodo'] ?>"
                                       onclick="event.stopPropagation()">
                                    <input type="radio" name="metodo_guardado"
                                           value="<?= $m['id_metodo'] ?>"
                                           id="metodo_<?= $m['id_metodo'] ?>" required>
                                    <span class="metodo-icon"><?= $m['tipo'] === 'tarjeta' ? '💳' : '🏦' ?></span>
                                    <div class="metodo-info">
                                        <span class="metodo-tipo"><?= htmlspecialchars($m['tipo']) ?></span>
                                        <span class="metodo-numero">•••• •••• •••• <?= htmlspecialchars(substr($m['numero'],-4)) ?></span>
                                        <span class="metodo-titular"><?= htmlspecialchars($m['titular']) ?></span>
                                    </div>
                                </label>
                                <div class="metodo-acciones" onclick="event.stopPropagation()">
                                    <button type="button" class="btn-mac btn-mac-edit" title="Editar"
                                            onclick="abrirEditar(<?= $m['id_metodo'] ?>,'<?= htmlspecialchars($m['tipo'],ENT_QUOTES) ?>','<?= htmlspecialchars($m['numero'],ENT_QUOTES) ?>','<?= htmlspecialchars($m['titular'],ENT_QUOTES) ?>')">✏️</button>
                                    <button type="button" class="btn-mac btn-mac-del" title="Eliminar"
                                            onclick="confirmarEliminar(<?= $m['id_metodo'] ?>)">🗑️</button>
                                </div>
                            </div>
                            <?php endforeach; ?>
<<<<<<< HEAD
                            <?php endif; ?>

=======
>>>>>>> 5d405ce413be4185aafbe4bd95866b0cad3f7dc5
                            <button type="submit" class="btn-pagar">
                                ✅ Pagar $<?= number_format($totalCarritoPago,0,',','.') ?>
                            </button>
                        </form>

                    <?php elseif (!$tieneDireccion): ?>
                        <button class="btn-pagar" disabled>🔒 Agrega una dirección para continuar</button>

                    <?php else: ?>
                        <div style="text-align:center;padding:20px;color:#9ca3af;">
                            <div style="font-size:40px;margin-bottom:8px;">💳</div>
<<<<<<< HEAD
                            <p>No hay métodos de pago disponibles aún.</p>
=======
                            <p>No tienes métodos guardados.<br>Agrega uno en el panel de la derecha.</p>
>>>>>>> 5d405ce413be4185aafbe4bd95866b0cad3f7dc5
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>

        <!-- DERECHA -->
        <div class="col-lg-5">

<<<<<<< HEAD
            <!-- PANEL DATOS DEL MÉTODO SELECCIONADO -->
            <div class="co-section mb-4" id="panelDatosMetodo" style="display:none;">
                <div class="co-section-header" id="panelDatosHeader">📋 Datos del método</div>
                <div class="co-section-body">
                    <!-- Instrucciones del método -->
                    <div id="instruccionesMetodo" style="background:#f5f3ff;border:1.5px solid #c4b5fd;border-radius:10px;padding:14px 16px;margin-bottom:20px;font-size:13px;color:#5b21b6;display:none;"></div>

                    <!-- Formulario de datos del cliente para este método -->
                    <div id="formDatosMetodo">
                        <div class="mb-3">
                            <label class="form-label">Número / Cuenta <span style="color:#dc2626;">*</span></label>
                            <input type="text" id="dato_numero" class="form-control"
                                   placeholder="Ej: 300 123 4567" maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nombre del titular <span style="color:#dc2626;">*</span></label>
                            <input type="text" id="dato_titular" class="form-control"
                                   placeholder="Tu nombre completo">
                        </div>
                        <button type="button" class="btn-guardar" onclick="confirmarDatosMetodo()">
                            ✅ Confirmar datos
                        </button>
                    </div>

                    <!-- Confirmación de datos ingresados -->
                    <div id="datosConfirmados" style="display:none;">
                        <div style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:10px;padding:14px 16px;margin-bottom:16px;">
                            <div style="font-weight:700;color:#166534;margin-bottom:6px;">✅ Datos confirmados</div>
                            <div style="font-size:13px;color:#374151;" id="resumenDatos"></div>
                        </div>
                        <button type="button" class="btn-guardar" style="background:#6b7280;"
                                onclick="editarDatosMetodo()">
                            ✏️ Cambiar datos
                        </button>
                    </div>
=======
            <!-- MÉTODO DE PAGO -->
            <div class="co-section mb-4">
                <div class="co-section-header">➕ Agregar método de pago</div>
                <div class="co-section-body">
                    <form method="POST" action="index.php?action=guardar_metodo">
                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <select name="tipo" class="form-select" required>
                                <option value="tarjeta">💳 Tarjeta de crédito/débito</option>
                                <option value="transferencia">🏦 Transferencia bancaria</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Número de tarjeta / cuenta</label>
                            <input type="text" name="numero" class="form-control"
                                   placeholder="4111 1111 1111 1111" maxlength="19" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Titular</label>
                            <input type="text" name="titular" class="form-control"
                                   placeholder="Nombre como aparece en la tarjeta" required>
                        </div>
                        <button type="submit" class="btn-guardar">💾 Guardar método</button>
                    </form>
>>>>>>> 5d405ce413be4185aafbe4bd95866b0cad3f7dc5
                </div>
            </div>

            <!-- DIRECCIÓN -->
            <div class="co-section">
                <div class="co-section-header">📦 <?= $tieneDireccion ? 'Actualizar dirección' : 'Agregar dirección de envío' ?></div>
                <div class="co-section-body">

                    <?php if (!$tieneDireccion): ?>
                        <div style="background:#fff7ed;border:1.5px solid #fb923c;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#9a3412;">
                            ⚠️ <strong>Agrega una dirección</strong> para poder pagar.
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?action=guardar_direccion">
                        <div class="mb-3">
                            <label class="form-label">Dirección <span style="color:#dc2626;">*</span></label>
                            <input type="text" name="direccion" class="form-control"
                                   placeholder="Calle, número, barrio" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label">Ciudad</label>
                                <input type="text" name="ciudad" class="form-control" placeholder="Ciudad">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Departamento</label>
                                <input type="text" name="departamento" class="form-control" placeholder="Departamento">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" placeholder="300 123 4567">
                        </div>
                        <button type="submit" class="btn-guardar">📦 Guardar dirección</button>
                    </form>

                    <div class="seguridad-badge">🔒 Tus datos están protegidos con cifrado seguro</div>
                </div>
            </div>

        </div>
    </div>
</div>
</div>

<!-- MODAL EDITAR MÉTODO -->
<div class="modal fade" id="modalEditarMetodo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:#1a1a2e;color:#fff;border:none;padding:20px 24px;">
                <h5 class="modal-title fw-bold">✏️ Editar método de pago</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=editar_metodo">
                <div class="modal-body" style="padding:24px;">
                    <input type="hidden" name="id_metodo" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" id="edit_tipo" class="form-select" required>
                            <option value="tarjeta">💳 Tarjeta</option>
                            <option value="transferencia">🏦 Transferencia</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Número</label>
                        <input type="text" name="numero" id="edit_numero" class="form-control" maxlength="19" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Titular</label>
                        <input type="text" name="titular" id="edit_titular" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer" style="border:none;padding:16px 24px;background:#f9fafb;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">💾 Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR DIRECCIÓN -->
<div class="modal fade" id="modalEditarDir" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:#1a1a2e;color:#fff;border:none;padding:20px 24px;">
                <h5 class="modal-title fw-bold">✏️ Editar dirección</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=editar_direccion">
                <div class="modal-body" style="padding:24px;">
                    <div class="mb-3">
                        <label class="form-label">Dirección completa</label>
                        <input type="text" name="direccion" id="dir_direccion" class="form-control" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Ciudad</label>
                            <input type="text" name="ciudad" class="form-control" placeholder="Ciudad">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Departamento</label>
                            <input type="text" name="departamento" class="form-control" placeholder="Departamento">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" placeholder="300 123 4567">
                    </div>
                </div>
                <div class="modal-footer" style="border:none;padding:16px 24px;background:#f9fafb;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">💾 Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
<<<<<<< HEAD
// ── Datos de métodos admin desde PHP ──
const metodosAdmin = <?php
    $metodosConfig = require __DIR__ . '/../../views/admin/pago/MetodosPago.php';
    $activos = array_values(array_filter($metodosConfig, fn($m) => $m['activo']));
    echo json_encode($activos);
?>;

// Cuando el cliente selecciona un método admin, muestra el panel de datos
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name="metodo_guardado"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const val = this.value;
            const panel = document.getElementById('panelDatosMetodo');

            if (val.startsWith('admin_')) {
                const idx = parseInt(val.replace('admin_', ''));
                const metodo = metodosAdmin[idx];
                if (!metodo) return;

                // Actualizar header del panel
                document.getElementById('panelDatosHeader').textContent =
                    metodo.icono + ' ' + metodo.nombre;

                // Mostrar instrucciones si las hay
                const instrDiv = document.getElementById('instruccionesMetodo');
                if (metodo.instrucciones) {
                    instrDiv.textContent = '📋 ' + metodo.instrucciones;
                    instrDiv.style.display = 'block';
                } else {
                    instrDiv.style.display = 'none';
                }

                // Resetear formulario de datos
                document.getElementById('dato_numero').value  = '';
                document.getElementById('dato_titular').value = '';
                document.getElementById('formDatosMetodo').style.display  = 'block';
                document.getElementById('datosConfirmados').style.display = 'none';
                document.getElementById('hidden_numero').value  = '';
                document.getElementById('hidden_titular').value = '';

                panel.style.display = 'block';
                panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                // Método guardado del cliente — no necesita datos extra
                document.getElementById('panelDatosMetodo').style.display = 'none';
                document.getElementById('hidden_numero').value  = '';
                document.getElementById('hidden_titular').value = '';
            }
        });
    });

    // Validar que si hay método admin seleccionado, los datos estén confirmados
    const formPago = document.querySelector('form[action*="confirmar_pago"]');
    if (formPago) {
        formPago.addEventListener('submit', function(e) {
            const seleccionado = document.querySelector('input[name="metodo_guardado"]:checked');
            if (seleccionado && seleccionado.value.startsWith('admin_')) {
                const numero  = document.getElementById('hidden_numero').value.trim();
                const titular = document.getElementById('hidden_titular').value.trim();
                if (!numero || !titular) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Faltan tus datos',
                        text: 'Ingresa y confirma tu número y nombre para el método seleccionado.',
                    });
                }
            }
        });
    }
});

function confirmarDatosMetodo() {
    const numero  = document.getElementById('dato_numero').value.trim();
    const titular = document.getElementById('dato_titular').value.trim();
    if (!numero || !titular) {
        Swal.fire({ icon:'warning', title:'Campos requeridos', text:'Ingresa el número y el nombre del titular.' });
        return;
    }
    document.getElementById('hidden_numero').value  = numero;
    document.getElementById('hidden_titular').value = titular;
    document.getElementById('resumenDatos').innerHTML =
        '<strong>Número:</strong> ' + numero + '<br><strong>Titular:</strong> ' + titular;
    document.getElementById('formDatosMetodo').style.display  = 'none';
    document.getElementById('datosConfirmados').style.display = 'block';
}

function editarDatosMetodo() {
    document.getElementById('formDatosMetodo').style.display  = 'block';
    document.getElementById('datosConfirmados').style.display = 'none';
}

function abrirEditar(id, tipo, numero, titular) {
    document.getElementById('edit_id').value      = id;
    document.getElementById('edit_tipo').value    = tipo;
    document.getElementById('edit_numero').value  = numero;
    document.getElementById('edit_titular').value = titular;
    _abrirModal('modalEditarMetodo');
}
function confirmarEliminar(id) {
    Swal.fire({ title:'¿Eliminar método?', text:'Esta acción no se puede deshacer.', icon:'warning',
        showCancelButton:true, confirmButtonColor:'#dc2626', cancelButtonColor:'#6b7280',
        confirmButtonText:'Sí, eliminar', cancelButtonText:'Cancelar'
    }).then(r => { if (r.isConfirmed) window.location.href = 'index.php?action=eliminar_metodo&id=' + id; });
}
function abrirEditarDir(dir) {
    document.getElementById('dir_direccion').value = dir;
    _abrirModal('modalEditarDir');
=======
function abrirEditar(id, tipo, numero, titular) {
    document.getElementById('edit_id').value      = id;
    document.getElementById('edit_tipo').value    = tipo;
    document.getElementById('edit_numero').value  = numero;
    document.getElementById('edit_titular').value = titular;
    new bootstrap.Modal(document.getElementById('modalEditarMetodo')).show();
}
function confirmarEliminar(id) {
    Swal.fire({ title:'¿Eliminar método?', text:'Esta acción no se puede deshacer.', icon:'warning',
        showCancelButton:true, confirmButtonColor:'#dc2626', cancelButtonColor:'#6b7280',
        confirmButtonText:'Sí, eliminar', cancelButtonText:'Cancelar'
    }).then(r => { if (r.isConfirmed) window.location.href = 'index.php?action=eliminar_metodo&id=' + id; });
}
function abrirEditarDir(dir) {
    const modalEl = document.getElementById('modalEditarDir');
    document.getElementById('dir_direccion').value = dir;
    document.body.appendChild(modalEl);
    const modal = new bootstrap.Modal(modalEl);
    modalEl.addEventListener('shown.bs.modal', function handler() {
        document.getElementById('dir_direccion').focus();
        modalEl.removeEventListener('shown.bs.modal', handler);
    });
    modal.show();
>>>>>>> 5d405ce413be4185aafbe4bd95866b0cad3f7dc5
}
function confirmarEliminarDir() {
    Swal.fire({ title:'¿Eliminar dirección?', text:'Se borrará tu dirección de envío.', icon:'warning',
        showCancelButton:true, confirmButtonColor:'#dc2626', cancelButtonColor:'#6b7280',
        confirmButtonText:'Sí, eliminar', cancelButtonText:'Cancelar'
    }).then(r => { if (r.isConfirmed) window.location.href = 'index.php?action=eliminar_direccion'; });
}
<<<<<<< HEAD

function _abrirModal(id) {
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow    = '';
    document.body.style.paddingRight = '';
    const modalEl = document.getElementById(id);
    const prev = bootstrap.Modal.getInstance(modalEl);
    if (prev) prev.dispose();
    if (modalEl.parentElement !== document.body) document.body.appendChild(modalEl);
    const modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true, focus: true });
    modal.show();
    modalEl.addEventListener('shown.bs.modal', function handler() {
        const primer = modalEl.querySelector('input:not([type=hidden]), select, textarea');
        if (primer) { primer.focus(); primer.click(); }
        modalEl.removeEventListener('shown.bs.modal', handler);
    });
}
=======
>>>>>>> 5d405ce413be4185aafbe4bd95866b0cad3f7dc5
</script>
