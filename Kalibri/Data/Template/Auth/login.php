<?php
	if( isset( $errorMsg ) && $errorMsg !== '' )
	{
		echo $errorMsg;
	}
?>
<form action="<?php echo \Url::site('/auth/login')?>" method="post">
	<div>
		<label for="login"><?php echo tr('Login')?>:</label>
		<input type="login" name="login"/>
	</div>
	<div>
		<label for="password"><?php echo tr('Password')?>:</label>
		<input type="password" name="password"/>
	</div>
	<div>
		<input type="submit" value="<?php echo tr('Send')?>"/>
		<a href="<?php echo \Url::site('/auth/register')?>"><?php echo tr('Sign up')?></a>
	</div>
</form>