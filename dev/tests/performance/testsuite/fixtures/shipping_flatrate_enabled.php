<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var Magento_Core_Model_Config_Data $configData */
$configData = Mage::getModel('Magento_Core_Model_Config_Data');
$configData->setPath('carriers/flatrate/active')
    ->setScope(Magento_Core_Model_Config::SCOPE_DEFAULT)
    ->setScopeId(0)
    ->setValue(1)
    ->save();

Mage::app()->cleanCache(array(Magento_Core_Model_Config::CACHE_TAG));
