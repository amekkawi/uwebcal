<?php
$this->layout = 'error'; 
$this->pageTitle = "Internal Error $code | " . Yii::app()->name;
?>

<h2>Error <?php echo $code; ?></h2>

<div class="error-message"><?php echo Yii::t('app', 'An internal error was encountered. If you continue to experience this issue please contact {admin}.', array('{admin}'=>$adminInfo)); ?></div>