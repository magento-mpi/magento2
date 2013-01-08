<?php
/**
 * Application entry point
 *
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once __DIR__ . '/app/bootstrap.php';

$appOptions = new Mage_Core_Model_App_Options($_SERVER);
Mage::run($appOptions->getRunCode(), $appOptions->getRunType(), $appOptions->getRunOptions());
