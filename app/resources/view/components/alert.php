<?php

/**
 *  Bootstrap Alert Component 
 **/

return [
	'component'	=> '
	<div class="alert [CLASS] alert-dismissible fade show" role="alert">
		[ICON]
        [TITLE] [MESSAGE]
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>',
	'classes'	=> [
		'default'	=> 'alert-primary',
		'success'	=> 'alert-success',
		'alert'		=> 'alert-warning',
		'error'		=> 'alert-danger'
	]

];