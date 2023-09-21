<h1 class="nombre-pagina">Olvidé password</h1>
<p class="descripcion-pagina">Reestablece tu password escribiendo
    tu E-mail a continuación
</p>

<?php include_once __DIR__ . '/../templates/alertas.php' ?>

<form class="formulario" action="/forget" method="POST">
    <div class="campo">
        <label for="email">Email</label>
        <input type="email"
        id="email"
        name="email"
        placeholder="Escribe tu E-mail"
        >
    </div>
    <input type="submit" value="Enviar Instrucciones" class="boton">
</form>

<div class="acciones">
    <a href='/'>¿Ya tienes una cuenta? Inicia Sesión</a>
    <a href='/create-account'>¿Aún no tienes una cuenta? Crear Una</a>
</div>