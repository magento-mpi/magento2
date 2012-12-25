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

/** @var $theme Mage_Core_Model_Theme */
$theme = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
$theme->setThemePath('test/test')
    ->setThemeVersion('2.0.0.0')
    ->setArea('frontend')
    ->setThemeTitle('Test Theme')
    ->save();

/** @var $layoutUpdate1 Mage_Core_Model_Layout_Update */
$layoutUpdate1 = Mage::getObjectManager()->create('Mage_Core_Model_Layout_Update');
$layoutUpdate1->setHandle('test_handle')
    ->setXml('not_temporary')
    ->setStoreId(0)
    ->setThemeId($theme->getId())
    ->save();

/** @var $layoutUpdate2 Mage_Core_Model_Layout_Update */
$layoutUpdate2 = Mage::getObjectManager()->create('Mage_Core_Model_Layout_Update');
$layoutUpdate2->setHandle('test_handle')
    ->setIsTemporary(1)
    ->setXml('temporary')
    ->setStoreId(0)
    ->setThemeId($theme->getId())
    ->save();
