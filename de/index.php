<?php
if (version_compare(phpversion(), '5.2.0', '<')===true) {
	echo "<h1>Invalid PHP version</h1><p>Magento supports only PHP 5.2.0 or newer.</p>";
	exit;
}

require '../app/Mage.php';
Mage::run('german');
