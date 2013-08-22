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
Mage::app()->loadAreaPart(Magento_Core_Model_App_Area::AREA_ADMINHTML, Magento_Core_Model_App_Area::PART_CONFIG);
$designDir = Magento_Test_Helper_Bootstrap::getInstance()->getAppInstallDir() . '/media_for_change';
$themeDir = $designDir . DIRECTORY_SEPARATOR . 'frontend/test_default';
$sourcePath = dirname(__DIR__) . '/Model/_files/design/frontend/test_publication/';

mkdir($themeDir . '/images', 0777, true);

// Copy all files to fixture location
$mTime = time() - 10; // To ensure that all files, changed later in test, will be recognized for publication
$files = array('theme.xml', 'style.css', 'sub.css', 'images/square.gif', 'images/rectangle.gif');
foreach ($files as $file) {
    copy($sourcePath . $file, $themeDir . DIRECTORY_SEPARATOR . $file);
    touch($themeDir . DIRECTORY_SEPARATOR . $file, $mTime);
}

/** @var $registration Magento_Core_Model_Theme_Registration */
$registration = Mage::getModel('Magento_Core_Model_Theme_Registration');
$registration->register(
    $designDir,
    implode(DIRECTORY_SEPARATOR, array('*', '*', 'theme.xml'))
);
