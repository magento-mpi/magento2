<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $page Magento_Cms_Model_Page */
$page = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Cms_Model_Page');
$page//->setId(100) // doesn't work: it triggers update
    ->setTitle('Cms Page 100')
    ->setIdentifier('page100')
    ->setStores(array(0))
    ->setIsActive(1)
    ->setContent('<h1>Cms Page 100 Title</h1>')
    ->setRootTemplate('one_column')
    ->save();

$page = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Cms_Model_Page');
$page->setTitle('Cms Page Design Blank')
    ->setIdentifier('page_design_blank')
    ->setStores(array(0))
    ->setIsActive(1)
    ->setContent('<h1>Cms Page Design Blank Title</h1>')
    ->setRootTemplate('one_column')
    ->setCustomTheme('magento_blank')
    ->save();
