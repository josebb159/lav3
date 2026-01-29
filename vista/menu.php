<div class="sidebar">
        <h4 class="text-center text-white mb-4">Panel Administrativo</h4>
        <?php $m = $_GET['m'] ?? 'home'; ?>
        <a href="home.php?m=home" class="<?= $m == 'home' ? 'active' : '' ?>">Inicio</a>
        <a href="home.php?m=us" class="<?= $m == 'us' ? 'active' : '' ?>">Usuarios Sistema</a>
        <a href="home.php?m=up" class="<?= $m == 'up' ? 'active' : '' ?>">Usuarios App</a>
        <a href="home.php?m=a" class="<?= $m == 'a' ? 'active' : '' ?>">Alquileres</a>
        <a href="home.php?m=n" class="<?= $m == 'n' ? 'active' : '' ?>">Negocios</a>
        <a href="home.php?m=pr" class="<?= $m == 'pr' ? 'active' : '' ?>">Proveedores</a>
        <a href="home.php?m=l" class="<?= $m == 'l' ? 'active' : '' ?>">Lavadoras</a>
        <a href="home.php?m=pre" class="<?= $m == 'pre' ? 'active' : '' ?>">Precios Globales</a>
        <a href="home.php?m=pa" class="<?= $m == 'pa' ? 'active' : '' ?>">Pagos</a>
        <a href="home.php?m=pau" class="<?= $m == 'pau' ? 'active' : '' ?>">Pagos Payu</a>
        <a href="home.php?m=mo" class="<?= $m == 'mo' ? 'active' : '' ?>">Motivos</a>
        <a href="home.php?m=r" class="<?= $m == 'r' ? 'active' : '' ?>">Reporte</a>
        <a href="home.php?m=tc" class="<?= $m == 'tc' ? 'active' : '' ?>">Terminos y Condiciones</a>
        <a href="home.php?m=c" class="<?= $m == 'c' ? 'active' : '' ?>">Configuraci¨®n</a>
        <a href="home.php?m=s">Salir</a> 
    </div>