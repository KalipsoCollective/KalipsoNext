		<nav class="navbar navbar-expand-lg navbar-dark kn-nav">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Sandbox</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link<?php echo self::currentPage('sandbox'); ?>" href="<?php echo self::base('sandbox'); ?>">
                                <?php echo self::lang('home'); ?>
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
		<div class="container account-pages">
			<div class="row justify-content-center align-items-center">
				<div class="col-12 col-lg-3 col-md-4 col-sm-6">
					<?php echo self::lang('welcome'); ?>
				</div>
			</div>
		</div>