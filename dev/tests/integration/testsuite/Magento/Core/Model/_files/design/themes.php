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
Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_App')
    ->loadAreaPart(Magento_Core_Model_App_Area::AREA_ADMINHTML, Magento_Core_Model_App_Area::PART_CONFIG);
/** @var $registration Magento_Core_Model_Theme_Registration */
$registration = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Theme_Registration');
$registration->register(
    __DIR__,
    implode(DIRECTORY_SEPARATOR, array('*', '*', 'theme.xml'))
);
