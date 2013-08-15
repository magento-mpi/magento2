<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Saas_Launcher_Model_Resource_Setup */
$installer = $this;

// Add 'Store Launcher' page
Mage::getModel('Saas_Launcher_Model_Page')
    ->load(1)
    ->setPageCode('store_launcher')
    ->save();

/** @var $tile Saas_Launcher_Model_Tile */
$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('store_launcher');
$tile->setTileCode('business_info');
$tile->setState(Saas_Launcher_Model_Tile::STATE_TODO);
$tile->setSortOrder(50);
$tile->save();

$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('store_launcher');
$tile->setTileCode('store_design');
$tile->setState(Saas_Launcher_Model_Tile::STATE_TODO);
$tile->setSortOrder(100);
$tile->save();

$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('store_launcher');
$tile->setTileCode('payments');
$tile->setState(Saas_Launcher_Model_Tile::STATE_TODO);
$tile->setSortOrder(150);
$tile->save();

$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('store_launcher');
$tile->setTileCode('shipping');
$tile->setState(Saas_Launcher_Model_Tile::STATE_TODO);
$tile->setSortOrder(200);
$tile->save();

$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('store_launcher');
$tile->setTileCode('tax');
$tile->setState(Saas_Launcher_Model_Tile::STATE_TODO);
$tile->setSortOrder(250);
$tile->save();

$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('store_launcher');
$tile->setTileCode('product');
$tile->setState(Saas_Launcher_Model_Tile::STATE_TODO);
$tile->setSortOrder(300);
$tile->save();

// Add 'Promote Store' page
Mage::getModel('Saas_Launcher_Model_Page')
    ->load(2)
    ->setPageCode('promote_store')
    ->save();

/** @var $tile Saas_Launcher_Model_Tile */
$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('home_page');
$tile->setState(Saas_Launcher_Model_Tile::STATE_TODO);
$tile->setSortOrder(50);
$tile->save();

/** @var $tile Saas_Launcher_Model_Tile */
$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('content_pages');
$tile->setState(Saas_Launcher_Model_Tile::STATE_TODO);
$tile->setSortOrder(100);
$tile->save();

/** @var $tile Saas_Launcher_Model_Tile */
$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('seo');
$tile->setState(Saas_Launcher_Model_Tile::STATE_TODO);
$tile->setSortOrder(150);
$tile->save();

/** @var $tile Saas_Launcher_Model_Tile */
$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('customer_communication');
$tile->setState(Saas_Launcher_Model_Tile::STATE_TODO);
$tile->setSortOrder(200);
$tile->save();

/** @var $tile Saas_Launcher_Model_Tile */
$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('reports');
$tile->setState(Saas_Launcher_Model_Tile::STATE_TODO);
$tile->setSortOrder(250);
$tile->save();

/** @var $tile Saas_Launcher_Model_Tile */
$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('promotion');
$tile->setState(Saas_Launcher_Model_Tile::STATE_TODO);
$tile->setSortOrder(300);
$tile->save();

/** @var $tile Saas_Launcher_Model_Tile */
$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('catalog_price_rule');
$tile->setState(Saas_Launcher_Model_Tile::STATE_TODO);
$tile->setSortOrder(350);
$tile->save();

/** @var $tile Saas_Launcher_Model_Tile */
$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('google_analytics');
$tile->setState(Saas_Launcher_Model_Tile::STATE_TODO);
$tile->setSortOrder(400);
$tile->save();

/** @var $tile Saas_Launcher_Model_Tile */
$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('ebay');
$tile->setState(Saas_Launcher_Model_Tile::STATE_TODO);
$tile->setSortOrder(450);
$tile->save();

/** @var $tile Saas_Launcher_Model_Tile */
$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('wishlist');
$tile->setState(Saas_Launcher_Model_Tile::STATE_TODO);
$tile->setSortOrder(500);
$tile->save();

/** @var $tile Saas_Launcher_Model_Tile */
$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('rss');
$tile->setState(Saas_Launcher_Model_Tile::STATE_TODO);
$tile->setSortOrder(550);
$tile->save();

/** @var $tile Saas_Launcher_Model_Tile */
$tile = Mage::getModel('Saas_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('related_products');
$tile->setState(Saas_Launcher_Model_Tile::STATE_TODO);
$tile->setSortOrder(600);
$tile->save();
