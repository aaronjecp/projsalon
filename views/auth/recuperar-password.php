<h1 class="nombre-pagina">Recuperar password</h1>
<p class="descripcion-pagina">Coloca tu nuevo password
</p>

<?php include_once __DIR__ . '/../templates/alertas.php' ?>

<?php if($error) return ?>

<form class="formulario" method="POST">
    <div class="campo">
        <label for="email">Password</label>
        <input type="password"
        id="password"
        name="password"
        placeholder="Tu Nuevo password">
    </div>
    <input type="submit" value="Guardar password" class="boton">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes cuenta? Iniciar Sesión</a>
    <a href="/crear-cuenta">¿Aún no tienes cuenta? Obtener una</a>
</div>