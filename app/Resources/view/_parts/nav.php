        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?php echo $this->url('/'); ?>"><?php echo KN\Helpers\Base::config('app.name'); ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link<?php echo $this->currentLink('/'); ?>" href="<?php echo $this->url('/'); ?>">
                                <?php echo KN\Helpers\Base::lang('app.home'); ?>
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <?php
                        if (! KN\Helpers\Base::isAuth()) {
                        ?>
                            <li class="nav-item">
                                <a class="nav-link<?php echo $this->currentLink('/auth/login'); ?>" href="<?php echo $this->url('/auth/login'); ?>">
                                    <?php echo KN\Helpers\Base::lang('base.login'); ?>
                                </a>
                            </li>
                            <div class="vr"></div>
                            <li class="nav-item">
                                <a class="nav-link<?php echo $this->currentLink('/auth/register'); ?>" href="<?php echo $this->url('/auth/register'); ?>">
                                    <?php echo KN\Helpers\Base::lang('base.register'); ?>
                                </a>
                            </li>
                        <?php 
                        } else { ?>
                            <li class="nav-item">
                                <a class="nav-link<?php echo $this->currentLink('/auth'); ?>" href="<?php echo $this->url('/auth'); ?>">
                                    <?php echo KN\Helpers\Base::lang('base.account'); ?>
                                </a>
                            </li>
                            <div class="vr"></div>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo $this->url('/auth?logout'); ?>">
                                    <i class="mdi mdi-power"></i> <?php echo KN\Helpers\Base::lang('base.logout'); ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </nav>