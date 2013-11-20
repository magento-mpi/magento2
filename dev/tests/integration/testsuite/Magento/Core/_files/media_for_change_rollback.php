<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$designDir = \Magento\TestFramework\Helper\Bootstrap::getInstance()->getAppInstallDir() . '/media_for_change';
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Filesystem')->delete($designDir);
