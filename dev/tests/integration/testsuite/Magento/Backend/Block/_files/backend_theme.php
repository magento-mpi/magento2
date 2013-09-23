<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $registration Magento_Core_Model_Theme_Registration */
Mage::app()->loadAreaPart(Magento_Core_Model_App_Area::AREA_ADMINHTML, Magento_Core_Model_App_Area::PART_CONFIG);
$registration = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Theme_Registration');
$registration->register(
    __DIR__ . DIRECTORY_SEPARATOR . 'design',
    implode(DIRECTORY_SEPARATOR, array('*', '*', 'theme.xml'))
);
