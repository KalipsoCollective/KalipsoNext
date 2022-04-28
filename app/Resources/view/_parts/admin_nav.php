        <nav class="navbar navbar-expand-xl navbar-dark bg-black fixed-top shadow">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?php echo $this->url('/'); ?>">
                    <?php echo KN\Helpers\Base::config('app.name'); ?>
                    <small class="h6">_admin</small>    
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarNav" aria-controls="navbarNav" 
                aria-expanded="false" aria-label="<?php echo KN\Helpers\Base::lang('base.toggle_navigation'); ?>">
                    <span class="menu-btn"><span></span></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link<?php echo $this->currentLink('/management'); ?>" href="<?php echo $this->url('/management'); ?>">
                                <?php echo KN\Helpers\Base::lang('base.dashboard'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link<?php echo $this->currentLink('/management/users'); ?>" href="<?php echo $this->url('/management/users'); ?>">
                                <?php echo KN\Helpers\Base::lang('base.users'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link<?php echo $this->currentLink('/management/roles'); ?>" href="<?php echo $this->url('/management/roles'); ?>">
                                <?php echo KN\Helpers\Base::lang('base.user_roles'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link<?php echo $this->currentLink('/management/sessions'); ?>" href="<?php echo $this->url('/management/sessions'); ?>">
                                <?php echo KN\Helpers\Base::lang('base.sessions'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link<?php echo $this->currentLink('/management/logs'); ?>" href="<?php echo $this->url('/management/logs'); ?>">
                                <?php echo KN\Helpers\Base::lang('base.logs'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link<?php echo $this->currentLink('/management/settings'); ?>" href="<?php echo $this->url('/management/settings'); ?>">
                                <?php echo KN\Helpers\Base::lang('base.settings'); ?>
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $this->url('/'); ?>">
                                <?php echo KN\Helpers\Base::lang('base.home'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link<?php echo $this->currentLink('/auth', 'active', false); ?>" href="<?php echo $this->url('/auth'); ?>">
                                <?php echo KN\Helpers\Base::lang('base.account'); ?>
                            </a>
                        </li>
                        <div class="vr"></div>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $this->url('/auth/logout'); ?>">
                                <i class="mdi mdi-power"></i> <?php echo KN\Helpers\Base::lang('base.logout'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>