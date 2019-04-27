<header class="main-header">
    <a href="<?php echo base_url() ?>" class="logo">
        <span class="logo-mini"><b>EXT</b></span>
        <span class="logo-lg"><b>Extemporaneo</b></span>
    </a>
    <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Conmutar menu lateral</span>
            <i class="fas fa-bars"></i>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?php echo base_url("assets/images/usuario.png") ?>" class="user-image" alt="User Image">
                        <span class="hidden-xs"><?php echo getNombreCompleto() ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="user-header">
                            <img src="<?php echo base_url("assets/images/usuario_160x160.png") ?>" class="img-circle" alt="User Image">
                            <p>
                                <?php echo $this->session->nombre ?>
                                <small><?php echo $this->session->perfil ?></small>
                            </p>
                        </li>                        
                        <li class="user-footer">                            
                            <div class="pull-right">
                                <a href="#" class="btn btn-primary btn-flat btn-log-out">Salir</a>
                            </div>
                        </li>
                    </ul>
                </li>                
            </ul>
        </div>
    </nav>
</header>