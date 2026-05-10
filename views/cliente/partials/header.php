<header class="mb-6">

    <!-- HEADER SUPERIOR -->
    <div class="bg-slate-950">
    <div class="mx-auto flex w-full max-w-[1280px] items-center gap-4 px-3 py-3 md:px-6">

        <!-- LOGO -->
        <a href="index.php"
           class="flex items-center gap-3 text-decoration-none">

            <img src="/power-net/img/OIP (1).webp"
                 style="width:44px; height:44px; object-fit:cover;"
                 class="rounded-xl ring-1 ring-white/20">

            <div class="leading-tight">
                <span class="block text-[28px] font-black tracking-tight text-white">Power<span class="text-amber-400">Net</span></span>
                <span class="block text-xs text-slate-300">Electricidad</span>
            </div>
        </a>

        <!-- BUSCADOR -->
        <form method="GET" action="index.php" class="relative mx-auto hidden w-full max-w-[640px] md:block" id="form-buscar">
            <input type="text"
                   name="buscar"
                   id="input-buscar"
                   placeholder="Buscar productos..."
                   value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>"
                   class="h-11 w-full rounded-xl border border-slate-700 bg-white px-4 pr-28 text-sm text-slate-800 outline-none ring-0 transition focus:border-amber-400 focus:ring-4 focus:ring-amber-200/70"
                   autocomplete="off"
            >
            <button type="submit"
                    class="absolute right-1.5 top-1/2 -translate-y-1/2 rounded-lg bg-amber-400 px-5 py-2 text-xs font-black uppercase tracking-wide text-slate-900 transition hover:bg-amber-300">
                Buscar
            </button>
        </form>

        <script>
        // Búsqueda en tiempo real — espera 400ms después de que el usuario deja de escribir
        (function() {
            const input = document.getElementById('input-buscar');
            const form  = document.getElementById('form-buscar');
            if (!input) return;
            let timer;
            input.addEventListener('input', function() {
                clearTimeout(timer);
                timer = setTimeout(function() {
                    // Solo busca si está en el home (no en otras vistas)
                    const url = new URL(window.location.href);
                    if (!url.searchParams.get('action') || url.searchParams.get('action') === '') {
                        form.submit();
                    }
                }, 400);
            });
        })();
        </script>

        <!-- CARRITO -->
        <a href="index.php?action=carrito"
           class="relative ms-auto rounded-xl bg-slate-900/80 px-3 py-2 text-decoration-none text-white ring-1 ring-white/10 transition hover:bg-slate-800 md:ms-0">

            <span style="font-size:22px;">🛒</span>

            <?php if (!empty($_SESSION['carrito'])): ?>
                <span style="
                    position:absolute;
                    top:-8px;
                    right:-8px;
                    background:#fb7185;
                    color:#fff;
                    font-size:11px;
                    font-weight:700;
                    padding:2px 7px;
                    border-radius:50%;
                ">
                    <?= array_sum($_SESSION['carrito']) ?>
                </span>
            <?php endif; ?>

        </a>

    </div>
    </div>

    <!-- MENÚ -->
    <nav class="relative z-40 overflow-visible border-b border-slate-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex w-full max-w-[1280px] items-center justify-between gap-2 px-3 py-3 text-sm font-semibold text-slate-700 md:px-6">

            <div class="hidden w-[140px] md:block"></div>
            <div class="flex flex-1 flex-wrap items-center justify-center gap-1 text-[15px]">
            <a href="index.php" class="rounded-lg px-4 py-2 text-decoration-none transition hover:bg-brand-50 hover:text-brand-700">Inicio</a>
            <span class="mx-1 hidden h-5 w-px bg-slate-200 md:inline-block"></span>

            <!-- DROPDOWN CATEGORÍAS -->
            <?php
            // Cargar categorías activas para el menú
            require_once __DIR__ . '/../../../models/Category.php';
            $catMenu = new Category();
            $listaCats = $catMenu->obtenerActivas();
            ?>
            <div class="dropdown position-relative">
                <a href="#"
                   class="rounded-lg px-4 py-2 text-decoration-none text-slate-700 dropdown-toggle transition hover:bg-brand-50 hover:text-brand-700"
                   data-bs-toggle="dropdown"
                   aria-expanded="false">
                    Categorias
                </a>
                <ul class="dropdown-menu mt-2 overflow-hidden border-0 shadow-xl" style="min-width:220px;border-radius:14px;left:50%;transform:translateX(-50%);z-index:70;">
                    <li>
                        <a class="dropdown-item py-2 fw-semibold" href="index.php">
                            📦 Todas las categorías
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <?php if (!empty($listaCats)): ?>
                        <?php foreach ($listaCats as $cat): ?>
                            <li>
                                <a class="dropdown-item py-2"
                                   href="index.php?categoria=<?= $cat['id_categoria'] ?>">
                                    <?= htmlspecialchars($cat['nombre_categoria']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><span class="dropdown-item text-muted">Sin categorías</span></li>
                    <?php endif; ?>
                </ul>
            </div>

            <span class="mx-1 hidden h-5 w-px bg-slate-200 md:inline-block"></span>
            <a href="index.php?action=ofertas" class="rounded-lg px-4 py-2 text-decoration-none text-slate-700 transition hover:bg-brand-50 hover:text-brand-700">Ofertas</a>
            <span class="mx-1 hidden h-5 w-px bg-slate-200 md:inline-block"></span>
            <a href="index.php?action=mis_pedidos" class="rounded-lg px-4 py-2 text-decoration-none text-slate-700 transition hover:bg-brand-50 hover:text-brand-700">Mis Pedidos</a>
            </div>

            <div class="flex w-[140px] justify-end">
            <?php if (isset($_SESSION['usuario'])): ?>

                <div class="dropdown">
                    <button class="btn dropdown-toggle rounded-xl border-slate-300 px-3 py-1.5 text-sm font-semibold text-slate-700 hover:bg-slate-100"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                        👤 Mi cuenta
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end rounded-xl border-0 p-2 shadow-xl">
                        <li><a class="dropdown-item" href="index.php?action=mi_perfil">Mi perfil</a></li>
                        <li><a class="dropdown-item" href="index.php?action=carrito">🛒 Mi carrito</a></li>
                        <li><a class="dropdown-item" href="index.php?action=mis_pedidos">📦 Mis pedidos</a></li>
                        <li><a class="dropdown-item" href="index.php?action=mis_devoluciones">↩️ Mis devoluciones</a></li>
                        <li><a class="dropdown-item" href="index.php?action=mis_favoritos">❤️ Mis favoritos</a></li>
                        <li><a class="dropdown-item" href="index.php?action=medios_pago">💳 Métodos de pago</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="index.php?action=logout">Cerrar sesión</a></li>
                    </ul>
                </div>

            <?php endif; ?>
            </div>

        </div>
    </nav>

</header>

<!-- SweetAlert2 global para el cliente -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (isset($_SESSION['alert'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon:              '<?= addslashes($_SESSION['alert']['icon'])  ?>',
        title:             '<?= addslashes($_SESSION['alert']['title']) ?>',
        text:              '<?= addslashes($_SESSION['alert']['text'])  ?>',
        confirmButtonColor: '#7c3aed',
        timer:             <?= in_array($_SESSION['alert']['icon'], ['success']) ? 3000 : 0 ?>,
        timerProgressBar:  <?= in_array($_SESSION['alert']['icon'], ['success']) ? 'true' : 'false' ?>
    });
});
</script>
<?php unset($_SESSION['alert']); ?>
<?php endif; ?>

