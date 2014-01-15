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


$themeDirectory = \Magento\TestFramework\Helper\Bootstrap::getInstance()->getAppInstallDir() . '/media_for_change';

\Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
    \Magento\Filesystem::PARAM_APP_DIRS => array(
        \Magento\Filesystem::VAR_DIR => array('path' => $themeDirectory),
    ),
));
/** @var $objectManager \Magento\ObjectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var $directoryWrite \Magento\Filesystem\Directory\Write */
$directoryWrite = $objectManager->create('Magento\Filesystem')->getDirectoryWrite(\Magento\Filesystem::VAR_DIR);
$directoryWrite->delete();
