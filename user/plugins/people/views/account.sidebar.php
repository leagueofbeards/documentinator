<?php namespace Habari; ?>
<div class="three columns sidebar">
	<nav>
		<h4><a href="<?php URL::out('display_useraccount', array('slug' => $person->username)); ?>" title="Your Account">Your Account</a></h4>
		<hr style="margin-bottom:10px;">		
		<ul id="pages">
			<li><a href="<?php URL::out('display_integrations', array('slug' => $person->username)); ?>">Integrations</a></li>
			<li><a href="<?php URL::out('display_notifications'); ?>">Notifications</a></li>			
			<li><a href="<?php URL::out('display_billing'); ?>">Billing Details</a></li>
			<li><a href="<?php URL::out('display_burninate', array('slug' => $person->username)); ?>">Close Your Account</a></li>
		</ul>
	</nav>
</div>