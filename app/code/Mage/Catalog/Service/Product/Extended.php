<?php
class Mage_Catalog_Service_Product_Extended extends Mage_Catalog_Service_Product
{
    protected function _getObjectData(Varien_Object $object)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $object;
        $data = parent::_getObjectData($product);

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

        $propertiesWithoutMapping = array(
            'isDisabled',
            'isSuperGroup',
            'isSuperConfig',
            'isGrouped',
            'isConfigurable',
            'isSuper',
            'isVisibleInCatalog',
            'isDuplicable',
            'isAvailable',
            'isVirtual',
            'isRecurring',
            'isInStock',
            'isComposite',
            'hasCustomOptions',
            'canAffectOptions',
            'options',
            'customOptions',
            'defaultAttributeSetId',
            'reservedAttributes',
            'image',
            'storeId',
            'storeIds',
            'price',
            'sku',
            'weight',
            'status',
            'categoryId',
            'categoryIds',
            'attributes',
            'groupPrice',
            'tierPrice',
            'tierPriceCount',
            'finalPrice',
            'minimalPrice',
            'visibleInCatalogStatuses',
            'visibleStatuses',
            'visibleInSiteVisibilities',
            'urlInStore',
            'urlPath',
            'websiteIds',
            'relatedProductIds',
            'crossSellProductIds',
            'mediaAttributes',
            'mediaGalleryImages',
        );

        $data[static::RELATED_DATA_KEY] = array();

        foreach ($propertiesWithoutMapping as $property) {
            $first3Letters = substr($property, 0, 3) === 'has';

            if ($first3Letters == 'can' || $first3Letters === 'has' || substr($property, 0, 3) === 'is') {
                $method = $property;
            } else {
                $method = 'get' . ucwords($property);
            }

            $data[static::RELATED_DATA_KEY][$property] = $product->$method();
        }

        $data[static::RELATED_DATA_KEY] += array(
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
            'formattedPrice' => $product->getFormatedPrice(),
            'formattedTierPrice' => $product->getFormatedTierPrice(),
        );

        return $data;
    }
}
