<?php
// Cargar datos del cliente y métodos de pago para mostrarlos en el perfil
require_once __DIR__ . '/../../models/Cliente.php';
require_once __DIR__ . '/../../models/MetodoPago.php';

$clientePerfilModel = new Cliente();
$metodoPerfilModel  = new MetodoPago();

$id_usuario_perfil  = $_SESSION['usuario']['id'];
$clientePerfil      = $clientePerfilModel->obtenerPorUsuario($id_usuario_perfil);
$id_cliente_perfil  = $clientePerfil['id_cliente'] ?? null;
$metodosPerfil      = $id_cliente_perfil ? $metodoPerfilModel->obtenerPorCliente($id_cliente_perfil) : [];
$direccionPerfil    = trim($clientePerfil['direccion'] ?? '');

// Tab activo (por defecto: datos)
$tabActivo = $_GET['tab'] ?? 'datos';
?>

<style>
/* ============ PERFIL ============ */
.perfil-wrap { background:#f4f6fb; min-height:100vh; padding:40px 0 60px; }

/* Avatar */
.perfil-avatar {
    width: 80px; height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7c3aed, #1a1a2e);
    color: #fff;
    font-size: 28px;
    font-weight: 900;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* Header del perfil */
.perfil-header {
    background: #fff;
    border-radius: 16px;
    padding: 28px 32px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 24px;
}

.perfil-header-info h4 { font-weight: 800; color: #1a1a2e; margin: 0 0 4px; }
.perfil-header-info p  { color: #6b7280; font-size: 14px; margin: 0; }

/* Tabs */
.perfil-tabs {
    display: flex;
    gap: 4px;
    background: #fff;
    border-radius: 16px;
    padding: 8px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    margin-bottom: 24px;
    flex-wrap: wrap;
}

.perfil-tab {
    flex: 1;
    min-width: 120px;
    padding: 12px 16px;
    border: none;
    border-radius: 10px;
    background: transparent;
    color: #6b7280;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    text-align: center;
    transition: all .2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.perfil-tab:hover { background: #f3f4f6; color: #1a1a2e; }
.perfil-tab.activo { background: #1a1a2e; color: #fff; }

/* Sección */
.perfil-section {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    overflow: hidden;
}

.perfil-section-header {
    background: #1a1a2e;
    color: #fff;
    padding: 16px 28px;
    font-size: 16px;
    font-weight: 700;
}

.perfil-section-body { padding: 28px; }

/* Inputs */
.p-label { font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 6px; display: block; }
.p-input {
    width: 100%;
    padding: 11px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    background: #fafafa;
    transition: border-color .2s;
    outline: none;
}
.p-input:focus { border-color: #7c3aed; background: #fff; }

/* Botón guardar */
.p-btn {
    padding: 12px 28px;
    background: #1a1a2e;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: background .2s;
}
.p-btn:hover { background: #7c3aed; }

/* Método de pago card */
.metodo-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 10px;
    gap: 12px;
}

.metodo-card-info { display: flex; align-items: center; gap: 14px; flex: 1; }
.metodo-card-icon { font-size: 26px; }
.metodo-card-tipo { font-weight: 800; font-size: 13px; color: #1a1a2e; text-transform: uppercase; }
.metodo-card-num  { font-size: 14px; color: #374151; font-family: monospace; letter-spacing: 1px; }
.metodo-card-tit  { font-size: 12px; color: #9ca3af; }

.btn-icon {
    width: 36px; height: 36px; border: none; border-radius: 8px;
    font-size: 15px; cursor: pointer; display: flex; align-items: center;
    justify-content: center; transition: all .2s;
}
.btn-icon-edit { background: #eff6ff; color: #2563eb; }
.btn-icon-edit:hover { background: #2563eb; color: #fff; }
.btn-icon-del  { background: #fef2f2; color: #dc2626; }
.btn-icon-del:hover  { background: #dc2626; color: #fff; }

/* Dirección card */
.dir-card-perfil {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 2px solid #86efac;
    background: #f0fdf4;
    border-radius: 12px;
    padding: 16px 18px;
    gap: 12px;
}

.dir-vacia {
    border: 2px dashed #e5e7eb;
    border-radius: 12px;
    padding: 24px;
    text-align: center;
    color: #9ca3af;
    font-size: 14px;
}

/* Seguridad */
.pass-strength { height: 4px; border-radius: 4px; margin-top: 6px; transition: all .3s; }
</style>

<div class="perfil-wrap">
<div class="container" style="max-width:860px;">

    <!-- HEADER DEL PERFIL -->
    <div class="perfil-header">
        <div class="perfil-avatar">
            <?= strtoupper(substr($_SESSION['usuario']['nombre'], 0, 2)) ?>
        </div>
        <div class="perfil-header-info">
            <h4><?= htmlspecialchars($_SESSION['usuario']['nombre']) ?></h4>
            <p><?= htmlspecialchars($_SESSION['usuario']['email'] ?? '') ?></p>
            <p style="font-size:12px;color:#10b981;margin-top:4px;">✅ Cuenta activa</p>
        </div>
    </div>

    <!-- TABS DE NAVEGACIÓN -->
    <div class="perfil-tabs">
        <a href="index.php?action=mi_perfil&tab=datos"
           class="perfil-tab <?= $tabActivo === 'datos' ? 'activo' : '' ?>">
            👤 Datos
        </a>
        <a href="index.php?action=mi_perfil&tab=direccion"
           class="perfil-tab <?= $tabActivo === 'direccion' ? 'activo' : '' ?>">
            📍 Dirección
        </a>
        <a href="index.php?action=mi_perfil&tab=metodos"
           class="perfil-tab <?= $tabActivo === 'metodos' ? 'activo' : '' ?>">
            💳 Métodos de pago
        </a>
        <a href="index.php?action=mi_perfil&tab=seguridad"
           class="perfil-tab <?= $tabActivo === 'seguridad' ? 'activo' : '' ?>">
            🔒 Seguridad
        </a>
    </div>

    <!-- ========================
         TAB: DATOS
    ======================== -->
    <?php if ($tabActivo === 'datos'): ?>
    <div class="perfil-section">
        <div class="perfil-section-header">👤 Datos de tu cuenta</div>
        <div class="perfil-section-body">
            <form method="POST" action="index.php?action=actualizar_perfil">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="p-label">Nombre completo</label>
                        <input type="text" name="nombre" class="p-input"
                               value="<?= htmlspecialchars($_SESSION['usuario']['nombre'] ?? '') ?>"
                               required>
                    </div>
                    <div class="col-md-6">
                        <label class="p-label">Correo electrónico</label>
                        <input type="email" name="correo" class="p-input"
                               value="<?= htmlspecialchars($_SESSION['usuario']['email'] ?? '') ?>"
                               required>
                    </div>
                </div>
                <button type="submit" class="p-btn">💾 Guardar cambios</button>
            </form>
        </div>
    </div>

    <!-- ========================
         TAB: DIRECCIÓN
    ======================== -->
    <?php elseif ($tabActivo === 'direccion'): ?>
    <div class="perfil-section">
        <div class="perfil-section-header">📍 Dirección de envío</div>
        <div class="perfil-section-body">

            <?php if ($direccionPerfil): ?>
                <!-- Dirección guardada con botones editar y eliminar -->
                <div class="dir-card-perfil mb-4">
                    <div style="display:flex;align-items:center;gap:14px;flex:1;">
                        <span style="font-size:28px;">🏠</span>
                        <div>
                            <div style="font-weight:700;font-size:15px;color:#1a1a2e;">
                                <?= htmlspecialchars($direccionPerfil) ?>
                            </div>
                            <div style="font-size:12px;color:#166534;margin-top:2px;">✅ Dirección registrada</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;">
                        <!-- Lápiz: abre el modal de edición prellenado -->
                        <button type="button" class="btn-icon btn-icon-edit"
                                title="Editar dirección"
                                onclick="abrirEditarDirPerfil('<?= htmlspecialchars($direccionPerfil, ENT_QUOTES) ?>')">
                            ✏️
                        </button>
                        <!-- Canastilla: confirma antes de eliminar -->
                        <button type="button" class="btn-icon btn-icon-del"
                                title="Eliminar dirección"
                                onclick="confirmarEliminarDirPerfil()">
                            🗑️
                        </button>
                    </div>
                </div>

                <!-- Botón para agregar nueva dirección (reemplaza la actual) -->
                <button type="button" class="p-btn" style="background:#7c3aed;"
                        onclick="document.getElementById('form-nueva-dir').style.display='block';this.style.display='none'">
                    ➕ Agregar / cambiar dirección
                </button>

                <!-- Formulario oculto para agregar/cambiar -->
                <div id="form-nueva-dir" style="display:none;margin-top:20px;">
                    <form method="POST" action="index.php?action=guardar_direccion">
                        <div class="mb-3">
                            <label class="p-label">Dirección <span style="color:#dc2626;">*</span></label>
                            <input type="text" name="direccion" class="p-input"
                                   placeholder="Calle, número, barrio" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="p-label">Ciudad</label>
                                <input type="text" name="ciudad" class="p-input" placeholder="Ciudad">
                            </div>
                            <div class="col-6">
                                <label class="p-label">Departamento</label>
                                <input type="text" name="departamento" class="p-input" placeholder="Departamento">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="p-label">Teléfono</label>
                            <input type="text" name="telefono" class="p-input" placeholder="300 123 4567">
                        </div>
                        <div style="display:flex;gap:12px;">
                            <button type="submit" class="p-btn">📦 Guardar dirección</button>
                            <button type="button" class="p-btn" style="background:#6b7280;"
                                    onclick="document.getElementById('form-nueva-dir').style.display='none';document.querySelector('.p-btn[onclick*=form-nueva-dir]').style.display='inline-flex'">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>

            <?php else: ?>
                <!-- Sin dirección: mostrar formulario directamente -->
                <div class="dir-vacia mb-4">
                    <div style="font-size:2rem;margin-bottom:8px;">📍</div>
                    <p>No tienes una dirección registrada.</p>
                </div>

                <form method="POST" action="index.php?action=guardar_direccion">
                    <div class="mb-3">
                        <label class="p-label">Dirección <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="direccion" class="p-input"
                               placeholder="Calle, número, barrio" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="p-label">Ciudad</label>
                            <input type="text" name="ciudad" class="p-input" placeholder="Ciudad">
                        </div>
                        <div class="col-6">
                            <label class="p-label">Departamento</label>
                            <input type="text" name="departamento" class="p-input" placeholder="Departamento">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="p-label">Teléfono</label>
                        <input type="text" name="telefono" class="p-input" placeholder="300 123 4567">
                    </div>
                    <button type="submit" class="p-btn">📦 Guardar dirección</button>
                </form>
            <?php endif; ?>

        </div>
    </div>

    <!-- ========================
         TAB: MÉTODOS DE PAGO
    ======================== -->
    <?php elseif ($tabActivo === 'metodos'): ?>
    <?php
    $metodosConfig  = require __DIR__ . '/../../views/admin/pago/MetodosPago.php';
    $metodosActivos = array_values(array_filter($metodosConfig, fn($m) => $m['activo']));
    ?>
    <div class="perfil-section">
        <div class="perfil-section-header">💳 Métodos de pago</div>
        <div class="perfil-section-body">

            <!-- Métodos guardados del cliente -->
            <?php if (!empty($metodosPerfil)): ?>
                <p style="font-size:13px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">
                    Mis métodos guardados
                </p>
                <?php foreach ($metodosPerfil as $m): ?>
                <div class="metodo-card" style="margin-bottom:10px;">
                    <div class="metodo-card-info">
                        <span class="metodo-card-icon">💳</span>
                        <div>
                            <div class="metodo-card-tipo"><?= htmlspecialchars($m['tipo']) ?></div>
                            <div class="metodo-card-num">•••• <?= htmlspecialchars(substr($m['numero'],-4)) ?></div>
                            <div class="metodo-card-tit"><?= htmlspecialchars($m['titular']) ?></div>
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;">
                        <button type="button" class="btn-icon btn-icon-del"
                                onclick="confirmarEliminarMetodo(<?= $m['id_metodo'] ?>)">🗑️</button>
                    </div>
                </div>
                <?php endforeach; ?>
                <hr class="my-4">
            <?php endif; ?>

            <!-- Métodos disponibles del admin para agregar -->
            <?php if (empty($metodosActivos)): ?>
                <div class="dir-vacia">
                    <div style="font-size:2rem;margin-bottom:8px;">💳</div>
                    <p>No hay métodos de pago disponibles por el momento.</p>
                </div>
            <?php else: ?>
                <p style="font-size:13px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">
                    Selecciona un método para agregar
                </p>
                <?php foreach ($metodosActivos as $idx => $m): ?>
                <div class="metodo-card" style="margin-bottom:10px;cursor:pointer;transition:border-color .2s;"
                     id="metodo-opcion-<?= $idx ?>"
                     onclick="seleccionarMetodo(<?= $idx ?>,'<?= htmlspecialchars($m['nombre'],ENT_QUOTES) ?>','<?= htmlspecialchars($m['icono']??'💰',ENT_QUOTES) ?>','<?= htmlspecialchars($m['instrucciones']??'',ENT_QUOTES) ?>')">
                    <div class="metodo-card-info">
                        <span class="metodo-card-icon"><?= $m['icono'] ?? '💰' ?></span>
                        <div>
                            <div class="metodo-card-tipo"><?= htmlspecialchars($m['nombre']) ?></div>
                            <?php if (!empty($m['descripcion'])): ?>
                            <div class="metodo-card-tit"><?= htmlspecialchars($m['descripcion']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <span style="font-size:18px;color:#d1d5db;" id="check-<?= $idx ?>">＋</span>
                </div>

                <!-- Formulario inline que aparece al seleccionar -->
                <div id="form-metodo-<?= $idx ?>" style="display:none;background:#f5f3ff;border:1.5px solid #c4b5fd;border-radius:12px;padding:18px;margin-bottom:12px;">
                    <?php if (!empty($m['instrucciones'])): ?>
                    <div style="font-size:12px;color:#5b21b6;margin-bottom:14px;">
                        📋 <?= htmlspecialchars($m['instrucciones']) ?>
                    </div>
                    <?php endif; ?>
                    <form method="POST" action="index.php?action=guardar_metodo">
                        <input type="hidden" name="redirect" value="mi_perfil_metodos">
                        <input type="hidden" name="tipo" value="<?= htmlspecialchars($m['nombre']) ?>">
                        <div class="mb-3">
                            <label class="p-label">Tu número / cuenta</label>
                            <input type="text" name="numero" class="p-input"
                                   placeholder="Ej: 300 123 4567" required>
                        </div>
                        <div class="mb-3">
                            <label class="p-label">Tu nombre</label>
                            <input type="text" name="titular" class="p-input"
                                   placeholder="Nombre completo" required>
                        </div>
                        <div style="display:flex;gap:10px;">
                            <button type="submit" class="p-btn">💾 Guardar</button>
                            <button type="button" class="p-btn" style="background:#6b7280;"
                                    onclick="cerrarMetodo(<?= $idx ?>)">Cancelar</button>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>

    <script>
    function seleccionarMetodo(idx, nombre, icono, instrucciones) {
        // Cerrar todos los demás
        document.querySelectorAll('[id^="form-metodo-"]').forEach(f => f.style.display = 'none');
        document.querySelectorAll('[id^="check-"]').forEach(c => { c.textContent = '＋'; c.style.color = '#d1d5db'; });
        document.querySelectorAll('[id^="metodo-opcion-"]').forEach(c => c.style.borderColor = '#e5e7eb');

        const form  = document.getElementById('form-metodo-' + idx);
        const check = document.getElementById('check-' + idx);
        const card  = document.getElementById('metodo-opcion-' + idx);

        if (form.style.display === 'block') {
            form.style.display = 'none';
            check.textContent  = '＋';
            check.style.color  = '#d1d5db';
            card.style.borderColor = '#e5e7eb';
        } else {
            form.style.display = 'block';
            check.textContent  = '✓';
            check.style.color  = '#7c3aed';
            card.style.borderColor = '#7c3aed';
            form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }
    function cerrarMetodo(idx) {
        document.getElementById('form-metodo-' + idx).style.display = 'none';
        document.getElementById('check-' + idx).textContent  = '＋';
        document.getElementById('check-' + idx).style.color  = '#d1d5db';
        document.getElementById('metodo-opcion-' + idx).style.borderColor = '#e5e7eb';
    }
    </script>

    <!-- ========================
         TAB: SEGURIDAD
    ======================== -->
    <?php elseif ($tabActivo === 'seguridad'): ?>
    <div class="perfil-section">
        <div class="perfil-section-header">🔒 Seguridad</div>
        <div class="perfil-section-body">

            <p style="color:#6b7280;font-size:14px;margin-bottom:24px;">
                Cambia tu contraseña regularmente para mantener tu cuenta segura.
            </p>

            <form method="POST" action="index.php?action=cambiar_password">
                <div class="mb-3">
                    <label class="p-label">Contraseña actual</label>
                    <input type="password" name="password_actual" class="p-input"
                           placeholder="••••••••" required>
                </div>
                <div class="mb-3">
                    <label class="p-label">Nueva contraseña</label>
                    <input type="password" name="password_nueva" id="pass_nueva" class="p-input"
                           placeholder="Mínimo 8 caracteres" required
                           oninput="medirFuerza(this.value)">
                    <!-- Barra de fuerza de contraseña -->
                    <div class="pass-strength mt-2" id="pass-bar" style="background:#e5e7eb;width:100%;"></div>
                    <div id="pass-label" style="font-size:12px;color:#9ca3af;margin-top:4px;"></div>
                </div>
                <div class="mb-4">
                    <label class="p-label">Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirmar" id="pass_confirmar" class="p-input"
                           placeholder="Repite la contraseña" required
                           oninput="verificarCoincidencia()">
                    <div id="pass-match" style="font-size:12px;margin-top:4px;"></div>
                </div>
                <button type="submit" class="p-btn" id="btn-cambiar-pass">🔒 Cambiar contraseña</button>
            </form>

        </div>
    </div>
    <?php endif; ?>

</div>
</div>

<!-- MODAL EDITAR MÉTODO -->
<div class="modal fade" id="modalEditarMetodoPerfil" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:#1a1a2e;color:#fff;border:none;padding:20px 24px;">
                <h5 class="modal-title fw-bold">✏️ Editar método de pago</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=editar_metodo">
                <input type="hidden" name="redirect" value="mi_perfil_metodos">
                <div class="modal-body" style="padding:24px;">
                    <input type="hidden" name="id_metodo" id="pedit_id">
                    <div class="mb-3">
                        <label class="p-label">Tipo</label>
                        <select name="tipo" id="pedit_tipo" class="p-input" required>
                            <option value="tarjeta">💳 Tarjeta</option>
                            <option value="transferencia">🏦 Transferencia</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="p-label">Número</label>
                        <input type="text" name="numero" id="pedit_numero" class="p-input" maxlength="19" required>
                    </div>
                    <div class="mb-3">
                        <label class="p-label">Titular</label>
                        <input type="text" name="titular" id="pedit_titular" class="p-input" required>
                    </div>
                </div>
                <div class="modal-footer" style="border:none;padding:16px 24px;background:#f9fafb;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="p-btn">💾 Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirEditarMetodo(id, tipo, numero, titular) {
    document.getElementById('pedit_id').value      = id;
    document.getElementById('pedit_tipo').value    = tipo;
    document.getElementById('pedit_numero').value  = numero;
    document.getElementById('pedit_titular').value = titular;
    _abrirModal('modalEditarMetodoPerfil');
}

function confirmarEliminarMetodo(id) {
    Swal.fire({
        title: '¿Eliminar método?', text: 'Esta acción no se puede deshacer.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
    }).then(r => {
        if (r.isConfirmed) window.location.href = 'index.php?action=eliminar_metodo&id=' + id + '&redirect=mi_perfil_metodos';
    });
}

function confirmarEliminarDirPerfil() {
    Swal.fire({
        title: '¿Eliminar dirección?', text: 'Se borrará tu dirección de envío.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
    }).then(r => {
        if (r.isConfirmed) window.location.href = 'index.php?action=eliminar_direccion&redirect=mi_perfil_direccion';
    });
}

function abrirEditarDirPerfil(dir) {
    document.getElementById('modal_dir_actual').value = dir;
    _abrirModal('modalEditarDirPerfil');
}

function _abrirModal(id) {
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow    = '';
    document.body.style.paddingRight = '';

    const modalEl = document.getElementById(id);
    const prev = bootstrap.Modal.getInstance(modalEl);
    if (prev) prev.dispose();

    if (modalEl.parentElement !== document.body) {
        document.body.appendChild(modalEl);
    }

    const modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true, focus: true });
    modal.show();

    modalEl.addEventListener('shown.bs.modal', function handler() {
        const primer = modalEl.querySelector('input:not([type=hidden]), select, textarea');
        if (primer) { primer.focus(); primer.click(); }
        modalEl.removeEventListener('shown.bs.modal', handler);
    });
}
// Fuerza de contraseña
function medirFuerza(val) {
    const bar   = document.getElementById('pass-bar');
    const label = document.getElementById('pass-label');
    let fuerza  = 0;
    if (val.length >= 8)              fuerza++;
    if (/[A-Z]/.test(val))            fuerza++;
    if (/[0-9]/.test(val))            fuerza++;
    if (/[^A-Za-z0-9]/.test(val))     fuerza++;

    const colores = ['#ef4444','#f97316','#eab308','#22c55e'];
    const labels  = ['Muy débil','Débil','Buena','Fuerte'];
    const pct     = (fuerza / 4) * 100;

    bar.style.background = colores[fuerza - 1] || '#e5e7eb';
    bar.style.width      = pct + '%';
    label.textContent    = val.length ? labels[fuerza - 1] || '' : '';
    label.style.color    = colores[fuerza - 1] || '#9ca3af';
}

// Verificar coincidencia de contraseñas
function verificarCoincidencia() {
    const nueva     = document.getElementById('pass_nueva').value;
    const confirmar = document.getElementById('pass_confirmar').value;
    const match     = document.getElementById('pass-match');
    const btn       = document.getElementById('btn-cambiar-pass');

    if (!confirmar) { match.textContent = ''; return; }

    if (nueva === confirmar) {
        match.textContent = '✔ Las contraseñas coinciden';
        match.style.color = '#22c55e';
        btn.disabled      = false;
    } else {
        match.textContent = '✖ Las contraseñas no coinciden';
        match.style.color = '#ef4444';
        btn.disabled      = true;
    }
}
</script>

<!-- MODAL EDITAR DIRECCIÓN -->
<div class="modal fade" id="modalEditarDirPerfil" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:#1a1a2e;color:#fff;border:none;padding:20px 24px;">
                <h5 class="modal-title fw-bold">✏️ Editar dirección</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=guardar_direccion">
                <div class="modal-body" style="padding:24px;">
                    <div class="mb-3">
                        <label class="p-label">Dirección <span style="color:#dc2626;">*</span></label>
                        <input type="text" name="direccion" id="modal_dir_actual"
                               class="p-input" placeholder="Calle, número, barrio" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="p-label">Ciudad</label>
                            <input type="text" name="ciudad" class="p-input" placeholder="Ciudad">
                        </div>
                        <div class="col-6">
                            <label class="p-label">Departamento</label>
                            <input type="text" name="departamento" class="p-input" placeholder="Departamento">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="p-label">Teléfono</label>
                        <input type="text" name="telefono" class="p-input" placeholder="300 123 4567">
                    </div>
                </div>
                <div class="modal-footer" style="border:none;padding:16px 24px;background:#f9fafb;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="p-btn">💾 Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
