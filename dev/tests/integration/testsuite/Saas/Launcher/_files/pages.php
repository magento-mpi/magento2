<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

// Page that contains correctly configured tiles
/**
 * @var $page Saas_Launcher_Model_Page
 */
$page = Mage::getModel('Saas_Launcher_Model_Page');
$page->setPageCode('landing_page_1')
    ->save();

/**
 * @var $tile Magento_Model_Launcher_Tile
 */
$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('landing_page_1')
    ->setTileCode('tile_1')
    ->setState(Saas_Launcher_Model_Tile::STATE_TODO)
    ->setSortOrder(20)
    ->save();

$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('landing_page_1')
    ->setTileCode('tile_2')
    ->setState(Saas_Launcher_Model_Tile::STATE_TODO)
    ->setSortOrder(10)
    ->save();

// Page that contains tiles without appropriate configuration for testing expected exceptions
$page = Mage::getModel('Saas_Launcher_Model_Page');
$page->setPageCode('landing_page_50')
    ->save();

$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('landing_page_50')
    ->setTileCode('tile_50')
    ->setState(Saas_Launcher_Model_Tile::STATE_TODO)
    ->setSortOrder(20)
    ->save();

