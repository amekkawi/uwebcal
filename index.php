<?php

$configDir = dirname(__FILE__).'/protected/config';

$base = require($configDir.'/web-base.php');
$local = (@include($configDir.'/web-local.php'));

if ((@include_once($yii)) === FALSE) {
?>
<!DOCTYPE HTML>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<style type="text/css">body { font: 14px/1.5 'Helvetica Neue',Arial,'Liberation Sans',FreeSans,sans-serif; } li { padding-top: 3px; } code, pre { background-color: #EEE; padding: 2px 4px; } input { width: 400px; padding: 6px; font-size: 1em; } </style>
<title>Yii Framework Missing | UWebCal</title></head><body>

<h1>Yii Framework Missing</h1>
<p>The Yii framework could not be loaded<?php if (!is_array($local)) { ?>, and is required to install UWebCal<?php } ?>.</p>
<h2>Installing the Yii Framework:</h2>
	<ol>
		<li>Download the Yii Framework from <a href="http://www.yiiframework.com/">www.yiiframework.com/</a></li>
		<li>Uncompress the download, and within it locate the <code>framework</code> directory.</li>
		<li>Copy that directory into your UWebCal directory.</li>
	</ol>
<h2>Using Your Existing Installation of the Yii Framework:</h2>
	<ol>
		<li>Create a file named <code>web-local.php</code> within the <code>protected/config</code> directory in your UWebCal directory.</li>
		<li>Copy the following code into that file, changing the <code>path/to/yii/framework</code> to the path to your Yii installation:
			<div style="padding: 6px;"><input onclick="this.focus(); this.select();" type="text" value="&lt;?php $yii = 'path/to/yii/framework/yii.php';" /></div>
		</li>
	</ol>

</body>
</html>
<?php
	exit;
}

$config = CMap::mergeArray($base, $local);

date_default_timezone_set($config['params']['timezone']);

require_once(dirname(__FILE__).'/protected/components/WebApplication.php');
Yii::createApplication('WebApplication',$config)->run();
