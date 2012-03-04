<h1>Login:</h1>
<div class="loginform">
	<form action="<?php echo CHtml::encode(Yii::app()->createUrl($this->id . '/' . $this->action->id, $_GET)); ?>" method="POST">
		<?php
		foreach (Yii::app()->user->flashes as $flash) {
			?><div class="flash"><?php echo CHtml::encode($flash); ?></div><?php
		}
		?>
		<div class="field">
			<div class="label">Username:</div>
			<div class="input">
				<input type="text" name="username" value="<?php if (isset($_POST['username'])) echo CHtml::encode($_POST['username']); ?>" />
			</div>
		</div>
		<div class="field">
			<div class="label">Password:</div>
			<div class="input">
				<input type="password" name="password" />
			</div>
		</div>
		<div class="submit">
			<input type="submit" value="Login" />
		</div>
	</form>
</div>
