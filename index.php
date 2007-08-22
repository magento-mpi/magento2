<?php
if (version_compare(phpversion(), '5.2.0', '<')===true) {
	echo "<h1>Invalid PHP version</h1>Magento supports only PHP 5.2.0 or newer.";
	exit;
}

require 'app/Mage.php';
Mage::run('base');
