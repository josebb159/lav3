<?php
if(isset($_GET['m'])){
    $menu = $_GET['m'];
    
    switch ($menu) {
        case 'home':
            require_once "inicio.php";
            break;
        case 'us':
            require_once "usuarios_sistema.php";
        break;
        case 'up':
            require_once "usuarios_app.php";
            break;
        case 'a':
        require_once "alquileres.php";
        break;
        case 'n':
            require_once "negocios.php";
            break;

        case 'c':
        require_once "configuracion.php";
        break;
    
        case 'l':
        require_once "lavadora.php";
        break;
        case 'r':
            require_once "reporte.php";
            break;
    
        case 's':
            require_once "logout.php";
            break;
        case 'pa':
            require_once "payments.php";
            break;
        case 'pau':
            require_once "pay_payu.php";
            break;
        case 'pr':
            require_once "proveedor.php";
            break;      
      case 'pre':
            // CAMBIO: Solo administradores pueden acceder a precios globales
            if ($_SESSION['user_rol'] != 1) {
                echo '<div class="alert alert-danger">Acceso denegado. Solo administradores pueden gestionar precios globales.</div>';
            } else {
                require_once "precios.php";
            }
            break;      
       case 'mo':
            require_once "motivos.php";
            break;      
            
                case 'tc':
            require_once "terminos_condiciones.php";
            break;   
            
                  case 'ma':
            require_once "map.php";
            break;    
        default:
            require_once "inicio.php";
            break;
    }
}
?>