<?php
// Variables desde index.php (case 'mis_devoluciones'):
// $devolucionesCli, $devModelCli
?>

<div class="container mt-5 mb-5">
    <h4 class="fw-bold mb-4">↩️ Mis Devoluciones</h4>

    <?php if (empty($devolucionesCli)): ?>
        <div class="text-center py-5">
            <div style="font-size:3rem;">↩️</div>
            <h5 class="mt-3 text-muted">No tienes solicitudes de devolución</h5>
            <a href="index.php?action=mis_pedidos" class="btn btn-dark mt-2 rounded-pill px-4">Ver mis pedidos</a>
        </div>
    <?php else: ?>
        <div class="row g-3">
        <?php foreach ($devolucionesCli as $d): ?>
        <?php
        $est   = $d['estado'] ?? 'pendiente';
        $badge = match($est) {
            'pendiente'  => ['bg-warning text-dark', '⏳ Pendiente revisión'],
            'aprobada'   => ['bg-success',            '✅ Aprobada'],
            'rechazada'  => ['bg-danger',              '❌ Rechazada'],
            'completada' => ['bg-primary',             '💰 Reembolso procesado'],
            default      => ['bg-secondary',           ucfirst($est)]
        };
        ?>
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius:16px;overflow:hidden;">
                <!-- Header de la card -->
                <div style="background:#1a1a2e;color:#fff;padding:14px 24px;display:flex;justify-content:space-between;align-items:center;">
                    <span class="fw-bold">Devolución #<?= $d['id_devolucion'] ?> — Pedido #<?= $d['id_pedido'] ?></span>
                    <span class="badge <?= $badge[0] ?>"><?= $badge[1] ?></span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <div class="text-muted small">Fecha solicitud</div>
                            <div class="fw-semibold"><?= $d['fecha_devolucion'] ? date('d/m/Y', strtotime($d['fecha_devolucion'])) : '—' ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Monto solicitado</div>
                            <div class="fw-bold text-danger">$<?= number_format($d['monto_devolucion'], 0, ',', '.') ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Total del pedido</div>
                            <div class="fw-semibold">$<?= number_format($d['total_pedido'] ?? 0, 0, ',', '.') ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Estado pedido</div>
                            <div class="fw-semibold"><?= ucfirst($d['estado_pedido'] ?? '—') ?></div>
                        </div>
                    </div>

                    <!-- Mensaje de rechazo si aplica -->
                    <?php if ($est === 'rechazada' && !empty($d['motivo_rechazo'])): ?>
                    <div style="background:#fef2f2;border:2px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:12px;">
                        <strong style="color:#dc2626;">❌ Motivo del rechazo:</strong>
                        <p style="margin:4px 0 0;color:#374151;"><?= htmlspecialchars($d['motivo_rechazo']) ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if ($est === 'completada'): ?>
                    <div style="background:#d1fae5;border:2px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:12px;">
                        <strong style="color:#065f46;">💰 Tu reembolso fue procesado correctamente.</strong>
                    </div>
                    <?php endif; ?>

                    <!-- Línea de tiempo del estado -->
                    <div style="display:flex;align-items:center;gap:0;margin-top:8px;">
                        <?php
                        $pasos = ['pendiente' => '⏳ Pendiente', 'aprobada' => '✅ Aprobada', 'completada' => '💰 Reembolsada'];
                        $orden = ['pendiente', 'aprobada', 'completada'];
                        $posActual = array_search($est, $orden);
                        foreach ($pasos as $key => $label):
                            $pos     = array_search($key, $orden);
                            $activo  = $pos <= $posActual && $est !== 'rechazada';
                            $esActual = $key === $est;
                        ?>
                        <div style="display:flex;align-items:center;flex:1;">
                            <div style="text-align:center;flex:1;">
                                <div style="width:32px;height:32px;border-radius:50%;margin:0 auto 4px;
                                     background:<?= $activo ? '#7c3aed' : '#e5e7eb' ?>;
                                     color:<?= $activo ? '#fff' : '#9ca3af' ?>;
                                     display:flex;align-items:center;justify-content:center;font-size:14px;">
                                    <?= $activo ? '✓' : ($pos + 1) ?>
                                </div>
                                <div style="font-size:11px;color:<?= $activo ? '#7c3aed' : '#9ca3af' ?>;font-weight:<?= $esActual ? '700' : '400' ?>;">
                                    <?= $label ?>
                                </div>
                            </div>
                            <?php if ($key !== 'completada'): ?>
                            <div style="flex:1;height:2px;background:<?= $activo ? '#7c3aed' : '#e5e7eb' ?>;"></div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                        <?php if ($est === 'rechazada'): ?>
                        <div style="text-align:center;flex:1;">
                            <div style="width:32px;height:32px;border-radius:50%;margin:0 auto 4px;background:#dc2626;color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;">✗</div>
                            <div style="font-size:11px;color:#dc2626;font-weight:700;">❌ Rechazada</div>
                        </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
