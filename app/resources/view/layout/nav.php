        <nav class="navbar navbar-expand-lg navbar-dark kn-nav fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="#"><?php echo self::config('app.name'); ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link<?php echo self::currentPage(); ?>" href="<?php echo self::base(); ?>">
                                <?php echo self::lang('home'); ?>
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <!--
                        <li class="nav-item">
                            <a class="nav-link<?php echo self::currentPage('/account'); ?>" href="<?php echo self::base('/account'); ?>">
                                <?php echo self::lang('account'); ?>
                            </a>
                        </li>
                        -->
                        <li class="nav-item">
                            <a class="nav-link<?php echo self::currentPage('/account/login'); ?>" href="<?php echo self::base('/account/login'); ?>">
                                <?php echo self::lang('login'); ?>
                            </a>
                        </li>
                        <div class="vr"></div>
                        <li class="nav-item">
                            <a class="nav-link<?php echo self::currentPage('/account/register'); ?>" href="<?php echo self::base('/account/register'); ?>">
                                <?php echo self::lang('register'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>