<div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8 transition-all duration-300">

    <!-- LOGO -->
    <div class="text-center mb-6">
        <img src="/power-net/img/logo.jpg" class="w-16 mx-auto mb-2">
        <h2 class="text-2xl font-bold text-gray-800">Power Net</h2>
        <p class="text-gray-500 text-sm">Accede a tu cuenta</p>
    </div>

    <!-- LOGIN -->
    <div id="formLogin">
        <form action="/power-net/public/index.php?action=login" method="POST" class="space-y-5">

            <div>
                <label class="text-sm text-gray-600">Correo</label>
                <input name="email" type="email" required class="w-full mt-1 px-4 py-2 border rounded-lg" placeholder="correo@ejemplo.com">
            </div>

            <div class="relative">
                <label class="text-sm text-gray-600">Contraseña</label>
                <input name="password" id="loginPassword" type="password" required class="w-full mt-1 px-4 py-2 border rounded-lg" placeholder="********">
                <span onclick="togglePassword('loginPassword')" class="absolute right-3 top-9 cursor-pointer">🔐</span>
            </div>

            <div class="flex justify-between text-sm">
                <label><input type="checkbox"> Recordarme</label>
                <a href="#" onclick="showRecuperar()" class="text-indigo-600">¿Olvidaste tu contraseña?</a>
            </div>

            <button class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700">
                Iniciar sesión
            </button>
        </form>

        <p class="text-center mt-4 text-sm">
            ¿No tienes cuenta?
            <a href="#" onclick="showRegister()" class="text-indigo-600 font-semibold">Crear cuenta</a>
        </p>
    </div>

    <!-- REGISTRO -->
    <div id="formRegistro" class="hidden">
        <form action="/power-net/public/index.php?action=register" method="POST" id="registerForm" class="space-y-4">

            <input name="nombre" type="text" required class="w-full px-4 py-2 border rounded-lg" placeholder="Nombre completo">
            <input name="email" type="email" required class="w-full px-4 py-2 border rounded-lg" placeholder="Correo">

            <input name="password" id="pass" type="password" required class="w-full px-4 py-2 border rounded-lg" placeholder="Contraseña">
            <input name="cpassword" id="cpass" type="password" required class="w-full px-4 py-2 border rounded-lg" placeholder="Confirmar contraseña">

            <p id="error" class="text-red-500 text-sm hidden">⚠ Las contraseñas no coinciden</p>

            <button class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                Crear cuenta
            </button>
        </form>

        <p class="text-center mt-4 text-sm">
            ¿Ya tienes cuenta?
            <a href="#" onclick="showLogin()" class="text-indigo-600 font-semibold">Iniciar sesión</a>
        </p>
    </div>

    <!-- RECUPERAR CONTRASEÑA -->
    <div id="formRecuperar" class="hidden">
        <h4 class="text-xl font-bold mb-3">Recuperar contraseña</h4>

        <p class="text-gray-500 text-sm mb-4">
            Escribe tu correo y generaremos un enlace para recuperar tu contraseña.
        </p>

        <form method="POST" action="/power-net/public/index.php?action=enviar_recuperacion" class="space-y-4">
            <input type="email" name="email" required class="w-full px-4 py-2 border rounded-lg" placeholder="Correo electrónico">

            <button class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700">
                Enviar enlace
            </button>
        </form>

        <p class="text-center mt-4 text-sm">
            <a href="#" onclick="showLogin()" class="text-indigo-600 font-semibold">← Volver al login</a>
        </p>
    </div>

</div>

<script>
function showRegister(){
    document.getElementById('formLogin').classList.add('hidden');
    document.getElementById('formRecuperar').classList.add('hidden');
    document.getElementById('formRegistro').classList.remove('hidden');
}

function showLogin(){
    document.getElementById('formRegistro').classList.add('hidden');
    document.getElementById('formRecuperar').classList.add('hidden');
    document.getElementById('formLogin').classList.remove('hidden');
}

function showRecuperar(){
    document.getElementById('formLogin').classList.add('hidden');
    document.getElementById('formRegistro').classList.add('hidden');
    document.getElementById('formRecuperar').classList.remove('hidden');
}

function togglePassword(id){
    let input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
}

document.getElementById("registerForm").addEventListener("submit", function(e){
    let p1 = document.getElementById("pass").value;
    let p2 = document.getElementById("cpass").value;

    if(p1 !== p2){
        e.preventDefault();
        document.getElementById("error").classList.remove("hidden");
    }
});
</script>