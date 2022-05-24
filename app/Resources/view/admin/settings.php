		<div class="wrap">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<div class="d-flex align-items-center">
							<h1 class="h3 fw-bold"><?php echo \KN\Helpers\Base::lang('base.settings'); ?></h1>
						</div>
						<p><?php echo $description; ?></p>
					</div>
					<div class="col-12">
						<form class="row g-2 mb-4" data-kn-form id="settingsUpdate" method="post" action="<?php echo $this->url('management/settings/update'); ?>">
							<div class="form-loader">
								<div class="spinner-border text-light" role="status">
									<span class="visually-hidden"><?php echo \KN\Helpers\Base::lang('base.loading'); ?></span>
								</div>
							</div>
							<div class="col-12 form-info"></div>
							<?php 
							echo '
							<small class="text-muted">
								' . $groups['basic']['items']['last_updated_at']['name'] . ': 
								<strong>' . date('d.m.Y H:i',  (int)$groups['basic']['items']['last_updated_at']['value']) . '</strong>
							</small>';
							$hiddenInputs = '';
							foreach ($groups as $group) {
								?>
								<div class="card p-0 mb-2 shadow-sm">
									<div class="card-header">
										<h2 class="card-header-title mb-0 fw-bold h5">
											<?php echo $group['name']; ?>
										</h2>
									</div>
									<div class="card-body">
										<div class="list-group list-group-flush">
											<?php
											foreach ($group['items'] as $name => $values) {

												if ($values['type'] == 'hidden') {
													$hiddenInputs .= '<input type="hidden" name="' . $name . '" value="' . $values['value'] . '">';
												} else {

													$rightSection = '';
													if ($values['type'] == 'select') {

														foreach ($values['options'] as $val => $txt) {
															$rightSection .= '
															<option value="' . $val . '"' . ($val == $values['value'] ? ' selected' : '') . '>
																' . $txt . '
															</option>';
														}
														$rightSection = '
														<div class="col-auto">
															<select class="form-select"' . (isset($values['required']) !== false ? ' required' : '') . ' name="' . $name . '" id="' . $name . '">
																' . $rightSection . '
															</select>
														</div>';

													} elseif ($values['type'] == 'input') {

														if (isset($values['multilingual']) !== false) {
															$rightSection .= '
															<div class="col-auto">';
															foreach ($languages as $langCode => $langName) {
																$rightSection .= '
																<div class="form-floating mb-1">
																	<input type="'.(isset($values['numeric']) !== false ? 'number' : 'text').'" class="form-control"' . (isset($values['required']) !== false ? ' required' : '') . ' name="' . $name . '['.$langCode.']" id="' . $name . $langCode . '" placeholder="' . $langName . '" value="' . $values['value'][$langCode] . '" />
																	 <label for="' . $name . $langCode . '">' . $langName . '</label>
																</div>';
															}
															$rightSection .= '
															</div>';
															
														} else {
															$rightSection = '
															<div class="col-auto">
																<input type="'.(isset($values['numeric']) !== false ? 'number' : 'text').'" class="form-control"' . (isset($values['required']) !== false ? ' required' : '') . ' name="' . $name . '" id="' . $name . '" placeholder="' . $values['name'] . '" value="' . $values['value'] . '" />
															</div>';
														}


													} elseif ($values['type'] == 'check') {

														$rightSection = '
														<div class="col-auto">
															<div class="form-check form-switch d-flex h-100 align-items-center">
																<input class="form-check-input px-4 py-3" type="checkbox"' . ($values['value'] ? ' checked' : '') . ' role="switch" id="' . $name . '" name="' . $name . '">
															</div>
														</div>';

													}	?>
													<div class="list-group-item">
														<div class="row align-items-center">
															<div class="col">
																<h3 class="font-weight-base mb-1 fw-bold h6">
																	<?php 
																	echo $values['name'] . (
																		isset($values['required']
																	) !== false ? ' <sup class="text-danger">*</sup>' : '')
																	?>
																</h3>
																<?php
																if (isset($values['info']) !== false) {
																	?>
																	<small class="text-muted">
																		<?php echo $values['info']; ?>
																	</small>
																	<?php
																}	?>
															</div>
															<?php echo $rightSection; ?>
														</div>
													</div>
												<?php
												}
											}	?>
										</div>
									</div>
								</div>
								<?php
								echo $hiddenInputs;
							}	?>
							<div class="col-12 d-flex">
								<button type="submit" class="btn ms-auto btn-primary"><?php echo \KN\Helpers\Base::lang('base.update'); ?></button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>