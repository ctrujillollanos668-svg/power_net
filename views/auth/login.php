<div style="padding:40px 36px;max-width:500px;margin:0 auto;font-family:'Inter','Segoe UI',sans-serif;">

    <!-- LOGO -->
    <div style="text-align:center;margin-bottom:28px;">
        <img src="<?= IMG_URL ?>/logo.jpg"
             style="width:72px;height:72px;object-fit:cover;border-radius:16px;margin-bottom:14px;">
        <h2 style="font-size:26px;font-weight:900;color:#1e293b;margin:0 0 6px;">Power Net</h2>
        <p style="font-size:14px;color:#64748b;margin:0;">Accede a tu cuenta</p>
    </div>

    <!-- LOGIN -->
    <div id="formLogin">
        <form action="index.php?action=login" method="POST">

            <div style="margin-bottom:18px;">
                <label style="font-size:13px;font-weight:700;color:#64748b;display:block;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;">Correo</label>
                <input name="email" type="email" required
                       placeholder="correo@ejemplo.com"
                       style="width:100%;padding:14px 16px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:15px;outline:none;font-family:inherit;transition:border-color .2s;box-sizing:border-box;"
                       onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div style="margin-bottom:18px;position:relative;">
                <label style="font-size:13px;font-weight:700;color:#64748b;display:block;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;">Contraseña</label>
                <input name="password" id="loginPassword" type="password" required
                       placeholder="••••••••"
                       style="width:100%;padding:14px 48px 14px 16px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:15px;outline:none;font-family:inherit;transition:border-color .2s;box-sizing:border-box;"
                       onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e2e8f0'">
                <span onclick="togglePassword('loginPassword')"
                      style="position:absolute;right:16px;top:42px;cursor:pointer;font-size:18px;user-select:none;">👁</span>
            </div>

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;font-size:14px;">
                <label style="display:flex;align-items:center;gap:8px;color:#64748b;cursor:pointer;">
                    <input type="checkbox" style="accent-color:#7c3aed;width:16px;height:16px;"> Recordarme
                </label>
                <a href="#" onclick="showRecuperar()"
                   style="color:#7c3aed;font-weight:600;text-decoration:none;">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit"
                    style="width:100%;padding:15px;background:linear-gradient(135deg,#7c3aed,#6d28d9);color:#fff;border:none;border-radius:14px;font-size:16px;font-weight:700;cursor:pointer;font-family:inherit;transition:opacity .2s;"
                    onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                Iniciar sesión
            </button>
        </form>

        <p style="text-align:center;margin-top:22px;font-size:14px;color:#64748b;">
            ¿No tienes cuenta?
            <a href="#" onclick="showRegister()"
               style="color:#7c3aed;font-weight:700;text-decoration:none;">Crear cuenta</a>
        </p>
    </div>

    <!-- REGISTRO -->
    <div id="formRegistro" style="display:none;">
        <h4 style="font-size:18px;font-weight:800;color:#1e293b;margin:0 0 20px;">Crear cuenta</h4>

        <form action="index.php?action=register" method="POST" id="registerForm">

            <?php
            $inputStyle = "width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;font-family:inherit;margin-bottom:12px;box-sizing:border-box;";
            ?>

            <input name="nombre" type="text" required placeholder="Nombre completo"
                   style="<?= $inputStyle ?>"
                   onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e2e8f0'">

            <input name="email" type="email" required placeholder="Correo electrónico"
                   style="<?= $inputStyle ?>"
                   onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e2e8f0'">

            <input name="password" id="pass" type="password" required placeholder="Contraseña"
                   style="<?= $inputStyle ?>"
                   onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e2e8f0'">

            <input name="cpassword" id="cpass" type="password" required placeholder="Confirmar contraseña"
                   style="<?= $inputStyle ?>"
                   onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e2e8f0'">

            <p id="error" style="display:none;color:#ef4444;font-size:13px;margin-bottom:12px;">
                ⚠ Las contraseñas no coinciden
            </p>

            <button type="submit"
                    style="width:100%;padding:13px;background:linear-gradient(135deg,#7c3aed,#6d28d9);color:#fff;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;font-family:inherit;">
                Crear cuenta
            </button>
        </form>

        <p style="text-align:center;margin-top:20px;font-size:13px;color:#64748b;">
            ¿Ya tienes cuenta?
            <a href="#" onclick="showLogin()"
               style="color:#7c3aed;font-weight:700;text-decoration:none;">Iniciar sesión</a>
        </p>
    </div>

    <!-- RECUPERAR CONTRASEÑA -->
    <div id="formRecuperar" style="display:none;">
        <h4 style="font-size:18px;font-weight:800;color:#1e293b;margin:0 0 8px;">Recuperar contraseña</h4>
        <p style="font-size:13px;color:#64748b;margin-bottom:20px;">
            Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.
        </p>

        <form method="POST" action="index.php?action=enviar_recuperacion">
            <input type="email" name="email" required placeholder="Correo electrónico"
                   style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;font-family:inherit;margin-bottom:16px;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e2e8f0'">

            <button type="submit"
                    style="width:100%;padding:13px;background:linear-gradient(135deg,#7c3aed,#6d28d9);color:#fff;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;font-family:inherit;">
                Enviar enlace
            </button>
        </form>

        <p style="text-align:center;margin-top:20px;font-size:13px;">
            <a href="#" onclick="showLogin()"
               style="color:#7c3aed;font-weight:700;text-decoration:none;">← Volver al login</a>
        </p>
    </div>

</div>

<script>
function showRegister() {
    document.getElementById('formLogin').style.display     = 'none';
    document.getElementById('formRecuperar').style.display = 'none';
    document.getElementById('formRegistro').style.display  = 'block';
}
function showLogin() {
    document.getElementById('formRegistro').style.display  = 'none';
    document.getElementById('formRecuperar').style.display = 'none';
    document.getElementById('formLogin').style.display     = 'block';
}
function showRecuperar() {
    document.getElementById('formLogin').style.display     = 'none';
    document.getElementById('formRegistro').style.display  = 'none';
    document.getElementById('formRecuperar').style.display = 'block';
}
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const p1 = document.getElementById('pass').value;
    const p2 = document.getElementById('cpass').value;
    if (p1 !== p2) {
        e.preventDefault();
        document.getElementById('error').style.display = 'block';
    }
});
</script>
