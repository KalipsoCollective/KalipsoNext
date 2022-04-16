        <nav class="navbar navbar-expand-lg navbar-dark kn-nav fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?php echo Base::base(); ?>"><?php echo Base::config('app.name'); ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link<?php echo Base::currentPage(); ?>" href="<?php echo Base::base(); ?>">
                                <?php echo Base::lang('home'); ?>
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <?php
                        if (! Base::isAuth()) {
                        ?>
                            <li class="nav-item">
                                <a class="nav-link<?php echo Base::currentPage('/account/login'); ?>" href="<?php echo Base::base('/account/login'); ?>">
                                    <?php echo Base::lang('login'); ?>
                                </a>
                            </li>
                            <div class="vr"></div>
                            <li class="nav-item">
                                <a class="nav-link<?php echo Base::currentPage('/account/register'); ?>" href="<?php echo Base::base('/account/register'); ?>">
                                    <?php echo Base::lang('register'); ?>
                                </a>
                            </li>
                        <?php 
                        } else { ?>
                            <li class="nav-item">
                                <a class="nav-link<?php echo Base::currentPage('/account'); ?>" href="<?php echo Base::base('/account'); ?>">
                                    <?php echo Base::lang('account'); ?>
                                </a>
                            </li>
                            <div class="vr"></div>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo Base::base('/account?logout'); ?>">
                                    <i class="mdi mdi-power"></i> <?php echo Base::lang('logout'); ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </nav>