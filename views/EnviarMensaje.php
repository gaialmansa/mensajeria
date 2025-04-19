<html>
<head><title>Envío de mensajes a usuarios/grupos</title></head>
<body>
<div class="zbfBox">
<h1>Envío de mensajes</h1>
<form action="accion" method="get">
<div class="zbfCard">
    <div class="zbfCardTitle">Destinatario</div>
    <div class="zbfCardBody">
        <div class="zbfCardContent">
            <select name="grupo">
            <option value="-1">--Seleccionar grupo--</option> 
            <?php 
            //echo var_dump($grupos);
            foreach($grupos as $g)
            {
                echo "<option value='{$g['id_grupo']}'> {$g['grupo']} </option>";
            }
            ?>
            </select>
        </div>
        <div class="zbfCardContent">
<select name="usuario">
    <option value="-1">--Seleccionar usuario--</option> 
    <?php 
    //echo var_dump($grupos);

    foreach($usuarios as $u)
    {
            echo "<option value='{$u['id_usuario']}'> {$u['nombre']} </option>";
           
    }
    ?>
</select>
        </div>
    </div>
</div>

<div class="zbfCard">
    <div class="zbfCardTitle">Contenido</div>
    <div class="zbfCardBody">
        <div class="zbfCardContent">
Introduzca mensaje: 
<input type="text" width="60" maxlength="128" name="mensaje">
        </div>
    </div>
    </div>
     <button class="zbfCardButton" type="submit">Enviar</button>
</div>
</form>
</div>
</body>
</html>
