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
            <select name="equipo">
            <option value="-1">--Seleccionar equipo--</option> 
            <?php 
            foreach($equipos as $g)
            {
                echo "<option value='{$g['id_equipo']}'> {$g['nombre']} </option>";
            }
            ?>
            </select>
        </div>
        <div class="zbfCardContent">
<select name="rol">
    <option value="-1">--Seleccionar rol--</option> 
    <?php 
    
    foreach($roles as $rol)
    {
            echo "<option value='{$rol['id_rol']}'> {$rol['nombre']} </option>";
           
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
