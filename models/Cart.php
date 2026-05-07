<?php

class Cart {

    public static function obtener() {
        return $_SESSION['carrito'] ?? [];
    }

    public static function agregar($id) {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]++;
        } else {
            $_SESSION['carrito'][$id] = 1;
        }
    }

    public static function aumentar($id) {
        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]++;
        }
    }

    public static function disminuir($id) {
        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]--;

            if ($_SESSION['carrito'][$id] <= 0) {
                unset($_SESSION['carrito'][$id]);
            }
        }
    }

    public static function eliminar($id) {
        unset($_SESSION['carrito'][$id]);
    }

    public static function vaciar() {
        unset($_SESSION['carrito']);
    }
}