<!-- ============================================================
     ASISTENTE VIRTUAL — POWER NET
============================================================ -->
<style>
/* Botón flotante */
#asistente-btn {
    position: fixed; bottom: 28px; right: 28px; z-index: 9999;
    width: 58px; height: 58px; border-radius: 50%;
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    border: none; cursor: pointer; box-shadow: 0 6px 24px rgba(124,58,237,.45);
    display: flex; align-items: center; justify-content: center;
    transition: transform .2s, box-shadow .2s;
}
#asistente-btn:hover { transform: scale(1.1); box-shadow: 0 10px 32px rgba(124,58,237,.55); }
#asistente-btn .asi-icon { font-size: 26px; line-height: 1; }
#asistente-badge {
    position: absolute; top: -4px; right: -4px;
    width: 18px; height: 18px; background: #ef4444; border-radius: 50%;
    border: 2px solid #fff; display: none;
}

/* Ventana del chat */
#asistente-box {
    position: fixed; bottom: 100px; right: 28px; z-index: 9998;
    width: 360px; max-height: 540px;
    background: #fff; border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,.18);
    display: flex; flex-direction: column;
    overflow: hidden; transform: scale(0.85) translateY(20px);
    opacity: 0; pointer-events: none;
    transition: all .25s cubic-bezier(.34,1.56,.64,1);
    transform-origin: bottom right;
}
#asistente-box.abierto {
    transform: scale(1) translateY(0);
    opacity: 1; pointer-events: all;
}

