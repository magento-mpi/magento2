<?php
/**
 * Extended product service schema mapper.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Catalog_Service_Product_Extended_Mapper extends Mage_Catalog_Service_Product_Mapper
{
    protected $_wishlistHelper;
    protected $_compareHelper;
    protected $_productHelper;
    /** @var \Mage_Core_Model_Factory_Helper */
    protected $_helperFactory;
    /** @var Mage_Catalog_Block_Product_View */
    protected $_productViewBlock;
    /** @var Mage_Catalog_Helper_Output */
    protected $_helperOutput;

    function __construct(
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Catalog_Block_Product_View $productViewBlock,
        Mage_Catalog_Helper_Output $helperOutput
    ) {
        $this->_helperFactory = $helperFactory;
    }

    /**
     * Retrieves full unfiltered schema for the object.
     *
     * @return mixed
     */
    protected function _getSchema()
    {
        $schema = parent::_getSchema();

        $schema += array(
            'wishlistEnabled',
            'wishListAddUrl',
            'compareAddUrl',
            'emailToFriendUrl',
            'name',
            'shortDescription',
            'canEmailToFriend',
            'hasOptions',
            'optionsContainer',
            'productType',
            'jsonConfig',
            'productHasOptions',
            'hasShortDescription',
            'entityId',
            'isSalable',
            'submitUrlForProduct',
        );

        return $schema;
    }

    /**
     * {@inheritdoc}
     *
     * @param string        $schemaElement
     * @param Varien_Object $object
     * @return mixed
     */
    protected function _map($schemaElement, Varien_Object $object)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $object;
        $value = parent::_map($schemaElement, $product);

        if ($value) {
            return $value;
        }

        switch ($schemaElement) {
            case 'wishlistEnabled':
                return $this->_getWishlistHelper()->isAllow();
            case 'wishListAddUrl':
                return $this->_getWishlistHelper()->getAddUrl($product);
            case 'compareAddUrl':
                return $this->_getCompareHelper()->getAddUrl($product);
            case 'emailToFriendUrl':
                return $this->_getProductHelper()->getEmailToFriendUrl($product);
            case 'name':
                return $this->_helperOutput->productAttribute($product, $product->getName(), 'name');
            case 'shortDescription':
                return $this->_helperOutput->productAttribute(
                    $product, nl2br($product->getShortDescription()), 'short_description'
                );
            case 'canEmailToFriend':
                return $this->_productViewBlock->canEmailToFriend();
            case 'hasOptions':
                return $this->_productViewBlock->hasOptions();
            case 'optionsContainer':
                return $product->getOptionsContainer() == 'container1' ? 'container1' : 'container2';
            case 'productType':
                return $product->getTypeId();
            case 'jsonConfig':
                return $this->_productViewBlock->getJsonConfig();
            case 'productHasOptions':
                return $product->getHasOptions();
            case 'hasShortDescription':
                return $product->hasData('shortDescription');
            case 'entityId':
                return $product->getId();
            case 'isSalable':
                return $product->isSalable();
            case 'submitUrlForProduct':
                return $this->_productViewBlock->getSubmitUrl($product);
        }
    }

    /**
     * @return Mage_Wishlist_Helper_Data
     */
    protected function _getWishlistHelper()
    {
        if (empty($this->_wishlistHelper)) {
            $this->_wishlistHelper = $this->_wishlistHelper = $this->_helperFactory->get('Mage_Wishlist_Helper_Data');
        }

        return $this->_wishlistHelper;
    }

    /**
     * @return Mage_Catalog_Helper_Product_Compare
     */
    protected function _getCompareHelper()
    {
        if (empty($this->_compareHelper)) {
            $this->_compareHelper = $this->_helperFactory->get('Mage_Catalog_Helper_Product_Compare');
        }

        return $this->_compareHelper;
    }

    /**
     * @return Mage_Catalog_Helper_Product
     */
    protected function _getProductHelper()
    {
        if (empty($this->_productHelper)) {
            $this->_productHelper = $this->_helperFactory->get('Mage_Catalog_Helper_Product');
        }

        return $this->_productHelper;
    }
}
