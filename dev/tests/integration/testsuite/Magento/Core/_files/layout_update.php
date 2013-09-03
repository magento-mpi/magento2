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

/** @var $objectManager Magento_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
Mage::app()->loadAreaPart(Magento_Core_Model_App_Area::AREA_ADMINHTML, Magento_Core_Model_App_Area::PART_CONFIG);
/** @var $theme Magento_Core_Model_Theme */
$theme = $objectManager->create('Magento_Core_Model_Theme');
$theme->setThemePath('test/test')
    ->setThemeVersion('2.0.0.0')
    ->setArea('frontend')
    ->setThemeTitle('Test Theme')
    ->setType(Magento_Core_Model_Theme::TYPE_VIRTUAL)
    ->save();

/** @var $updateNotTemporary Magento_Core_Model_Layout_Update */
$updateNotTemporary = $objectManager->create('Magento_Core_Model_Layout_Update');
$updateNotTemporary->setHandle('test_handle')
    ->setXml('not_temporary')
    ->setStoreId(0)
    ->setThemeId($theme->getId())
    ->save();

/** @var $updateTemporary Magento_Core_Model_Layout_Update */
$updateTemporary = $objectManager->create('Magento_Core_Model_Layout_Update');
$updateTemporary->setHandle('test_handle')
    ->setIsTemporary(1)
    ->setXml('temporary')
    ->setStoreId(0)
    ->setThemeId($theme->getId())
    ->save();