/* Header del chat */
.asi-header {
    background: linear-gradient(135deg, #1a1a2e, #302b63);
    padding: 16px 18px; display: flex; align-items: center; gap: 12px;
}
.asi-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background: linear-gradient(135deg,#7c3aed,#a78bfa);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
}
.asi-header-info { flex: 1; }
.asi-header-info strong { color: #fff; font-size: 14px; display: block; }
.asi-header-info span { color: #a78bfa; font-size: 11px; }
.asi-online { width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block; margin-right: 4px; }
.asi-close {
    background: rgba(255,255,255,.1); border: none; color: #fff;
    width: 30px; height: 30px; border-radius: 50%; cursor: pointer;
    font-size: 16px; display: flex; align-items: center; justify-content: center;
    transition: background .2s;
}
.asi-close:hover { background: rgba(255,255,255,.25); }

/* Mensajes */
.asi-mensajes {
    flex: 1; overflow-y: auto; padding: 16px;
    display: flex; flex-direction: column; gap: 10px;
    background: #f8fafc;
}
.asi-mensajes::-webkit-scrollbar { width: 4px; }
.asi-mensajes::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

.msg { display: flex; gap: 8px; align-items: flex-end; max-width: 90%; }
.msg.bot { align-self: flex-start; }
.msg.user { align-self: flex-end; flex-direction: row-reverse; }

.msg-avatar {
    width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0;
    background: linear-gradient(135deg,#7c3aed,#a78bfa);
    display: flex; align-items: center; justify-content: center; font-size: 14px;
}
.msg-burbuja {
    padding: 10px 14px; border-radius: 16px; font-size: 13px; line-height: 1.5;
    max-width: 260px;
}
.msg.bot .msg-burbuja {
    background: #fff; color: #1a1a2e;
    border-bottom-left-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
}
.msg.user .msg-burbuja {
    background: linear-gradient(135deg,#7c3aed,#6d28d9);
    color: #fff; border-bottom-right-radius: 4px;
}
.msg-burbuja a { color: #7c3aed; font-weight: 700; text-decoration: none; }
.msg.user .msg-burbuja a { color: #e9d5ff; }

/* Chips de sugerencias */
.asi-chips {
    padding: 8px 16px 4px; display: flex; flex-wrap: wrap; gap: 6px;
    background: #f8fafc; border-top: 1px solid #f0f0f0;
}
.chip {
    background: #fff; border: 1.5px solid #e5e7eb; border-radius: 20px;
    padding: 5px 12px; font-size: 11px; font-weight: 600; color: #374151;
    cursor: pointer; transition: all .15s; white-space: nowrap;
}
.chip:hover { border-color: #7c3aed; color: #7c3aed; background: #faf5ff; }

/* Input */
.asi-input-wrap {
    padding: 12px 16px; background: #fff; border-top: 1px solid #f0f0f0;
    display: flex; gap: 8px; align-items: center;
}
.asi-input {
    flex: 1; border: 2px solid #e5e7eb; border-radius: 20px;
    padding: 8px 14px; font-size: 13px; outline: none;
    transition: border-color .2s;
}
.asi-input:focus { border-color: #7c3aed; }
.asi-send {
    width: 36px; height: 36px; border-radius: 50%;
    background: linear-gradient(135deg,#7c3aed,#6d28d9);
    border: none; color: #fff; cursor: pointer; font-size: 16px;
    display: flex; align-items: center; justify-content: center;
    transition: transform .15s;
}
.asi-send:hover { transform: scale(1.1); }

/* Typing indicator */
.typing-dots span {
    display: inline-block; width: 6px; height: 6px; border-radius: 50%;
    background: #9ca3af; margin: 0 2px;
    animation: typing 1.2s infinite;
}
.typing-dots span:nth-child(2) { animation-delay: .2s; }
.typing-dots span:nth-child(3) { animation-delay: .4s; }
@keyframes typing { 0%,60%,100% { transform: translateY(0); } 30% { transform: translateY(-6px); } }

@media (max-width: 480px) {
    #asistente-box { width: calc(100vw - 32px); right: 16px; bottom: 90px; }
    #asistente-btn { right: 16px; bottom: 20px; }
}
</style>

<!-- Botón flotante -->
<button id="asistente-btn" onclick="toggleAsistente()" title="Asistente Power Net">
    <span class="asi-icon">🤖</span>
    <span id="asistente-badge"></span>
</button>

<!-- Ventana del chat -->
<div id="asistente-box">
    <div class="asi-header">
        <div class="asi-avatar">🤖</div>
        <div class="asi-header-info">
            <strong>Asistente Power Net</strong>
            <span><span class="asi-online"></span>En línea ahora</span>
        </div>
        <button class="asi-close" onclick="toggleAsistente()">✕</button>
    </div>

    <div class="asi-mensajes" id="asi-mensajes"></div>

    <div class="asi-chips" id="asi-chips"></div>

    <div class="asi-input-wrap">
        <input type="text" class="asi-input" id="asi-input"
               placeholder="Escribe tu pregunta..."
               onkeydown="if(event.key==='Enter') enviarMensaje()">
        <button class="asi-send" onclick="enviarMensaje()">➤</button>
    </div>
</div>
<!--  asistente de ia -->
<script>
(function() {

// ── Estado de sesión ──
const logueado = <?= isset($_SESSION['usuario']) ? 'true' : 'false' ?>;
const nombreUsuario = <?= isset($_SESSION['usuario']) ? json_encode(htmlspecialchars($_SESSION['usuario']['nombre'] ?? 'Cliente')) : '"visitante"' ?>;

// ── Base de conocimiento ──
const KB = [
    // Compra / productos
    {
        claves: ['comprar','compra','cómo compro','como compro','adquirir','pedir'],
        respuesta: () => `Para comprar es muy fácil 🛒\n1. Busca el producto que quieres\n2. Haz clic en <strong>Comprar</strong> o agrégalo al carrito\n3. ${logueado ? 'Ve a tu <a href="index.php?action=carrito">carrito</a> y finaliza el pago' : '<a href="#" onclick="abrirLogin()">Inicia sesión</a> y completa tu pedido'}`
    },
    {
        claves: ['producto','productos','catálogo','catalogo','ver productos'],
        respuesta: () => `Tenemos una gran variedad de productos 📦\nPuedes explorar por <strong>categorías</strong> en el menú o usar el <strong>buscador</strong> arriba.\n<a href="index.php">Ver todos los productos →</a>`
    },
    {
        claves: ['oferta','ofertas','descuento','descuentos','promocion','promoción'],
        respuesta: () => `¡Tenemos ofertas especiales! 🔥\nRevisa nuestra sección de ofertas para ver los mejores precios.\n<a href="index.php?action=ofertas">Ver ofertas →</a>`
    },
    // Carrito
    {
        claves: ['carrito','carro','agregar carrito','mi carrito'],
        respuesta: () => logueado
            ? `Tu carrito está aquí 🛒\n<a href="index.php?action=carrito">Ver mi carrito →</a>`
            : `Para usar el carrito necesitas <a href="#" onclick="abrirLogin()">iniciar sesión</a> primero 😊`
    },
    // Pago
    {
        claves: ['pago','pagar','método de pago','metodo de pago','tarjeta','transferencia','cómo pago','como pago'],
        respuesta: () => `Aceptamos los siguientes métodos de pago 💳\n• Tarjeta de crédito/débito\n• Transferencia bancaria\n\n${logueado ? 'Puedes gestionar tus métodos en <a href="index.php?action=medios_pago">Métodos de pago →</a>' : '<a href="#" onclick="abrirLogin()">Inicia sesión</a> para agregar un método de pago'}`
    },
    // Envío
    {
        claves: ['envio','envío','envíos','envios','despacho','entrega','llega','demora','tiempo'],
        respuesta: () => `Realizamos envíos a todo el país 🚚\nUna vez confirmado tu pago, procesamos el pedido y te notificamos cuando sea enviado.\n${logueado ? 'Puedes ver el estado en <a href="index.php?action=mis_pedidos">Mis pedidos →</a>' : ''}`
    },
    // Pedidos
    {
        claves: ['pedido','pedidos','mis pedidos','estado pedido','donde está','donde esta','seguimiento'],
        respuesta: () => logueado
            ? `Puedes ver todos tus pedidos y su estado aquí 📦\n<a href="index.php?action=mis_pedidos">Ver mis pedidos →</a>`
            : `Para ver tus pedidos necesitas <a href="#" onclick="abrirLogin()">iniciar sesión</a> 🔐`
    },
    // Devoluciones
    {
        claves: ['devolucion','devolución','devolver','reembolso','cambio','cambiar producto'],
        respuesta: () => logueado
            ? `Puedes solicitar una devolución cuando tu pedido esté en estado <strong>Entregado</strong> ↩️\n<a href="index.php?action=mis_devoluciones">Ver mis devoluciones →</a>`
            : `Para solicitar una devolución primero <a href="#" onclick="abrirLogin()">inicia sesión</a> y ve a Mis pedidos`
    },
    // Factura
    {
        claves: ['factura','facturar','comprobante','recibo'],
        respuesta: () => logueado
            ? `Puedes descargar tu factura desde <strong>Mis pedidos</strong> cuando el pedido esté enviado o entregado 🧾\n<a href="index.php?action=mis_pedidos">Ir a mis pedidos →</a>`
            : `Para ver tu factura <a href="#" onclick="abrirLogin()">inicia sesión</a> primero`
    },
    // Favoritos
    {
        claves: ['favorito','favoritos','me gusta','guardar producto','lista deseos'],
        respuesta: () => logueado
            ? `Puedes guardar productos con el ❤️ en cada tarjeta.\n<a href="index.php?action=mis_favoritos">Ver mis favoritos →</a>`
            : `Para guardar favoritos <a href="#" onclick="abrirLogin()">inicia sesión</a> primero ❤️`
    },
    // Cuenta / perfil
    {
        claves: ['cuenta','perfil','mi perfil','datos','información personal'],
        respuesta: () => logueado
            ? `Puedes actualizar tus datos en tu perfil 👤\n<a href="index.php?action=mi_perfil">Ir a mi perfil →</a>`
            : `<a href="#" onclick="abrirLogin()">Inicia sesión</a> para acceder a tu cuenta`
    },
    // Dirección
    {
        claves: ['dirección','direccion','domicilio','donde envian','donde envían'],
        respuesta: () => logueado
            ? `Puedes agregar o editar tu dirección de envío en <a href="index.php?action=procesar_pago">Finalizar compra</a> o en tu perfil 📍`
            : `Para gestionar tu dirección <a href="#" onclick="abrirLogin()">inicia sesión</a> primero`
    },
    // Registro
    {
        claves: ['registrar','registro','crear cuenta','nueva cuenta','sign up'],
        respuesta: () => `Para crear una cuenta haz clic en <strong>Iniciar sesión</strong> y luego en <strong>Registrarse</strong> 📝\n<a href="#" onclick="abrirLogin()">Crear cuenta →</a>`
    },
    // Login
    {
        claves: ['login','iniciar sesion','iniciar sesión','entrar','acceder','contraseña','password'],
        respuesta: () => logueado
            ? `Ya estás conectado como <strong>${nombreUsuario}</strong> ✅`
            : `<a href="#" onclick="abrirLogin()">Haz clic aquí para iniciar sesión →</a>`
    },
    // Contacto / ayuda
    {
        claves: ['contacto','contactar','ayuda','soporte','problema','error','falla'],
        respuesta: () => `Estamos aquí para ayudarte 💜\nSi tienes un problema con tu pedido, revisa <a href="index.php?action=mis_pedidos">Mis pedidos</a>.\nPara soporte directo escríbenos a <strong>soporte@powernet.com</strong>`
    },
    // Saludo
    {
        claves: ['hola','buenas','buenos días','buenas tardes','buenas noches','hey','hi'],
        respuesta: () => logueado
            ? `¡Hola <strong>${nombreUsuario}</strong>! 👋 ¿En qué te puedo ayudar hoy?`
            : `¡Hola! 👋 Soy el asistente de <strong>Power Net</strong>. ¿En qué te puedo ayudar?`
    },
    // Gracias
    {
        claves: ['gracias','muchas gracias','thanks','perfecto','listo','ok','genial'],
        respuesta: () => `¡Con gusto! 😊 Si necesitas algo más, aquí estoy.`
    },
];

// Chips de sugerencias según estado de sesión
const chipsSinLogin = [
    { texto: '🛒 ¿Cómo compro?',       pregunta: 'cómo compro' },
    { texto: '💳 Métodos de pago',      pregunta: 'métodos de pago' },
    { texto: '🚚 ¿Hacen envíos?',       pregunta: 'envíos' },
    { texto: '🔥 Ver ofertas',          pregunta: 'ofertas' },
    { texto: '📝 Crear cuenta',         pregunta: 'registrar' },
];
const chipsConLogin = [
    { texto: '📦 Mis pedidos',          pregunta: 'mis pedidos' },
    { texto: '↩️ Devoluciones',         pregunta: 'devolución' },
    { texto: '🧾 Mi factura',           pregunta: 'factura' },
    { texto: '❤️ Favoritos',            pregunta: 'favoritos' },
    { texto: '💳 Métodos de pago',      pregunta: 'métodos de pago' },
    { texto: '🚚 Estado de envío',      pregunta: 'envíos' },
];

// ── Funciones ──
let abierto = false;
let primerApertura = true;

window.toggleAsistente = function() {
    abierto = !abierto;
    const box = document.getElementById('asistente-box');
    box.classList.toggle('abierto', abierto);
    document.getElementById('asistente-badge').style.display = 'none';

    if (abierto && primerApertura) {
        primerApertura = false;
        renderChips();
        setTimeout(() => {
            const saludo = logueado
                ? `¡Hola <strong>${nombreUsuario}</strong>! 👋 Soy tu asistente de <strong>Power Net</strong>. ¿En qué te puedo ayudar hoy?`
                : `¡Hola! 👋 Soy el asistente de <strong>Power Net</strong>.<br>Puedo ayudarte con compras, pedidos, pagos y más. ¿Qué necesitas?`;
            agregarMensaje('bot', saludo);
        }, 300);
    }

    if (abierto) {
        setTimeout(() => document.getElementById('asi-input').focus(), 300);
    }
};

function renderChips() {
    const chips = logueado ? chipsConLogin : chipsSinLogin;
    const wrap  = document.getElementById('asi-chips');
    wrap.innerHTML = chips.map(c =>
        `<button class="chip" onclick="preguntarChip('${c.pregunta}')">${c.texto}</button>`
    ).join('');
}

window.preguntarChip = function(texto) {
    document.getElementById('asi-input').value = texto;
    enviarMensaje();
};

window.enviarMensaje = function() {
    const input = document.getElementById('asi-input');
    const texto = input.value.trim();
    if (!texto) return;
    input.value = '';

    agregarMensaje('user', texto);
    mostrarTyping();

    setTimeout(() => {
        quitarTyping();
        const respuesta = buscarRespuesta(texto);
        agregarMensaje('bot', respuesta);
    }, 700 + Math.random() * 400);
};

function buscarRespuesta(texto) {
    const t = texto.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    for (const item of KB) {
        for (const clave of item.claves) {
            const c = clave.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            if (t.includes(c)) return item.respuesta();
        }
    }
    return `No estoy seguro de cómo ayudarte con eso 🤔<br>Prueba con: <em>pedidos, devolución, pago, envío, ofertas</em>...<br>O escríbenos a <strong>soporte@powernet.com</strong>`;
}

function agregarMensaje(tipo, html) {
    const wrap = document.getElementById('asi-mensajes');
    const div  = document.createElement('div');
    div.className = `msg ${tipo}`;

    if (tipo === 'bot') {
        div.innerHTML = `
            <div class="msg-avatar">🤖</div>
            <div class="msg-burbuja">${html.replace(/\n/g, '<br>')}</div>`;
    } else {
        div.innerHTML = `<div class="msg-burbuja">${html}</div>`;
    }

    wrap.appendChild(div);
    wrap.scrollTop = wrap.scrollHeight;
}

let typingEl = null;
function mostrarTyping() {
    const wrap = document.getElementById('asi-mensajes');
    typingEl   = document.createElement('div');
    typingEl.className = 'msg bot';
    typingEl.id = 'typing-indicator';
    typingEl.innerHTML = `
        <div class="msg-avatar">🤖</div>
        <div class="msg-burbuja typing-dots">
            <span></span><span></span><span></span>
        </div>`;
    wrap.appendChild(typingEl);
    wrap.scrollTop = wrap.scrollHeight;
}
function quitarTyping() {
    if (typingEl) { typingEl.remove(); typingEl = null; }
}

// Mostrar badge después de 3 segundos si no se ha abierto
setTimeout(() => {
    if (!abierto) {
        document.getElementById('asistente-badge').style.display = 'block';
    }
}, 3000);

})();
</script>
