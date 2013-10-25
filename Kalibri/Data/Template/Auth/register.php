<?php
	if( isset( $errorMsg ) && count( $errorMsg ) )
	{
		echo '<span class="error">'.implode('<br/>', $errorMsg).'</span>';
	}
?>
<form action="<?php echo \Url::site('/auth/register/')?>" method="post">
	<div>
		<label for="login"><?php echo tr('Login')?>:</label><input name="login"/>
	</div>
	<div>
		<label for="password"><?php echo tr('Password')?>:</label><input type="password" name="password"/>
	</div>
	<div>
		<label for="re-password"><?php echo tr('Re-password')?>:</label><input type="password" name="re-password"/>
	</div>
	<div>
		<input type="submit" value="<?php echo tr('Send')?>"/> <a href="<?php echo \Url::site('/auth/login')?>"><?php echo tr('Already have profile?')?></a>
	</div>
</form>