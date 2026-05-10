<?php
$paginaActual = basename($_SERVER['PHP_SELF'], '.php');
$titulos = [
    'dashboard'   => 'Dashboard',
    'pedidos'     => 'Pedidos',
    'categorias'  => 'Categorías',
    'productos'   => 'Productos',
    'inventario'  => 'Inventario',
    'pago'        => 'Pagos',
    'ventas'      => 'Ventas',
    'devolucion'  => 'Devoluciones',
    'ofertas'     => 'Ofertas',
    'proveedores' => 'Proveedores',
    'envios'      => 'Envíos',
];
$tituloActual = $titulos[$paginaActual] ?? 'Panel Admin';
$iniciales    = strtoupper(substr($_SESSION['usuario']['nombre'] ?? 'A', 0, 2));
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
/* ===== RESET BASE ===== */
*, *::before, *::after { box-sizing: border-box; }

body {
    font-family: 'Inter', sans-serif !important;
    background: #f1f5f9 !important;
    color: #1e293b !important;
    font-size: 14px !important;
    margin: 0 !important;
}

/* ===== SIDEBAR ===== */
.admin-sidebar {
    width: 240px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    background: #1e293b;
    display: flex;
    flex-direction: column;
    z-index: 100;
    overflow-y: auto;
}

.sidebar-logo {
    padding: 22px 20px 18px;
    border-bottom: 1px solid rgba(255,255,255,.08);
    font-size: 17px;
    font-weight: 800;
    color: #fff;
}

.sidebar-logo span { color: #a78bfa; }

.sidebar-section {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: rgba(255,255,255,.3);
    padding: 16px 20px 6px;
}

.sidebar-nav { padding: 6px 10px; flex: 1; }

.s-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border-radius: 8px;
    color: rgba(255,255,255,.5);
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: all .15s;
    margin-bottom: 2px;
    position: relative;
}

.s-link:hover {
    background: rgba(255,255,255,.07);
    color: #fff;
}

.s-link.active {
    background: rgba(124,58,237,.2);
    color: #c4b5fd;
    font-weight: 600;
}

.s-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 25%;
    bottom: 25%;
    width: 3px;
    background: #7c3aed;
    border-radius: 0 4px 4px 0;
}

/* ===== TOPBAR ===== */
.admin-topbar {
    position: fixed;
    top: 0;
    left: 240px;
    right: 0;
    height: 58px;
    background: #fff;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 24px;
    z-index: 99;
}

.topbar-title {
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
}

.topbar-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

.topbar-user {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 5px 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 50px;
}

.topbar-avatar {
    width: 30px;
    height: 30px;
    background: linear-gradient(135deg, #7c3aed, #a78bfa);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 800;
    color: #fff;
}

.topbar-name {
    font-size: 13px;
    font-weight: 600;
    color: #1e293b;
    line-height: 1.2;
}

.topbar-role {
    font-size: 11px;
    color: #7c3aed;
}

.btn-logout {
    padding: 6px 14px;
    background: #fef2f2;
    color: #ef4444;
    border: 1px solid #fecaca;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all .2s;
}

.btn-logout:hover {
    background: #ef4444;
    color: #fff;
}

/* ===== CONTENIDO ===== */
.admin-content {
    margin-left: 240px;
    margin-top: 58px;
    padding: 28px;
    min-height: calc(100vh - 58px);
}

.page-title {
    font-size: 20px;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 24px;
}

/* ===== CARDS ===== */
.card {
    background: #fff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 12px !important;
    box-shadow: 0 1px 3px rgba(0,0,0,.04) !important;
}

.card-header {
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
    color: #1e293b !important;
    font-weight: 700 !important;
    padding: 14px 20px !important;
}

.card-body { color: #334155 !important; }

/* ===== METRICS ===== */
.metric-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px 22px;
    transition: box-shadow .2s;
}

.metric-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,.08);
}

.metric-label {
    font-size: 11px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 8px;
}

.metric-value {
    font-size: 26px;
    font-weight: 900;
    color: #1e293b;
    line-height: 1;
}

.metric-value.c-accent  { color: #7c3aed; }
.metric-value.c-success { color: #16a34a; }
.metric-value.c-warning { color: #d97706; }
.metric-value.c-danger  { color: #dc2626; }
.metric-value.c-info    { color: #2563eb; }

/* ===== TABLAS ===== */
.table { color: #334155 !important; }

.table thead th {
    background: #f8fafc !important;
    color: #64748b !important;
    border-color: #e2e8f0 !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase;
    letter-spacing: .5px;
    padding: 12px 14px !important;
    white-space: nowrap;
}

.table tbody td {
    border-color: #f1f5f9 !important;
    color: #334155 !important;
    padding: 12px 14px !important;
    vertical-align: middle !important;
}

.table-hover tbody tr:hover td {
    background: #f8fafc !important;
}

/* ===== BOTONES (resumen limpio) ===== */
.btn { border-radius: 8px !important; font-size: 13px !important; font-weight: 600 !important; }

.btn-primary { background: #7c3aed !important; border-color: #7c3aed !important; color: #fff !important; }
.btn-primary:hover { background: #6d28d9 !important; }

.btn-dark { background: #1e293b !important; border-color: #1e293b !important; color: #fff !important; }
.btn-dark:hover { background: #7c3aed !important; }

/* ===== INPUTS ===== */
.form-control, .form-select {
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    color: #1e293b !important;
    font-size: 13px !important;
    background: #fff !important;
}

.form-control:focus, .form-select:focus {
    border-color: #7c3aed !important;
    box-shadow: 0 0 0 3px rgba(124,58,237,.1) !important;
}

/* ===== COMPATIBILIDAD SAFE ===== */
.backdrop-glass {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}
</style>

<!-- TOPBAR -->
<nav class="admin-topbar">
    <div class="topbar-title"><?= $tituloActual ?></div>
    <div class="topbar-right">
        <div class="topbar-user">
            <div class="topbar-avatar"><?= $iniciales ?></div>
            <div>
                <div class="topbar-name"><?= htmlspecialchars($_SESSION['usuario']['nombre']) ?></div>
                <div class="topbar-role">Administrador</div>
            </div>
        </div>
        <a href="/power-net/public/index.php?action=logout" class="btn-logout">Salir</a>
    </div>
</nav>

<!-- SweetAlert2 global -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (isset($_SESSION['alert'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon:              '<?= addslashes($_SESSION['alert']['icon'])  ?>',
        title:             '<?= addslashes($_SESSION['alert']['title']) ?>',
        text:              '<?= addslashes($_SESSION['alert']['text'])  ?>',
        confirmButtonColor:'#7c3aed',
        timer:             <?= $_SESSION['alert']['icon'] === 'success' ? 3000 : 0 ?>,
        timerProgressBar:  <?= $_SESSION['alert']['icon'] === 'success' ? 'true' : 'false' ?>
    });
});
</script>
<?php unset($_SESSION['alert']); ?>
<?php endif; ?>
