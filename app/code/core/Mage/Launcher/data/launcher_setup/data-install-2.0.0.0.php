<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Launcher_Model_Resource_Setup */
$installer = $this;
Mage::getModel('Mage_Launcher_Model_Page')
    ->load(1)
    ->setPageCode('store_launcher')
    ->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageCode('store_launcher');
$tile->setTileCode('business_info');
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageCode('store_launcher');
$tile->setTileCode('tax');
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageCode('store_launcher');
$tile->setTileCode('payments');
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageCode('store_launcher');
$tile->setTileCode('shipping');
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageCode('store_launcher');
$tile->setTileCode('product');
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

Mage::getModel('Mage_Launcher_Model_Page')
    ->load(2)
    ->setPageCode('promote_store')
    ->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('home_page');
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('content_pages');
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('reports');
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('seo');
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('promotion');
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('catalog_price_rule');
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('rss');
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('wishlist');
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('customer_communication');
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('ebay');
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('related_products');
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setPageCode('promote_store');
$tile->setTileCode('google_analytics');
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();
