<?php
// Variables preparadas desde index.php (case 'medios_pago')
$metodos    = $metodosMedios ?? [];
$cliente    = $clienteMedios ?? null;
$id_cliente = $cliente['id_cliente'] ?? null;
?>

<div class="container mt-5 mb-5">
    <h4 class="fw-bold mb-4">💳 Mis Métodos de Pago</h4>

    <div class="row g-4">

        <!-- IZQUIERDA: MÉTODOS GUARDADOS -->
        <div class="col-md-6">
            <div class="card p-4 shadow-sm">
                <h5 class="fw-bold mb-3">Métodos guardados</h5>

                <?php if (!empty($metodos)): ?>
                    <?php foreach ($metodos as $m): ?>
                        <div class="border rounded p-3 mb-2">
                            <strong><?= htmlspecialchars($m['tipo']) ?></strong><br>
                            <span class="text-muted"><?= htmlspecialchars($m['numero']) ?></span><br>
                            <small><?= htmlspecialchars($m['titular']) ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-warning">
                        No tienes métodos de pago guardados aún.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- DERECHA: AGREGAR NUEVO MÉTODO -->
        <div class="col-md-6">
            <div class="card p-4 shadow-sm">
                <h5 class="fw-bold mb-3">Añadir método de pago</h5>

                <?php if (!$id_cliente): ?>
                    <div class="alert alert-danger">
                        Debes completar tu perfil antes de agregar un método de pago.
                        <a href="index.php?action=datos_cuenta" class="alert-link">Completar perfil</a>
                    </div>
                <?php else: ?>
                    <form method="POST" action="index.php?action=guardar_metodo">
                        <input type="hidden" name="redirect" value="medios_pago">

                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <select name="tipo" class="form-select" required>
                                <option value="tarjeta">Tarjeta de crédito/débito</option>
                                <option value="transferencia">Transferencia bancaria</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Número</label>
                            <input type="text" name="numero" class="form-control"
                                   placeholder="Ej: 4111 1111 1111 1111" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Titular</label>
                            <input type="text" name="titular" class="form-control"
                                   placeholder="Nombre del titular" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            💾 Guardar método
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
