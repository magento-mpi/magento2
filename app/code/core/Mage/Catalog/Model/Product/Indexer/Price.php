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
 * @category   Mage
 * @package    Mage_
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Catalog_Model_Product_Indexer_Price extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Matched Entities instruction array
     *
     * @var array
     */
    protected $_matchedEntities = array(
        Mage_Catalog_Model_Product::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        )
    );

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product_indexer_price');
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('catalog')->__('Product Prices');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('catalog')->__('Index product prices');
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        /* @var $product Mage_Catalog_Model_Product */
        $product = $event->getDataObject();

        $reindexPrice = $product->getIsRelationsChanged();

        $attributes = array(
            'price',
            'special_price',
            'special_from_date',
            'special_to_date',
            'tax_class_id',
            'status'
        );

        foreach ($attributes as $attributeCode) {
            $reindexPrice = $reindexPrice || $product->dataHasChangedFor($attributeCode);
        }

        if ($reindexPrice) {
            $event->addNewData('reindex_price', 1);
        }
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
