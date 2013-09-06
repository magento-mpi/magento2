<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Catalog Product List Upsell Block
 *
 * @category   Magento
 * @package    Magento_TargetRule
 */
class Magento_TargetRule_Block_Catalog_Product_ProductList_Upsell
    extends Magento_TargetRule_Block_Catalog_Product_ProductList_Abstract
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_noform';

    /**
     * Retrieve Catalog Product List Type identifier
     *
     * @return int
     */
    public function getProductListType()
    {
        return Magento_TargetRule_Model_Rule::UP_SELLS;
    }

    /**
     * Retrieve related product collection assigned to product
     *
     * @throws Magento_Core_Exception
     * @return Magento_Catalog_Model_Resource_Product_Collection
     */
    public function getLinkCollection()
    {
        if (is_null($this->_linkCollection)) {
            parent::getLinkCollection();
            /**
             * Updating collection with desired items
             */
            Mage::dispatchEvent('catalog_product_upsell', array(
                'product'       => $this->getProduct(),
                'collection'    => $this->_linkCollection,
                'limit'         => $this->getPositionLimit()
            ));
        }

        return $this->_linkCollection;
    }

    /**
     * Get ids of all related products
     *
     * @return array
     */
    public function getAllIds()
    {
        if (is_null($this->_allProductIds)) {
            if (!$this->isShuffled()) {
                return parent::getAllIds();
            }

            $ids = parent::getAllIds();
            $ids = new \Magento\Object(array('items' => array_flip($ids)));
            /**
             * Updating collection with desired items
             */
            Mage::dispatchEvent('catalog_product_upsell', array(
                'product'       => $this->getProduct(),
                'collection'    => $ids,
                'limit'         => null,
            ));

            $this->_allProductIds = array_keys($ids->getItems());
            shuffle($this->_allProductIds);
        }

        return $this->_allProductIds;
    }
}
