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
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Data retreiver abstract model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogIndex_Model_Data_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Defines when product type has children
     *
     * @var boolean
     */
    protected $_haveChildren = array(
                        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_TIERS=>true,
                        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_PRICES=>true,
                        Mage_CatalogIndex_Model_Retreiver::CHILDREN_FOR_ATTRIBUTES=>true,
                        );

    /**
     * Defines when product type has parents
     *
     * @var boolean
     */
    protected $_haveParents = true;

    const LINK_GET_CHILDREN = 1;
    const LINK_GET_PARENTS = 1;

    protected function _construct()
    {
        $this->_init('catalogindex/data_abstract');
    }

    /**
     * Return all children ids
     *
     * @param Mage_Core_Model_Store $store
     * @param int $parentId
     * @return mixed
     */
    public function getChildProductIds($store, $parentIds)
    {
        if (!$this->_haveChildren) {
            return false;
        }

        if (!$this->_getLinkSettings()) {
            return false;
        }

        return $this->fetchLinkInformation($store, $this->_getLinkSettings(), self::LINK_GET_CHILDREN, $parentIds);
    }

    /**
     * Return all parent ids
     *
     * @param Mage_Core_Model_Store $store
     * @param int $childId
     * @return mixed
     */
    public function getParentProductIds($store, $childIds)
    {
        if (!$this->_haveParents) {
            return false;
        }

        if (!$this->_getLinkSettings()) {
            return false;
        }

        return $this->fetchLinkInformation($store, $this->_getLinkSettings(), self::LINK_GET_PARENTS, $childIds);
    }

    /**
     * Returns an array of product children/parents
     *
     * @param Mage_Core_Model_Store $store
     * @param array $settings
     * @param int $type
     * @param int $suppliedId
     */
    protected function fetchLinkInformation($store, $settings, $type, $suppliedId)
    {
        switch ($type) {
            case self::LINK_GET_CHILDREN:
                $whereField = $settings['parent_field'];
                $idField = $settings['child_field'];
                break;

            case self::LINK_GET_PARENTS:
                $idField = $settings['parent_field'];
                $whereField = $settings['child_field'];
                break;
        }

        $additional = array();
        if (isset($settings['additional']) && is_array($settings['additional'])) {
            $additional = $settings['additional'];
        }

        return $this->getResource()->fetchLinkInformation($store->getId(), $settings['table'], $idField, $whereField, $suppliedId, $additional);
    }

    /**
     * Fetch final price for product
     *
     * @param int $product
     * @param Mage_Core_Model_Store $store
     * @param Mage_Customer_Model_Group $group
     * @return float
     */
    public function getFinalPrice($product, $store, $group)
    {
        $basePrice = $specialPrice = $specialPriceFrom = $specialPriceTo = null;
        $priceId = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'price');
        $specialPriceId = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'special_price');
        $specialPriceFromId = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'special_from_date');
        $specialPriceToId = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'special_to_date');

        $attributes = array($priceId, $specialPriceId, $specialPriceFromId, $specialPriceToId);

        $productData = $this->getAttributeData($product, $attributes, $store);
        foreach ($productData as $row) {
            switch ($row['attribute_id']) {
                case $priceId:
                    $basePrice = $row['value'];
                break;
                case $specialPriceId:
                    $specialPrice = $row['value'];
                break;
                case $specialPriceFromId:
                    $specialPriceFrom = $row['value'];
                break;
                case $specialPriceToId:
                    $specialPriceTo = $row['value'];
                break;
            }
        }

        $finalPrice = Mage::getSingleton('catalog/product_type')
            ->priceFactory($this->getTypeCode())
            ->calculatePrice($basePrice, $specialPrice, $specialPriceFrom, $specialPriceTo, false, $store, $group, $product);

        return $finalPrice;
    }

    /**
     * Return minimal prices for specified products
     *
     * @param array $products
     * @param Mage_Core_Model_Store $store
     * @return array
     */
    public function getMinimalPrice($products, $store)
    {
        $priceAttributes = array(
            Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'tier_price'),
            Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'price'));

        $data = $this->getResource()->getMinimalPrice($products, $priceAttributes, $store->getId());

        $this->setMinimalPriceData($data);
        $eventData = array('indexer'=>$this, 'product_ids'=>$products, 'store'=>$store);
        Mage::dispatchEvent('catalogindex_get_minimal_price', $eventData);
        $data = $this->getMinimalPriceData();

        return $data;
    }

    /**
     * Return tier data for specified products in specified store
     *
     * @param array $products
     * @param Mage_Core_Model_Store $store
     * @return mixed
     */
    public function getTierPrices($products, $store)
    {
        return $this->getResource()->getTierPrices($products, $store->getWebsiteId());
    }

    /**
     * Retreive specified attribute data for specified products from specified store
     *
     * @param array $products
     * @param array $attributes
     * @param Mage_Core_Model_Store $store
     */
    public function getAttributeData($products, $attributes, $store)
    {
        return $this->getResource()->getAttributeData($products, $attributes, $store->getId());
    }

    /**
     * Retreive product type code
     *
     * @return string
     */
    public function getTypeCode()
    {
        Mage::throwException('Define custom data retreiver with getTypeCode function');
    }

    /**
     * Get child link table and field settings
     *
     * @return mixed
     */
    protected function _getLinkSettings()
    {
        return false;
    }

    /**
     * Returns if type supports children of the specified type
     *
     * @param int $type
     * @return bool
     */
    public function areChildrenIndexable($type)
    {
        if (!$this->_haveChildren || !isset($this->_haveChildren[$type]) || !$this->_haveChildren[$type]) {
            return false;
        }
        return true;
    }
}