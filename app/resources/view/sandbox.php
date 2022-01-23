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
						$output = '';
						$dbSchema = require self::path('app/resources/db_schema.php');

						if (isset($_GET['start']) !== false) {

							$init = (new App\Core\DB)->dbInit($dbSchema);

							if ($init === 0) {
								$output .= '<p class="text-success">Database has been prepared successfully.</p>';
							} else {
								$output .= '<p class="text-danger">There was a problem while preparing the database. -> ' . $init . '</p>';
							}

						} else {

							foreach ($dbSchema['tables'] as $table => $detail) {

								$cols = '
								<div class="table-responsive">
									<table class="table table-dark table-sm table-hover table-striped caption-bottom">
										<thead>
											<tr>
												<th scope="col">Column</th>
												<th scope="col">Type</th>
												<th scope="col">AI</th>
												<th scope="col">Attribute</th>
												<th scope="col">Default</th>
												<th scope="col">Index</th>
											</tr>
										</thead>
										<tbody>';

								foreach ($detail['cols'] as $col => $colDetail) {

									$cols .= '
											<tr>
												<th scope="row">'.$col.'</th>
												<td scope="col">
													'.$colDetail['type'].(
														isset($colDetail['type_values']) !== false ? 
														(is_array($colDetail['type_values']) ? '('.implode(',', $colDetail['type_values']).')' : 
															'('.$colDetail['type_values']).')' : ''
													).'
												</td>
												<td scope="col">'.(isset($colDetail['auto_inc']) !== false ? 'yes' : 'no').'</td>
												<td scope="col">'.(isset($colDetail['attr']) !== false ? $colDetail['attr'] : '').'</td>
												<td scope="col">'.(isset($colDetail['default']) !== false ? $colDetail['default'] : '').'</td>
												<td scope="col">'.(isset($colDetail['index']) !== false ? $colDetail['index'] : '').'</td>
											<tr>';

								}

								$tableValues = '';

								$tableValues = '<h3 class="small text-muted">
									'.(
										isset($dbSchema['table_values']['specific'][$table]['charset']) !== false ? 
											'Charset: <strong>'.$dbSchema['table_values']['specific'][$table]['charset'].'</strong><br>' : 
											''
									).'
									'.(
										isset($dbSchema['table_values']['specific'][$table]['collate']) !== false ? 
											'Collate: <strong>'.$dbSchema['table_values']['specific'][$table]['collate'].'</strong><br>' : 
											''
									).'
									'.(
										isset($dbSchema['table_values']['specific'][$table]['engine']) !== false ? 
											'Engine: <strong>'.$dbSchema['table_values']['specific'][$table]['engine'].'</strong><br>' : 
											''
									).'
								</h3>';

								$cols .= '
										</tbody>
										<caption>'.$tableValues.'</caption>
									</table>
								</div>';

								$output .= '<details><summary>'.$table.'</summary>'.$cols.'</details>';
							}

							if ($output != '') {
								$output = '
								<h3 class="small text-muted">
									Database Name: 
									<strong>'.self::config('database.name').'</strong><br>
									Database Charset: 
									<strong>'.(isset($dbSchema['table_values']['charset']) !== false ? $dbSchema['table_values']['charset'] : '-').'</strong><br>
									Database Collate: 
									<strong>'.(isset($dbSchema['table_values']['collate']) !== false ? $dbSchema['table_values']['collate'] : '-').'</strong><br>
									Database Engine: 
									<strong>'.(isset($dbSchema['table_values']['engine']) !== false ? $dbSchema['table_values']['engine'] : '-').'</strong><br>
								</h3>
								'.$output.'
								<p class="small text-danger mt-5">If there is no database named <strong>'.self::config('database.name').'</strong>, add it with the <strong>'.self::config('database.collation').'</strong> collation.</p>
								<a class="btn btn-dark btn-sm" href="'.self::base('sandbox/db-init?start').'">Good, Prepare!</a>';
							}
						}
						break;

					case 'db-seed':
						$pageTitle = self::lang('seed_database');
						$output = '';
						$dbSchema = require self::path('app/resources/db_schema.php');

						if (isset($_GET['start']) !== false) {

							$output = '<p class="text-muted">Seeding...</p>';
							$init = (new App\Core\DB)->dbSeed($dbSchema);

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