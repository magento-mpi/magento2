<?php
/**
 * API Product service.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Catalog_Service_Product extends Mage_Webapi_Service_Abstract
{
    /**
     * Returns info about one particular product.
     * @todo Develop format for methods' input so it conforms with input schema
     *
     * @param int $productId
     * @return array
     */
    public function item($productId)
    {
        $data = $this->_getData($productId);

        return $data;
    }

    /**
     * Returns info about several products.
     *
     * @param array $productIds
     * @return array
     */
    public function items(array $productIds)
    {
        $data = $this->_getCollectionData($productIds);

        return $data;
    }

    /**
     * {@inheritdoc}
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _getDictionary($product)
    {
        if (empty($this->_dictionary)) {
            /** @var $productBlock Mage_Catalog_Block_Product_View */
            $productBlock = $this->_objectManager->create(
                'Mage_Catalog_Block_Product_View', array('data' => array('product_id' => $product->getId()))
            );

            /** @var $productCompareHelper Mage_Catalog_Helper_Product_Compare */
            $productCompareHelper = $productBlock->helper('Mage_Catalog_Helper_Product_Compare');
            /** @var $productHelper Mage_Catalog_Helper_Product */
            $productHelper = $productBlock->helper('Mage_Catalog_Helper_Product');
            /** @var $wishlistHelper Mage_Wishlist_Helper_Data */
            $wishlistHelper = $productBlock->helper('Mage_Wishlist_Helper_Data');

            $this->_dictionary = array(
                'url' => $product->getProductUrl(),
                'submitUrl' => $productBlock->getSubmitUrl($product),
                'compareUrl' => $productCompareHelper->getAddUrl($product),
                'fileViewUrl' => $productBlock->getViewFileUrl('Mage_Catalog::js/price-option.js'),
                'emailToFriendUrl' => $productHelper->getEmailToFriendUrl($product),
                'canEmailToFriend' => $productBlock->canEmailToFriend(),
                'hasOptions' => $productBlock->hasOptions(),
                'wishlistDataAllowed' => $wishlistHelper->isAllow(),
                'optionsContainer' => $productBlock->getOptionsContainer(),
                'jsonConfig' => $productBlock->getJsonConfig(),
                'isSaleable' => $product->isSalable(),
            );
        }

        return $this->_dictionary;
    }

    /**
     * Returns model which operated by current service.
     *
     * @param mixed  $productId
     * @param string $fieldsetId
     * @return Mage_Catalog_Model_Product
     */
    protected function _getObject($productId, $fieldsetId = '')
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->_objectManager->create('Mage_Catalog_Model_Product');
        // Depends on MDS-167
        // $product->setFieldset($fieldsetId);
        $product->load($productId);

        return $product;
    }

    /**
     * Get collection object of the current service
     *
     * @param array  $productIds
     * @param string $fieldsetId
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _getObjectCollection(array $productIds, $fieldsetId = '')
    {
        $collection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Product_Collection');
        // Depends on MDS-167
        // $collection->setFieldset($fieldsetId);
        $collection->addIdFilter($productIds);

        return $collection;
    }
}
