<div class="sidebar">
        <h4 class="text-center text-white mb-4">Panel Administrativo</h4>
        <?php $m = $_GET['m'] ?? 'home'; ?>
        <a href="home.php?m=home" class="<?= $m == 'home' ? 'active' : '' ?>">Inicio</a>
        <a href="home.php?m=pr" class="<?= $m == 'pr' ? 'active' : '' ?>">Proveedores</a>
        <a href="home.php?m=up" class="<?= $m == 'up' ? 'active' : '' ?>">Usuarios App</a>
        <a href="home.php?m=a" class="<?= $m == 'a' ? 'active' : '' ?>">Alquileres</a>
        <a href="home.php?m=l" class="<?= $m == 'l' ? 'active' : '' ?>">Lavadoras</a>
        <?php /* <a href="home.php?m=pre" class="<?= $m == 'pre' ? 'active' : '' ?>">precios</a> */ ?>
        <a href="home.php?m=ma" class="<?= $m == 'ma' ? 'active' : '' ?>">Mapa</a>
        <?php /* <a href="home.php?m=c" class="<?= $m == 'c' ? 'active' : '' ?>">Configuración</a> */ ?>
        <a href="home.php?m=s">Salir</a> 
    </div>