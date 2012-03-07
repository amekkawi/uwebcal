<?php
header('Content-Type: text/html; charset=UTF-8');
if (Yii::app()->clientScriptPackage !== NULL)
	Yii::app()->clientScript->registerPackage(Yii::app()->clientScriptPackage);

if (strpos($this->calendar['html'].'', '{{content}}') === false)
	throw new CHttpException(500, Yii::t('app', 'Cannot display calendar template since it is missing the "{tag} tag".', array('{tag}' => '{{content}}')));

$content = '<div id="UWebCal" class="yui3-cssreset yui3-cssfonts yui3-cssbase">' . $content . '</div>';
echo str_replace('{{content}}', $content, $this->calendar['html'].'');