<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Selección de Rol</title>
    <style>
        /* --- Estilos Generales y Layout --- */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f2f2f2; /* Gris claro de fondo */
            display: flex;
            min-height: 100vh;
        }

        /* Contenedor principal para la banda negra y el contenido */
        .main-container {
            display: flex;
            width: 100%;
        }

        /* Banda Negra Lateral Izquierda (20%) - Contenedor Flex para centrado */
        .sidebar-black {
            width: 20%;
            background-color: #333; /* Negro/Gris muy oscuro */
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center; /* Centra horizontalmente el contenido */
            padding-top: 20px;
            box-sizing: border-box;
        }

        /* Logo centrado en el sidebar */
        .logo {
            margin-bottom: 30px;
            text-align: center;
        }

        .logo img {
            max-width: 80%; /* Ajusta el tamaño del logo para que no se salga */
            height: auto;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        /* Contenedor de la imagen de logout */
        .logout-container {
            text-align: center;
        }

        .logout-container img {
            width: 50px;   /* Ancho fijo de 50px */
            height: 50px;  /* Alto fijo de 50px */
            display: block;
            margin-left: auto;
            margin-right: auto;
            border: 1px solid #555;
            border-radius: 5px;
            transition: opacity 0.3s;
        }

        .logout-container img:hover {
            opacity: 0.8;
        }

        /* Contenido Principal (80%) */
        .content-area {
            width: 80%;
            background-color: #fff; /* Fondo blanco para el contenido */
            display: flex;
            justify-content: center; /* Centrado horizontal */
            align-items: center; /* Centrado vertical */
            padding: 20px;
            box-sizing: border-box;
        }

        /* --- Contenedor de Selección de Rol (Lado Derecho Centrado) --- */
        .role-selection-box {
            width: 90%;
            max-width: 500px;
            padding: 30px;
            background-color: #fff;
            border: 1px solid #ccc; /* Borde gris */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        /* Estilo del título */
        .role-selection-box h2 {
            color: #555; /* Texto gris oscuro */
            margin-bottom: 20px;
            font-size: 1.5em;
        }

        /* Cuadro Scrollable para las Opciones */
        .roles-list-container {
            max-height: 250px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #f9f9f9;
        }

        /* Estilos para los links de Rol */
        .role-option {
            display: block;
            padding: 12px 15px;
            margin: 8px 0;
            text-decoration: none;
            color: #444;
            background-color: #eee;
            border: 1px solid #ddd;
            border-radius: 4px;
            transition: background-color 0.3s, color 0.3s;
        }

        .role-option:hover {
            background-color: #ddd;
            color: #222;
        }
    </style>
</head>
<body>

    <div class="main-container">

        <div class="sidebar-black">
            
            <div class="logo">
                <img src="/res/img/logo mensajeria.png" alt="Logotipo de la Aplicación">
            </div>

            <div class="logout-container">
                <a href="index/logout"> 
                    <img src="/res/img/icons/arrow-go-back-fill.svg" alt="Cerrar Sesión"> 
                </a>
            </div>
        </div>

        <div class="content-area">

            <div class="role-selection-box">
                
                <h2>Seleccione Rol</h2>

                <div class="roles-list-container">
                    
                    <?php if (!empty($roles) && is_array($roles)): ?>
                        <?php foreach ($roles as $role): ?>
                            <a href="/index/setrol/<?=$role['id_rol']?>" class="role-option">
                                <?= htmlspecialchars($role['nombre']) ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #888;">No hay roles disponibles.</p>
                    <?php endif; ?>
                    
                </div>
            </div>

        </div>
    </div>
<?php //echo(var_dump($roles));?>
</body>
</html>