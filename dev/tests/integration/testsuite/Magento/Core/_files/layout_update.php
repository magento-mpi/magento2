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

/** @var $objectManager \Magento\ObjectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
\Mage::app()->loadAreaPart(\Magento\Core\Model\App\Area::AREA_ADMINHTML, \Magento\Core\Model\App\Area::PART_CONFIG);
/** @var $theme \Magento\Core\Model\Theme */
$theme = $objectManager->create('Magento\Core\Model\Theme');
$theme->setThemePath('test/test')
    ->setThemeVersion('2.0.0.0')
    ->setArea('frontend')
    ->setThemeTitle('Test Theme')
    ->setType(\Magento\Core\Model\Theme::TYPE_VIRTUAL)
    ->save();

/** @var $updateNotTemporary \Magento\Core\Model\Layout\Update */
$updateNotTemporary = $objectManager->create('Magento\Core\Model\Layout\Update');
$updateNotTemporary->setHandle('test_handle')
    ->setXml('not_temporary')
    ->setStoreId(0)
    ->setThemeId($theme->getId())
    ->save();

/** @var $updateTemporary \Magento\Core\Model\Layout\Update */
$updateTemporary = $objectManager->create('Magento\Core\Model\Layout\Update');
$updateTemporary->setHandle('test_handle')
    ->setIsTemporary(1)
    ->setXml('temporary')
    ->setStoreId(0)
    ->setThemeId($theme->getId())
    ->save();
