<?php
/**
 * Public alias for the application entry point
 *
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

require __DIR__ . '/../app/bootstrap.php';
$params = $_SERVER;
$params[Mage_Core_Model_App::INIT_OPTION_DIRS][Mage_Core_Model_App_Dir::PUB] = '';
Mage::run($params);
