<?php
require_once __DIR__ . '/../models/Product.php';

class ProductController {

    // ================= CREAR =================
    public function guardar() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        // DATOS DEL FORMULARIO
        $nombre = $_POST['nombre'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $precio = $_POST['precio'] ?? 0;
        $stock = $_POST['stock'] ?? 0;
        $categoria = $_POST['categoria'] ?? null;

        $product = new Product();

        // CREAR PRODUCTO SIN IMAGEN EN TABLA productos
        $idProducto = $product->crear($nombre, $descripcion, $precio, $stock, $categoria);

        // GUARDAR VARIAS IMÁGENES EN imagenes_producto
        if ($idProducto && !empty($_FILES['imagenes']['name'][0])) {

            foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmpName) {

                if (!empty($_FILES['imagenes']['name'][$key])) {

                    $nombreImagen = time() . "_" . $_FILES['imagenes']['name'][$key];
                    $rutaDestino = __DIR__ . '/../public/uploads/' . $nombreImagen;

                    move_uploaded_file($tmpName, $rutaDestino);

                    $product->guardarImagen($idProducto, $nombreImagen);
                }
            }
        }

        header("Location: /power-net/views/admin/productos/productos.php");
        exit;
    }

    // ================= ACTIVAR / DESACTIVAR =================
    public function toggle() {

        $id = $_GET['id'] ?? 0;

        $product = new Product();
        $product->toggle($id);

        header("Location: /power-net/views/admin/productos/productos.php");
        exit;
    }

    // ================= ELIMINAR =================
    public function eliminar() {

        $id      = $_GET['id'] ?? 0;
        $product = new Product();
        $result  = $product->eliminar($id);

        if ($result['bloqueado']) {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => '⚠️ No se puede eliminar',
                'text'  => $result['mensaje']
            ];
        } else {
            $_SESSION['alert'] = [
                'icon'  => 'success',
                'title' => 'Eliminado',
                'text'  => 'El producto fue eliminado correctamente.'
            ];
        }

        header("Location: /power-net/views/admin/productos/productos.php");
        exit;
    }

    // ================= EDITAR =================
    public function editar() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];
        $stock = $_POST['stock'];
        $categoria = $_POST['categoria'];

        $product = new Product();

        // ACTUALIZAR DATOS DEL PRODUCTO SIN IMAGEN
        $product->actualizar(
            $id,
            $nombre,
            $descripcion,
            $precio,
            $stock,
            $categoria
        );

        // SI SUBE NUEVAS IMÁGENES, LAS AGREGA
        if (!empty($_FILES['imagenes']['name'][0])) {

            foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmpName) {

                if (!empty($_FILES['imagenes']['name'][$key])) {

                    $nombreImagen = time() . "_" . $_FILES['imagenes']['name'][$key];
                    $rutaDestino = __DIR__ . '/../public/uploads/' . $nombreImagen;

                    move_uploaded_file($tmpName, $rutaDestino);

                    $product->guardarImagen($id, $nombreImagen);
                }
            }
        }

        header("Location: /power-net/views/admin/productos/productos.php");
        exit;
    }
    // ================= ELIMINAR IMAGEN INDIVIDUAL =================
public function eliminarImagen() {

    $idImagen = $_GET['id_imagen'] ?? 0;
    $idProducto = $_GET['id_producto'] ?? 0;

    $product = new Product();

    // Buscar imagen en BD
    $imagen = $product->obtenerImagenPorId($idImagen);

    if ($imagen) {

        // Borrar archivo físico
        $ruta = __DIR__ . '/../public/uploads/' . $imagen['imagen'];

        if (file_exists($ruta)) {
            unlink($ruta);
        }

        // Borrar de BD
        $product->eliminarImagen($idImagen);
    }

    header("Location: /power-net/views/admin/productos/productos.php");
    exit;
}
}