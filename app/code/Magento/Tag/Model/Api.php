<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product Tag API
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Model_Api extends Magento_Catalog_Model_Api_Resource
{
    /**
     * Tag data
     *
     * @var Magento_Tag_Helper_Data
     */
    protected $_tagData = null;

    /**
     * @param Magento_Tag_Helper_Data $tagData
     */
    public function __construct(
        Magento_Tag_Helper_Data $tagData
    ) {
        $this->_tagData = $tagData;
    }

    /**
     * Retrieve list of tags for specified product
     *
     * @param int $productId
     * @param string|int $store
     * @return array
     */
    public function items($productId, $store = null)
    {
        $result = array();
        // fields list to return
        $fieldsForResult = array('tag_id', 'name');

        /** @var $product Magento_Catalog_Model_Product */
        $product = Mage::getModel('Magento_Catalog_Model_Product')->load($productId);
        if (!$product->getId()) {
            $this->_fault('product_not_exists');
        }

        /** @var $tags Magento_Tag_Model_Resource_Tag_Collection */
        $tags = Mage::getModel('Magento_Tag_Model_Tag')->getCollection()->joinRel()->addProductFilter($productId);
        if ($store) {
            $tags->addStoreFilter($this->_getStoreId($store));
        }

        /** @var $tag Magento_Tag_Model_Tag */
        foreach ($tags as $tag) {
            $result[$tag->getId()] = $tag->toArray($fieldsForResult);
        }

        return $result;
    }

    /**
     * Retrieve tag info as array('name'-> .., 'status' => ..,
     * 'base_popularity' => .., 'products' => array($productId => $popularity, ...))
     *
     * @param int $tagId
     * @param string|int $store
     * @return array
     */
    public function info($tagId, $store)
    {
        $result = array();
        $storeId = $this->_getStoreId($store);
        /** @var $tag Magento_Tag_Model_Tag */
        $tag = Mage::getModel('Magento_Tag_Model_Tag')->setStoreId($storeId)->setAddBasePopularity()->load($tagId);
        if (!$tag->getId()) {
            $this->_fault('tag_not_exists');
        }
        $result['status'] = $tag->getStatus();
        $result['name'] = $tag->getName();
        $result['base_popularity'] = (is_numeric($tag->getBasePopularity())) ? $tag->getBasePopularity() : 0;
        // retrieve array($productId => $popularity, ...)
        $result['products'] = array();
        $relatedProductsCollection = $tag->getEntityCollection()->addTagFilter($tagId)
            ->addStoreFilter($storeId)->addPopularity($tagId);
        foreach ($relatedProductsCollection as $product) {
            $result['products'][$product->getId()] = $product->getPopularity();
        }

        return $result;
    }

    /**
     * Add tag(s) to product.
     * Return array of added/updated tags as array($tagName => $tagId, ...)
     *
     * @param array $data
     * @return array
     */
    public function add($data)
    {
        $data = $this->_prepareDataForAdd($data);
        /** @var $product Magento_Catalog_Model_Product */
        $product = Mage::getModel('Magento_Catalog_Model_Product')->load($data['product_id']);
        if (!$product->getId()) {
            $this->_fault('product_not_exists');
        }
        /** @var $customer Magento_Customer_Model_Customer */
        $customer = Mage::getModel('Magento_Customer_Model_Customer')->load($data['customer_id']);
        if (!$customer->getId()) {
            $this->_fault('customer_not_exists');
        }
        $storeId = $this->_getStoreId($data['store']);

        try {
            /** @var $tag Magento_Tag_Model_Tag */
            $tag = Mage::getModel('Magento_Tag_Model_Tag');
            $tagHelper = $this->_tagData;
            $tagNamesArr = $tagHelper->cleanTags($tagHelper->extractTags($data['tag']));
            foreach ($tagNamesArr as $tagName) {
                // unset previously added tag data
                $tag->unsetData();
                $tag->loadByName($tagName);
                if (!$tag->getId()) {
                    $tag->setName($tagName)
                        ->setFirstCustomerId($customer->getId())
                        ->setFirstStoreId($storeId)
                        ->setStatus($tag->getPendingStatus())
                        ->save();
                }
                $tag->saveRelation($product->getId(), $customer->getId(), $storeId);
                $result[$tagName] = $tag->getId();
            }
        } catch (Magento_Core_Exception $e) {
            $this->_fault('save_error', $e->getMessage());
        }

        return $result;
    }

    /**
     * Change existing tag information
     *
     * @param int $tagId
     * @param array $data
     * @param string|int $store
     * @return bool
     */
    public function update($tagId, $data, $store)
    {
        $data = $this->_prepareDataForUpdate($data);
        $storeId = $this->_getStoreId($store);
        /** @var $tag Magento_Tag_Model_Tag */
        $tag = Mage::getModel('Magento_Tag_Model_Tag')->setStoreId($storeId)->setAddBasePopularity()->load($tagId);
        if (!$tag->getId()) {
            $this->_fault('tag_not_exists');
        }

        // store should be set for 'base_popularity' to be saved in Magento_Tag_Model_Resource_Tag::_afterSave()
        $tag->setStore($storeId);
        if (isset($data['base_popularity'])) {
            $tag->setBasePopularity($data['base_popularity']);
        }
        if (isset($data['name'])) {
            $tag->setName(trim($data['name']));
        }
        if (isset($data['status'])) {
            // validate tag status
            if (!in_array($data['status'], array(
                $tag->getApprovedStatus(), $tag->getPendingStatus(), $tag->getDisabledStatus()))) {
                $this->_fault('invalid_data');
            }
            $tag->setStatus($data['status']);
        }

        try {
            $tag->save();
        } catch (Magento_Core_Exception $e) {
            $this->_fault('save_error', $e->getMessage());
        }

        return true;
    }

    /**
     * Remove existing tag
     *
     * @param int $tagId
     * @return bool
     */
    public function remove($tagId)
    {
        /** @var $tag Magento_Tag_Model_Tag */
        $tag = Mage::getModel('Magento_Tag_Model_Tag')->load($tagId);
        if (!$tag->getId()) {
            $this->_fault('tag_not_exists');
        }
        try {
            $tag->delete();
        } catch (Magento_Core_Exception $e) {
            $this->_fault('remove_error', $e->getMessage());
        }

        return true;
    }

    /**
     * Check data before add
     *
     * @param array $data
     * @return array
     */
    protected function _prepareDataForAdd($data)
    {
        if (!isset($data['product_id']) or !isset($data['tag'])
            or !isset($data['customer_id']) or !isset($data['store'])) {
            $this->_fault('invalid_data');
        }

        return $data;
    }

    /**
     * Check data before update
     *
     * @param $data
     * @return
     */
    protected function _prepareDataForUpdate($data)
    {
        // $data should contain at least one field to change
        if ( !(isset($data['name']) or isset($data['status']) or isset($data['base_popularity']))) {
            $this->_fault('invalid_data');
        }

        return $data;
    }
}
