<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * CatalogInventory Stock Status Indexer Model
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogInventory_Model_Indexer_Stock extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * @var array
     */
    protected $_matchedEntities = array(
        Mage_CatalogInventory_Model_Stock_Item::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Mage_Catalog_Model_Product::ENTITY => array(
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
            Mage_Index_Model_Event::TYPE_DELETE
        )
    );

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('cataloginventory/indexer_stock');
    }

    /**
     * Retrieve resource instance wrapper
     *
     * @return Mage_CatalogInventory_Model_Mysql4_Indexer_Stock
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('cataloginventory')->__('Stock status');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('cataloginventory')->__('Index product stock status for product list');
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        if ($event->getEntity() == Mage_CatalogInventory_Model_Stock_Item::ENTITY) {
            switch ($event->getType()) {
                case Mage_Index_Model_Event::TYPE_SAVE:
                    $this->_registerStockItemSaveEvent($event);
                    break;
            }
        } else if ($event->getEntity() == Mage_Catalog_Model_Product::ENTITY) {
            switch ($event->getType()) {
                case Mage_Index_Model_Event::TYPE_MASS_ACTION:
                    $this->_registerCatalogProductMassActionEvent($event);
                    break;

                case Mage_Index_Model_Event::TYPE_DELETE:
                    $this->_registerCatalogProductDeleteEvent($event);
                    break;
            }
        }
    }

    /**
     * Register data required by stock item save process in event object
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_CatalogInventory_Model_Indexer_Stock
     */
    protected function _registerStockItemSaveEvent(Mage_Index_Model_Event $event)
    {
        /* @var $object Mage_CatalogInventory_Model_Stock_Item */
        $object      = $event->getDataObject();

        $event->addNewData('reindex_stock', 1);
        $event->addNewData('product_id', $object->getProductId());

        return $this;
    }

    /**
     * Register data required by product delete process in event object
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_CatalogInventory_Model_Indexer_Stock
     */
    protected function _registerCatalogProductDeleteEvent(Mage_Index_Model_Event $event)
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = $event->getDataObject();

        $parentIds = $this->_getResource()->getProductParentsByChild($product->getId());
        if ($parentIds) {
            $event->addNewData('reindex_stock_parent_ids', $parentIds);
        }

        return $this;
    }

    /**
     * Register data required by product mass action process in event object
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_CatalogInventory_Model_Indexer_Stock
     */
    protected function _registerCatalogProductMassActionEvent(Mage_Index_Model_Event $event)
    {
        /* @var $actionObject Varien_Object */
        $actionObject = $event->getDataObject();
        $attributes   = array(
            'status'
        );
        $reindexStock = false;

        // check if attributes changed
        $attrData = $actionObject->getAttributesData();
        if (is_array($attrData)) {
            foreach ($attributes as $attributeCode) {
                if (array_key_exists($attributeCode, $attrData)) {
                    $reindexStock = true;
                    break;
                }
            }
        }

        // check changed websites
        if ($actionObject->getWebsiteIds()) {
            $reindexStock = true;
        }

        // register affected products
        if ($reindexStock) {
            $event->addNewData('reindex_stock_product_ids', $actionObject->getProductIds());
        }

        return $this;
    }

    /**
     * Process event
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $this->callEventHandler($event);
    }
}
