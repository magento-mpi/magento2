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
namespace Magento\TargetRule\Block\Catalog\Product\ProductList;

class Upsell
    extends \Magento\TargetRule\Block\Catalog\Product\ProductList\AbstractProductList
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
        return \Magento\TargetRule\Model\Rule::UP_SELLS;
    }

    /**
     * Retrieve related product collection assigned to product
     *
     * @throws \Magento\Core\Exception
     * @return \Magento\Catalog\Model\Resource\Product\Collection
     */
    public function getLinkCollection()
    {
        if (is_null($this->_linkCollection)) {
            parent::getLinkCollection();
            /**
             * Updating collection with desired items
             */
            \Mage::dispatchEvent('catalog_product_upsell', array(
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
            \Mage::dispatchEvent('catalog_product_upsell', array(
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
