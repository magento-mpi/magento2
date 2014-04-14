<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * List on composite module names for Magento CE
 */
require_once __DIR__ . '/../../../../../../app/bootstrap.php';
require_once realpath(
    dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))
) . '/app/code/Magento/Core/Model/Resource/SetupInterface.php';
require_once realpath(
    dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))
) . '/app/code/Magento/Core/Model/Resource/Setup.php';
require_once realpath(
    dirname(dirname(dirname(dirname(dirname(dirname(__DIR__))))))
) . '/app/code/Magento/Core/Model/Resource/Setup/Migration.php';

$objectManager = new \Magento\App\ObjectManager();
return $objectManager->create('\Magento\Module\Setup\Migration')->getCompositeModules();
