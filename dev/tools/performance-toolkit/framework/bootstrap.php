<?php
/**
 * Toolkit framework bootstrap script
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$magentoBaseDir = realpath(__DIR__ . '/../../../../');

require_once "$magentoBaseDir/app/bootstrap.php";
return $magentoBaseDir;
