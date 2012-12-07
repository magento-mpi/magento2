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

/** @var $registration Mage_Core_Model_Theme_Registration */
$registration = Mage::getModel('Mage_Core_Model_Theme_Registration');
$registration->register(
    __DIR__,
    implode(DIRECTORY_SEPARATOR, array('*','*', '*', 'theme.xml'))
);

Magento_Test_Bootstrap::getInstance()->reinitialize(array(
    Mage_Core_Model_App::INIT_OPTION_DIRS => array(
        Mage_Core_Model_Dir::VIEW => __DIR__
    )
));
Mage::getDesign()->setDesignTheme('test/default');
