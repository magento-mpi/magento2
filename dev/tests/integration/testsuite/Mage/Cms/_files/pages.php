<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$page = new Mage_Cms_Model_Page;
$page//->setId(100) // doesn't work: it triggers update
    ->setTitle('Cms Page 100')
    ->setIdentifier('page100')
    ->setStores(array(0))
    ->setIsActive(1)
    ->setContent('<h1>Cms Page 100 Title</h1>')
    ->setRootTemplate('one_column')
    ->save()
;

$page = new Mage_Cms_Model_Page;
$page->setTitle('Cms Page Design Modern')
    ->setIdentifier('page_design_modern')
    ->setStores(array(0))
    ->setIsActive(1)
    ->setContent('<h1>Cms Page Design Modern Title</h1>')
    ->setRootTemplate('one_column')
    ->setCustomTheme('default/modern/default')
    ->save()
;
