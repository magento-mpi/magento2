<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
Mage::app()->loadAreaPart(Magento_Core_Model_App_Area::AREA_ADMINHTML, Magento_Core_Model_App_Area::PART_CONFIG);
/** @var $registration Magento_Core_Model_Theme_Registration */
$registration = Mage::getModel('Magento_Core_Model_Theme_Registration');
$registration->register(
    __DIR__ . DIRECTORY_SEPARATOR . 'design',
    implode(DIRECTORY_SEPARATOR, array('*', '*', 'theme.xml'))
);
