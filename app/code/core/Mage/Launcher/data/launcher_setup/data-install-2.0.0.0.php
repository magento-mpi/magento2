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
    ->setCode('store_launcher')
    ->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setCode('business_info');
$tile->setPageId(1);
$tile->setState(0);
$tile->save();

$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setCode('tax');
$tile->setPageId(1);
$tile->setState(0);
$tile->save();

Mage::getModel('Mage_Launcher_Model_Page')
    ->load(2)
    ->setCode('promote_store')
    ->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setCode('home_page');
$tile->setPageId(2);
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setCode('content_pages');
$tile->setPageId(2);
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setCode('reports');
$tile->setPageId(2);
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setCode('seo');
$tile->setPageId(2);
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setCode('promotion');
$tile->setPageId(2);
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setCode('catalog_price_rule');
$tile->setPageId(2);
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setCode('rss');
$tile->setPageId(2);
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setCode('wishlist');
$tile->setPageId(2);
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setCode('customer_communication');
$tile->setPageId(2);
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();

/** @var $tile Mage_Launcher_Model_Tile */
$tile = Mage::getModel('Mage_Launcher_Model_Tile');
$tile->setCode('ebay');
$tile->setPageId(2);
$tile->setState(Mage_Launcher_Model_Tile::STATE_TODO);
$tile->save();
