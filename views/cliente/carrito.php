<?php
// 🛒 Evita errores si no vienen datos del controller
$productosCarrito = $productosCarrito ?? [];
$totalGeneral = $totalGeneral ?? 0;
?>

<div class="card shadow-sm p-4 border-0 rounded-4">

    <h5 class="mb-4 fw-bold">🛒 Mi carrito</h5>

    <div class="table-responsive">

        <table class="table align-middle">

            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-end">Subtotal</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

            <?php foreach ($productosCarrito as $producto): ?>

            <tr class="border-bottom align-middle">

                <!-- PRODUCTO -->
                <td>
                    <div class="d-flex align-items-center gap-3">

                        <img src="<?= UPLOADS_URL ?>/<?= $producto['imagen'] ?>"
                             class="rounded-3 shadow-sm"
                             style="width:60px;height:60px;object-fit:cover;">

                        <div class="fw-semibold">
                            <?= htmlspecialchars($producto['nombre']) ?>
                        </div>

                    </div>
                </td>

                <!-- PRECIO -->
                <td class="fw-semibold">
                    $<?= number_format($producto['precio'], 0, ',', '.') ?>
                </td>

                <!-- CANTIDAD + - -->
                <td class="text-center">

                    <div class="d-flex justify-content-center align-items-center gap-2">

                        <a href="index.php?action=disminuir_carrito&id=<?= $producto['id'] ?>"
                           class="btn btn-sm btn-outline-dark rounded-circle">
                            −
                        </a>

                        <span class="fw-bold">
                            <?= $producto['cantidad'] ?>
                        </span>

                        <a href="index.php?action=aumentar_carrito&id=<?= $producto['id'] ?>"
                           class="btn btn-sm btn-dark rounded-circle">
                            +
                        </a>

                    </div>

                </td>

                <!-- SUBTOTAL -->
                <td class="text-end fw-bold text-success">
                    $<?= number_format($producto['subtotal'], 0, ',', '.') ?>
                </td>

                <!-- 🗑 CANASTILLA MEJORADA -->
                <td class="text-end">
<a href="index.php?action=eliminar_carrito&id=<?= $producto['id'] ?>"
   class="trash-icon-red js-delete"
   title="Eliminar producto">

    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
         fill="currentColor" viewBox="0 0 16 16">
        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0A.5.5 0 0 1 8.5 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
        <path fill-rule="evenodd"
              d="M14.5 3h-13A.5.5 0 0 0 1 3.5v1A.5.5 0 0 0 1.5 5h.5l1 10.5A2 2 0 0 0 5 17h6a2 2 0 0 0 2-1.5L14 5h.5a.5.5 0 0 0 .5-.5v-1A.5.5 0 0 0 14.5 3z"/>
    </svg>

</a>

                </td>

            </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>

    <!-- TOTAL -->
    <div class="d-flex justify-content-between align-items-center mt-4">

        <!-- SEGUIR COMPRANDO -->
        <a href="index.php"
           class="btn btn-outline-dark px-4 rounded-pill">
            ← Seguir comprando
        </a>

        <!-- TOTAL + PAGAR -->
        <div class="text-end">

            <h5 class="fw-bold mb-2">
                Total: $<?= number_format($totalGeneral, 0, ',', '.') ?>
            </h5>

            <a href="index.php?action=procesar_pago"
   class="btn btn-success px-4 rounded-pill">
   💳 Procesar pago
</a>

        </div>

    </div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</div>
<style>
.trash-icon-red{
    width:36px;
    height:36px;

    display:flex;
    align-items:center;
    justify-content:center;

    border-radius:10px;

    color:#ef4444;
    background: transparent;

    border: 1px solid #fecaca;

    transition: all 0.2s ease;

    text-decoration:none;
}

/* hover moderno rojo sólido */
.trash-icon-red:hover{
    background:#ef4444;
    color:#ffffff;

    border-color:#ef4444;

    transform: scale(1.08);
    box-shadow: 0 8px 20px rgba(239,68,68,0.25);
}

/* click suave */
.trash-icon-red:active{
    transform: scale(0.95);
}
</style>
<script>
document.querySelectorAll('.js-delete').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();

        const url = this.getAttribute('href');

        Swal.fire({
            title: "¿Eliminar producto?",
            text: "Este producto se quitará del carrito",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
            reverseButtons: true,
            backdrop: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;

                // opcional: mensaje después
                Swal.fire({
                    title: "Eliminado",
                    text: "El producto fue removido del carrito",
                    icon: "success",
                    timer: 1200,
                    showConfirmButton: false
                });
            }
        });

    });
});
</script>