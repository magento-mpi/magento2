<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$designDir = Magento_Test_Bootstrap::getInstance()->getInstallDir() . '/media_for_change';
$themeDir = $designDir . DIRECTORY_SEPARATOR . '/frontend/test/default';
$sourcePath = dirname(__DIR__) . '/Model/_files/design/frontend/test/publication/';

mkdir($themeDir . '/images', 0777, true);

// Copy all files to fixture location
$mTime = time() - 10; // To ensure that all files, changed later in test, will be recognized for publication
$files = array('theme.xml', 'style.css', 'sub.css', 'images/square.gif', 'images/rectangle.gif');
foreach ($files as $file) {
    copy($sourcePath . $file, $themeDir . DIRECTORY_SEPARATOR . $file);
    touch($themeDir . DIRECTORY_SEPARATOR . $file, $mTime);
}

/** @var $registration Mage_Core_Model_Theme_Registration */
$registration = Mage::getModel('Mage_Core_Model_Theme_Registration');
$registration->register(
    $designDir,
    implode(DIRECTORY_SEPARATOR, array('*','*', '*', 'theme.xml'))
);

Magento_Test_Bootstrap::getInstance()->reinitialize(array(
    Mage_Core_Model_App::INIT_OPTION_DIRS => array(
        Mage_Core_Model_Dir::VIEW => $designDir
    )
));
Mage::getDesign()->setDesignTheme('test/default');
