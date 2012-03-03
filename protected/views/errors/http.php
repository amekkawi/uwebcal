<?php
$this->layout = 'error'; 
$this->pageTitle = "HTTP Error $code | " . Yii::app()->name;
?>

<h2>Error <?php echo $code; ?> <?php if ($errorCode != 0) echo "($errorCode)"; ?></h2>

<div class="error-message"><?php echo CHtml::encode($message); ?></div>

<?php return; ?>

<p class="error-file">
File: <?php echo CHtml::encode($file); ?><br/>Line <?php echo CHtml::encode($line); ?>
</p>

<pre>
<?php
$trace = debug_backtrace();
$toDump = array();
foreach ($trace as $i => $file) {
	$toDump[$i] = array();
	foreach ($file as $key => $val) {
		if ($key != "object" && $key != "args")
			$toDump[$i][$key] = $val;
	}
}
var_dump($toDump);
?>
</pre>