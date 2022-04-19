<nav class="navbar navbar-expand-lg navbar-dark bg-black fixed-top shadow">
	<div class="container-fluid">
	<a class="navbar-brand" href="#">
		<?php echo KN\Helpers\Base::config('app.name'); ?> 
		<small class="h6">_sandbox</small>
	</a>
	<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
	aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="navbarNav">
		<ul class="navbar-nav">
			<li class="nav-item">
				<a class="nav-link<?php echo $this->currentLink('/sandbox'); ?>" href="<?php echo $this->url('/sandbox'); ?>">
					<?php echo self::h('base')::lang('base.sandbox'); ?>
				</a>
			</li>
			<?php
			foreach ($steps as $step) {
				
				echo '
				<li class="nav-item">
					<a class="nav-link'.$this->currentLink('/sandbox/' . $step).'" href="'.$this->url('/sandbox/' . $step).'">
						'.self::h('base')::lang('base.' . str_replace('-', '_', $step)).'
					</a>
				</li>';
			} ?>
		</ul>
	</div>
	</div>
</nav>
<div class="wrap bg-dark text-light pt-5 min-vh-100">
	<div class="container">
		<div class="row pt-3">
			<div class="col-12">
				<h1><?php echo $head; ?></h1>
				<h2 class="h4"><?php echo $description; ?></h2>
				<?php echo $output; ?>
			</div>
		</div>
	</div>
</div>