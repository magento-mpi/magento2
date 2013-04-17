<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$type = 'Mage_Catalog_Block_Product_Widget_New';
$theme = Mage::getDesign()->setDefaultDesignTheme()->getDesignTheme();

/** @var $widgetInstance Mage_Widget_Model_Widget_Instance */
$widgetInstance = Mage::getModel('Mage_Widget_Model_Widget_Instance');
$widgetInstance
    ->setType($type)
    ->setThemeId($theme->getId())
    ->save();

Mage::register('current_widget_instance', $widgetInstance);
