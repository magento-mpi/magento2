<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * List on composite module names for Magento EE
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

$objectManager = new \Magento\Framework\App\ObjectManager();
return $objectManager->create('\Magento\Framework\Module\Setup\Migration')->getCompositeModules();
