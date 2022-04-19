		<nav class="navbar navbar-expand-lg navbar-dark kn-nav fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">sandbox</a>
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
                        <li class="nav-item">
                            <a class="nav-link<?php echo self::currentPage('sandbox/session'); ?>" href="<?php echo self::base('sandbox/session'); ?>">
                                <?php echo self::lang('session'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link<?php echo self::currentPage('sandbox/clear-storage'); ?>" href="<?php echo self::base('sandbox/clear-storage'); ?>">
                                <?php echo self::lang('clear_storage'); ?>
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
				$section = self::getAttribute('action') ? self::getAttribute('action') : 'welcome';
				switch ($section) {
					case 'db-init':
						$pageTitle = self::lang('prepare_database');
						
						break;

					case 'db-seed':
						$pageTitle = self::lang('seed_database');
						$output = '';
						$dbSchema = require self::path('app/Resources/db_schema.php');

						if (isset($_GET['start']) !== false) {

							$output = '<p class="text-muted">Seeding...</p>';
							$init = (new KN\Core\DB)->dbSeed($dbSchema);

							if ($init === 0) {
								$output .= '<p class="text-success">Database has been seeded successfully.</p>';
							} else {
								$output .= '<p class="text-danger">There was a problem while seeding the database. -> ' . $init. '</p>';
							}

						} else {

							foreach ($dbSchema['data'] as $table => $detail) {

								$cols = '
								<div class="table-responsive">
									<table class="table table-dark table-sm table-hover table-striped">
										<thead>
											<tr>
												<th scope="col">Table</th>
												<th scope="col">Data</th>
											</tr>
										</thead>
										<tbody>';

								foreach ($detail as $tableDataDetail) {

									$dataList = '<ul class="list-group list-group-flush">';
									foreach ($tableDataDetail as $col => $data) {
										$dataList .= '<li class="list-group-item d-flex justify-content-between align-items-start space"><strong>'.$col.'</strong> <span class="ml-2">'.$data.'</span></li>';
									}
									$dataList .= '</ul>';

									$cols .= '
											<tr>
												<th scope="row">'.$table.'</th>
												<td scope="col">
													'.$dataList.'
												</td>
											<tr>';

								}
								$cols .= '
									</table>
								</div>';

								$output .= '<details><summary>'.$table.'</summary>'.$cols.'</details>';
							}

							if ($output != '') {
								$output .= '<a class="btn btn-dark mt-5 btn-sm" href="'.self::base('sandbox/db-seed?start').'">Good, Seed!</a>';
							}
						}
						break;

					case 'php-info':
						$pageTitle = self::lang('php_info');
						ob_start ();
						phpinfo ();
						$output = ob_get_clean();
						$output = preg_replace('/(<script[^>]*>.+?<\/script>|<style[^>]*>.+?<\/style>|<meta[^>]*>|<title[^>]*>.+?<\/title>)/is', "", $output);
						$output = '<pre>'.trim(strip_tags($output)).'</pre>';
						break;

					case 'session':
						$pageTitle = self::lang('session');
						ob_start ();
						self::dump($_SESSION);
						$output = ob_get_clean();
						break;

					case 'clear-storage':
						$pageTitle = self::lang('clear_storage');
						ob_start ();

						$deleteAction = (isset($_GET['delete']) !== false AND count($_GET['delete'])) ? $_GET['delete'] : null;
						if ($deleteAction) {
							$glob = glob(self::path('app/Storage/*'), GLOB_BRACE);
							if ($glob AND count($glob)) {
								foreach ($glob as $folder) {
									if (in_array(basename($folder), $deleteAction))
										self::removeDir($folder);	
								}
								echo '<p class="text-success">Storage folder is cleared.</p>';

							}
						}

						$glob = glob(self::path('app/Storage/*'), GLOB_BRACE);

						if ($glob AND count($glob)) {

							echo '
							<form method="get">
								<div class="table-responsive">
									<table class="table table-hover table-borderless table-striped">
										<thead>
										    <tr>
												<th scope="col" width="5%">#</th>
												<th scope="col">Folder</th>
										    </tr>
										</thead>
										<tbody>';
										$deleteBtn = false;
										foreach ($glob as $folder) {
										
											if (! is_dir($folder)) 
												continue;

											$size = self::dirSize($folder);
											if (! $deleteBtn AND $size) 
												$deleteBtn = true;

											$basename = basename($folder);

											echo '
											<tr>
												<td>
													<div class="form-check">
														<input class="form-check-input" 
															type="checkbox" name="delete[]" 
															value="' . $basename . '"
															'.(! $size ? ' disabled' : ' checked').'>
													</div>
												</td>
												<td>/' . $basename . ' 
													<small class="'.(! $size ? 'text-muted' : 'text-primary').'">
														' . self::formatSize($size) . '
													</small>
												</td>
											</tr>';

										}
									echo '
										</tbody>
									</table>
								</div>
								<button type="submit" class="btn btn-danger btn-sm"'.(! $deleteBtn ? ' disabled' : '').'>Delete</button>
							</form>';
						} else {
							echo '<p class="text-danger">Folder not found!</p>';
						}
						$output = ob_get_clean();
						break;
					
					default:
						$pageTitle = self::lang('welcome');
						$output = '<p class="lead">Sandbox is the part prepared for basic database operations, if you want, you can update and continue using it while developing.</p>';
						break;
				}
				?>
				<div class="col-12">
					<h2><?php echo $pageTitle; ?></h2>
					<?php echo $output; ?>
				</div>
			</div>
		</div>