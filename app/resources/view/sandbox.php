		<nav class="navbar navbar-expand-lg navbar-dark kn-nav fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Sandbox</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link<?php echo self::currentPage('sandbox'); ?>" href="<?php echo self::base('sandbox'); ?>">
                                <?php echo self::lang('welcome'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link<?php echo self::currentPage('sandbox/db-init'); ?>" href="<?php echo self::base('sandbox/db-init'); ?>">
                                <?php echo self::lang('prepare_database'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link<?php echo self::currentPage('sandbox/db-seed'); ?>" href="<?php echo self::base('sandbox/db-seed'); ?>">
                                <?php echo self::lang('seed_database'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link<?php echo self::currentPage('sandbox/php-info'); ?>" href="<?php echo self::base('sandbox/php-info'); ?>">
                                <?php echo self::lang('php_info'); ?>
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link<?php echo self::currentPage(); ?>" href="<?php echo self::base(); ?>">
                            	<span class="mdi mdi-arrow-left"></span> 
                                <?php echo self::lang('back_to_home'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
		<div class="container sandbox">
			<div class="row">
				<?php
				$section = isset($attributes['action']) !== false ? $attributes['action'] : 'welcome';
				switch ($section) {
					case 'db-init':
						$pageTitle = self::lang('prepare_database');
						break;

					case 'db-seed':
						$pageTitle = self::lang('seed_database');
						break;

					case 'php-info':
						$pageTitle = self::lang('php_info');
						break;
					
					default:
						$pageTitle = self::lang('welcome');
						break;
				}
				?>
				<div class="col-12 col-lg-3 col-md-4 col-sm-6">
					<h2><?php echo $pageTitle; ?></h2>
				</div>
			</div>
		</div>