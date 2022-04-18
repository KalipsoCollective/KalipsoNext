<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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
				<a class="nav-link" href="<?php echo $this->url('/sandbox'); ?>">Welcome</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="<?php echo $this->url('/sandbox/db-init'); ?>">DB Init</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="<?php echo $this->url('/sandbox/db-seed'); ?>">DB Seed</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="<?php echo $this->url('/sandbox/php-info'); ?>">PHP Info</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="<?php echo $this->url('/sandbox/session'); ?>">Session</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="<?php echo $this->url('/sandbox/clear-storage'); ?>">Clear Storage</a>
			</li>
		</ul>
	</div>
	</div>
</nav>