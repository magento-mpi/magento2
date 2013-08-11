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

/** @var $objectManager Magento_ObjectManager */
$objectManager = Mage::getObjectManager();
Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_ADMINHTML, Mage_COre_Model_App_Area::PART_CONFIG);
/** @var $theme Mage_Core_Model_Theme */
$theme = $objectManager->create('Mage_Core_Model_Theme');
$theme->setThemePath('test/test')
    ->setThemeVersion('2.0.0.0')
    ->setArea('frontend')
    ->setThemeTitle('Test Theme')
    ->setType(Mage_Core_Model_Theme::TYPE_VIRTUAL)
    ->save();

/** @var $updateNotTemporary Mage_Core_Model_Layout_Update */
$updateNotTemporary = $objectManager->create('Mage_Core_Model_Layout_Update');
$updateNotTemporary->setHandle('test_handle')
    ->setXml('not_temporary')
    ->setStoreId(0)
    ->setThemeId($theme->getId())
    ->save();

/** @var $updateTemporary Mage_Core_Model_Layout_Update */
$updateTemporary = $objectManager->create('Mage_Core_Model_Layout_Update');
$updateTemporary->setHandle('test_handle')
    ->setIsTemporary(1)
    ->setXml('temporary')
    ->setStoreId(0)
    ->setThemeId($theme->getId())
    ->save();
