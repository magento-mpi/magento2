<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $objectManager \Magento\Framework\ObjectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$objectManager->get('Magento\Framework\App\AreaList')
    ->getArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
    ->load(\Magento\Framework\App\Area::PART_CONFIG);
/** @var $theme \Magento\Framework\View\Design\ThemeInterface */
$theme = $objectManager->create('Magento\Framework\View\Design\ThemeInterface');
$theme->setThemePath(
    'test/test'
)->setThemeVersion(
    '2.0.0.0'
)->setArea(
    'frontend'
)->setThemeTitle(
    'Test Theme'
)->setType(
    \Magento\Framework\View\Design\ThemeInterface::TYPE_VIRTUAL
)->save();

/** @var $updateNotTemporary \Magento\Core\Model\Layout\Update */
$updateNotTemporary = $objectManager->create('Magento\Core\Model\Layout\Update');
$updateNotTemporary->setHandle(
    'test_handle'
)->setXml(
    'not_temporary'
)->setStoreId(
    0
)->setThemeId(
    $theme->getId()
)->save();

/** @var $updateTemporary \Magento\Core\Model\Layout\Update */
$updateTemporary = $objectManager->create('Magento\Core\Model\Layout\Update');
$updateTemporary->setHandle(
    'test_handle'
)->setIsTemporary(
    1
)->setXml(
    'temporary'
)->setStoreId(
    0
)->setThemeId(
    $theme->getId()
)->save();
