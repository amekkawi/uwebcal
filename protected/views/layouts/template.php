<?php
if (strpos($this->calendar['html'].'', '{{content}}') === false)
	throw new CHttpException(500, Yii::t('app', 'Cannot display calendar template since it is missing the "{tag} tag".', array('{tag}' => '{{content}}')));

echo str_replace('{{content}}', $content, $this->calendar['html'].'');