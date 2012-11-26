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

// Page that contains correctly configured tiles
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
    ->setState(Mage_Launcher_Model_Tile::STATE_TODO)
    ->setSortOrder(20)
    ->save();

$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageId($pageId)
    ->setCode('tile_2')
    ->setState(Mage_Launcher_Model_Tile::STATE_TODO)
    ->setSortOrder(10)
    ->save();

// Page that contains tiles without appropriate configuration for testing expected exceptions
$page = Mage::getModel('Mage_Launcher_Model_Page');
$page->setCode('landing_page_50')
    ->save();
$pageId = $page->getId();
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageId($pageId)
    ->setCode('tile_50')
    ->setState(Mage_Launcher_Model_Tile::STATE_TODO)
    ->setSortOrder(20)
    ->save();

