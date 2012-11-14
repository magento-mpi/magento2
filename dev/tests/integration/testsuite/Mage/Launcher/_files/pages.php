<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Launcher
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @var $page Mage_Launcher_Model_Page
 */
$page = Mage::getModel('Mage_Launcher_Model_Page');
$page->setCode('landing_page_1')
    ->save();
$pageId = $page->getId();

/**
 * @var $tile Mage_Model_Launcher_Tile
 */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageId($pageId)
    ->setCode('tile_1')
    ->setState(1)
    ->save();

$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageId($pageId)
    ->setCode('tile_2')
    ->setState(1)
    ->save();
