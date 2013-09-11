<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $registration \Magento\Core\Model\Theme\Registration */
Mage::app()->loadAreaPart(\Magento\Core\Model\App\Area::AREA_ADMINHTML, \Magento\Core\Model\App\Area::PART_CONFIG);
$registration = Mage::getModel('Magento\Core\Model\Theme\Registration');
$registration->register(
    __DIR__ . DIRECTORY_SEPARATOR . 'design',
    implode(DIRECTORY_SEPARATOR, array('*', '*', 'theme.xml'))
);
