<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

Magento_TestFramework_Helper_Bootstrap::getInstance()->reinitialize(array(
    Mage::PARAM_APP_DIRS => array(
        \Magento\Core\Model\Dir::THEMES => dirname(__DIR__) . '/design'
    )
));

Mage::app()->loadAreaPart(\Magento\Core\Model\App\Area::AREA_ADMINHTML, \Magento\Core\Model\App\Area::PART_CONFIG);

/** @var $registration \Magento\Core\Model\Theme\Registration */
$registration = Mage::getModel('Magento\Core\Model\Theme\Registration');
$registration->register(
    __DIR__,
    implode(DIRECTORY_SEPARATOR, array('*', '*', 'theme.xml'))
);

