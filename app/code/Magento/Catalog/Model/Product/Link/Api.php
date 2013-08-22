<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product link api
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Link_Api extends Magento_Catalog_Model_Api_Resource
{
    /**
     * Product link type mapping, used for references and validation
     *
     * @var array
     */
    protected $_typeMap = array(
        'related'       => Magento_Catalog_Model_Product_Link::LINK_TYPE_RELATED,
        'up_sell'       => Magento_Catalog_Model_Product_Link::LINK_TYPE_UPSELL,
        'cross_sell'    => Magento_Catalog_Model_Product_Link::LINK_TYPE_CROSSSELL,
        'grouped'       => Magento_Catalog_Model_Product_Link::LINK_TYPE_GROUPED
    );

    public function __construct()
    {
        $this->_storeIdSessionField = 'product_store_id';
    }

    /**
     * Retrieve product link associations
     *
     * @param string $type
     * @param int|sku $productId
     * @param  string $identifierType
     * @return array
     */
    public function items($type, $productId, $identifierType = null)
    {
        $typeId = $this->_getTypeId($type);

        $product = $this->_initProduct($productId, $identifierType);

        $link = $product->getLinkInstance()
            ->setLinkTypeId($typeId);

        $collection = $this->_initCollection($link, $product);

        $result = array();

        foreach ($collection as $linkedProduct) {
            $row = array(
                'product_id' => $linkedProduct->getId(),
                'type'       => $linkedProduct->getTypeId(),
                'set'        => $linkedProduct->getAttributeSetId(),
                'sku'        => $linkedProduct->getSku()
            );

            foreach ($link->getAttributes() as $attribute) {
                $row[$attribute['code']] = $linkedProduct->getData($attribute['code']);
            }

            $result[] = $row;
        }

        return $result;
    }

    /**
     * Add product link association
     *
     * @param string $type
     * @param int|string $productId
     * @param int|string $linkedProductId
     * @param array $data
     * @param  string $identifierType
     * @return boolean
     */
    public function assign($type, $productId, $linkedProductId, $data = array(), $identifierType = null)
    {
        $typeId = $this->_getTypeId($type);

        $product = $this->_initProduct($productId, $identifierType);

        $link = $product->getLinkInstance()
            ->setLinkTypeId($typeId);

        $collection = $this->_initCollection($link, $product);
        $idBySku = $product->getIdBySku($linkedProductId);
        if ($idBySku) {
            $linkedProductId = $idBySku;
        }

        $links = $this->_collectionToEditableArray($collection);

        $links[(int)$linkedProductId] = array();

        foreach ($collection->getLinkModel()->getAttributes() as $attribute) {
            if (isset($data[$attribute['code']])) {
                $links[(int)$linkedProductId][$attribute['code']] = $data[$attribute['code']];
            }
        }

        try {
            if ($type == 'grouped') {
                $link->getResource()->saveGroupedLinks($product, $links, $typeId);
            } else {
                $link->getResource()->saveProductLinks($product, $links, $typeId);
            }

            $_linkInstance = Mage::getSingleton('Magento_Catalog_Model_Product_Link');
            $_linkInstance->saveProductRelations($product);

            $indexerStock = Mage::getModel('Magento_CatalogInventory_Model_Stock_Status');
            $indexerStock->updateStatus($productId);

            $indexerPrice = Mage::getResourceModel('Magento_Catalog_Model_Resource_Product_Indexer_Price');
            $indexerPrice->reindexProductIds($productId);
        } catch (Exception $e) {
            $this->_fault('data_invalid', __('The linked product does not exist.'));
        }

        return true;
    }

    /**
     * Update product link association info
     *
     * @param string $type
     * @param int|string $productId
     * @param int|string $linkedProductId
     * @param array $data
     * @param  string $identifierType
     * @return boolean
     */
    public function update($type, $productId, $linkedProductId, $data = array(), $identifierType = null)
    {
        $typeId = $this->_getTypeId($type);

        $product = $this->_initProduct($productId, $identifierType);

        $link = $product->getLinkInstance()
            ->setLinkTypeId($typeId);

        $collection = $this->_initCollection($link, $product);

        $links = $this->_collectionToEditableArray($collection);

        $idBySku = $product->getIdBySku($linkedProductId);
        if ($idBySku) {
            $linkedProductId = $idBySku;
        }

        foreach ($collection->getLinkModel()->getAttributes() as $attribute) {
            if (isset($data[$attribute['code']])) {
                $links[(int)$linkedProductId][$attribute['code']] = $data[$attribute['code']];
            }
        }

        try {
            if ($type == 'grouped') {
                $link->getResource()->saveGroupedLinks($product, $links, $typeId);
            } else {
                $link->getResource()->saveProductLinks($product, $links, $typeId);
            }

            $_linkInstance = Mage::getSingleton('Magento_Catalog_Model_Product_Link');
            $_linkInstance->saveProductRelations($product);

            $indexerStock = Mage::getModel('Magento_CatalogInventory_Model_Stock_Status');
            $indexerStock->updateStatus($productId);

            $indexerPrice = Mage::getResourceModel('Magento_Catalog_Model_Resource_Product_Indexer_Price');
            $indexerPrice->reindexProductIds($productId);
        } catch (Exception $e) {
            $this->_fault('data_invalid', __('The linked product does not exist.'));
        }

        return true;
    }

    /**
     * Remove product link association
     *
     * @param string $type
     * @param int|string $productId
     * @param int|string $linkedProductId
     * @param  string $identifierType
     * @return boolean
     */
    public function remove($type, $productId, $linkedProductId, $identifierType = null)
    {
        $typeId = $this->_getTypeId($type);

        $product = $this->_initProduct($productId, $identifierType);

        $link = $product->getLinkInstance()
            ->setLinkTypeId($typeId);

        $collection = $this->_initCollection($link, $product);

        $idBySku = $product->getIdBySku($linkedProductId);
        if ($idBySku) {
            $linkedProductId = $idBySku;
        }

        $links = $this->_collectionToEditableArray($collection);

        if (isset($links[$linkedProductId])) {
            unset($links[$linkedProductId]);
        }

        try {
            $link->getResource()->saveProductLinks($product, $links, $typeId);
        } catch (Exception $e) {
            $this->_fault('not_removed');
        }

        return true;
    }

    /**
     * Retrieve attribute list for specified type
     *
     * @param string $type
     * @return array
     */
    public function attributes($type)
    {
        $typeId = $this->_getTypeId($type);

        $attributes = Mage::getModel('Magento_Catalog_Model_Product_Link')
            ->getAttributes($typeId);

        $result = array();

        foreach ($attributes as $attribute) {
            $result[] = array(
                'code'  => $attribute['code'],
                'type'  => $attribute['type']
            );
        }

        return $result;
    }

    /**
     * Retrieve link types
     *
     * @return array
     */
    public function types()
    {
        return array_keys($this->_typeMap);
    }

    /**
     * Retrieve link type id by code
     *
     * @param string $type
     * @return int
     */
    protected function _getTypeId($type)
    {
        if (!isset($this->_typeMap[$type])) {
            $this->_fault('type_not_exists');
        }

        return $this->_typeMap[$type];
    }

    /**
     * Initialize and return product model
     *
     * @param int $productId
     * @param  string $identifierType
     * @return Magento_Catalog_Model_Product
     */
    protected function _initProduct($productId, $identifierType = null)
    {
        $product = Mage::helper('Magento_Catalog_Helper_Product')->getProduct($productId, null, $identifierType);
        if (!$product->getId()) {
            $this->_fault('product_not_exists');
        }

        return $product;
    }

    /**
     * Initialize and return linked products collection
     *
     * @param Magento_Catalog_Model_Product_Link $link
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    protected function _initCollection($link, $product)
    {
        $collection = $link
            ->getProductCollection()
            ->setIsStrongMode()
            ->setProduct($product);

        return $collection;
    }

    /**
     * Export collection to editable array
     *
     * @param Magento_Catalog_Model_Resource_Product_Link_Product_Collection $collection
     * @return array
     */
    protected function _collectionToEditableArray($collection)
    {
        $result = array();

        foreach ($collection as $linkedProduct) {
            $result[$linkedProduct->getId()] = array();

            foreach ($collection->getLinkModel()->getAttributes() as $attribute) {
                $result[$linkedProduct->getId()][$attribute['code']] = $linkedProduct->getData($attribute['code']);
            }
        }

        return $result;
    }
} // Class Magento_Catalog_Model_Product_Link_Api End
