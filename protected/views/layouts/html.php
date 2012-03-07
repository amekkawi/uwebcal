<?php
header('Content-Type: text/html; charset=UTF-8');
if (Yii::app()->clientScriptPackage !== NULL)
	Yii::app()->clientScript->registerPackage(Yii::app()->clientScriptPackage);
?>
<!DOCTYPE HTML>
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
<?php
if ($this instanceof Controller)
	$this->echoHtmlPart('htmlheader');
?>
</head>

<body>
<?php

if ($this instanceof Controller)
	$this->echoHtmlPart('header');
?>

<div id="UWebCal" class="yui3-cssreset yui3-cssfonts yui3-cssbase"><?php echo $content; ?></div>

<?php
if ($this instanceof Controller)
	$this->echoHtmlPart('footer');

?>
</body>
</html>