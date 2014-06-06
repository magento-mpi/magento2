<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

$loader = include __DIR__ . '/../../../../vendor/autoload.php';
$loader->addPsr4('Magento\\Filesystem\\', array(__DIR__ .  '/../src/'));
