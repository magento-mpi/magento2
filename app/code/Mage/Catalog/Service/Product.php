<?php
/**
 * API Product service.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @Service catalogProduct
 * @Version 1.0
 * @Path /catalog/product
 */
class Mage_Catalog_Service_Product extends Mage_Core_Service_Entity_Abstract
{
    /**
     * Returns info about one particular product.
     *
     * @Type call
     * @Method GET
     * @Path /{id}
     * @Bindings [REST]
     * @Consumes /resources/product/item/input.xsd
     * @Produces /resources/product/item/output.xsd
     *
     * @param Mage_Core_Service_Parameter_Input $input
     * @return array
     */
    public function item(Mage_Core_Service_Parameter_Input $input)
    {
        $data = $this->_getData($input->getProductId());

        return $data;
    }

    /**
     * Returns info about several products.
     *
     * @Type call
     * @Method GET
     * @todo Not sure how to define @Path that might be given 1..Inf number of parameters
     * @Bindings [REST]
     * @Consumes input.xsd
     * @Produces output.xsd
     *
     * @param Mage_Core_Service_Parameter_Input $input
     * @return array
     */
    public function items(Mage_Core_Service_Parameter_Input $input)
    {
        $data = $this->_getCollectionData($input->getProductIds());

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

            // Calling to these methods set value to _data
            $product->getStatus();
            $product->getCategoryIds();
            $product->getWebsiteIds();
            $product->getStoreIds();
            $product->getRelatedProductIds();
            $product->getCrossSellProductIds();
            $product->getMediaAttributes();
            $product->getMediaGalleryImages();

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
                'price',
                'sku',
                'weight',
                'categoryId',
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
            );

            foreach ($propertiesWithoutMapping as $property) {
                $first3Letters = substr($property, 0, 3) === 'has';

                if ($first3Letters == 'can' || $first3Letters === 'has' || substr($property, 0, 3) === 'is') {
                    $method = $property;
                } else {
                    $method = 'get' . ucwords($property);
                }

                $this->_dictionary[$property] = $product->$method();
            }

            $this->_dictionary += array(
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

    protected function _getObjectData(Varien_Object $object)
    {
        $data = parent::_getObjectData($object);

//        $return = array(
//            'name' => $data['name'],
//            'sku' => $data['sku'],
//            'description' => $data['description'],
//            'shortDescription' => $data['short_description'],
//            'price' => array(
//                'amount' => '', // @todo
//                'currencyCode' => '', // @todo
//                'formattedPrice' => '', // @todo
//            ),
//            'special_price' => array(
//                'special_price' => '', // @todo
//                'special_from_date' => '', // @todo
//                'special_to_date' => '', // @todo
//            ),
//            'cost' => array(
//                'amount' => '', // @todo
//                'currencyCode' => '', // @todo
//                'formattedPrice' => '', // @todo
//            ),
//            ...
//        );

        return $data;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    protected function _getServiceId()
    {
        return 'catalog_product';
    }








    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Return resource object or resource object data.
     *
     * @Type call
     * @Method GET
     * @Path /{skuOrId}
     * @Bindings [REST]
     * @Consumes /resources/product/item/input.xsd
     * @Produces /resources/product/item/output.xsd
     *
     * @param mixed $args
     * @return array
     */
    public function getItem(Mage_Core_Service_Args $args = null, $asObject = true)
    {
        $result = $this->_getItem($args);
        if (!$asObject) {
            $fieldset = $args->getFieldset();
            $fields = $this->_getFields($fieldset);

            $result = $this->_getItemData($result, $fields);
        }

        return $result;
    }

    /**
     * Returns model which operated by current service.
     *
     * @param Mage_Core_Service_Args $args
     * @return Mage_Catalog_Model_Product
     */
    protected function _getItem(Mage_Core_Service_Args $args)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->_objectManager->create('Mage_Catalog_Model_Product');

        $id = $args->getId();
        // TODO: try as `sku` first (can be voided if we won't be supporting numeric SKUs)
        $productId = $product->getIdBySku($id);
        if (false === $productId) {
            if (is_numeric($id)) {
                $productId = $id;
            }
        }

        // `set` methods are creating troubles
        foreach ($args->getData() as $k => $v) {
            $product->setDataUsingMethod($k, $v);
        }

        if (false !== $productId) {
            // TODO: Depends on MDS-167
            //$fieldset = $args->getFieldset();
            //$product->setFieldset($fieldset);

            $product->load($productId);
        }

        if (!$product->getId()) {
            // TODO: so what to do?
            //assumption:
            $product->unsetData();
        } elseif (!$product->isVisibleInCatalog() || !$product->isVisibleInSiteVisibility()) {
            // TODO: so what to do?
            //assumption:
            $product->unsetData();
        } elseif (!in_array(Mage::app()->getStore()->getWebsiteId(), $product->getWebsiteIds())) {
            // TODO: so what to do?
            //assumption:
            $product->unsetData();
        }

        return $product;
    }

    /**
     * Returns info about several products.
     *
     * @Type call
     * @Method GET
     * @Path /[:field/:value]
     * @Bindings [REST]
     * @Consumes input.xsd
     * @Produces output.xsd
     *
     * @param Mage_Core_Service_Parameter_Input $input
     * @return array
     */
    public function getItems($args = null, $asObject = true)
    {
        $collection = $this->_getItems($args);
        if ($asObject) {
            return $collection;
        }

        $fieldset = $args->getFieldset();
        $fields = $this->_getFields($fieldset);

        $dataCollection = array();
        foreach ($collection as $item) {
            /** @var $item Varien_Object */
            $dataCollection[] = $this->_getItemData($item, $fields);
        }
        return $dataCollection;
    }

    /**
     * Get collection object of the current service
     *
     * @param Mage_Core_Service_Args $args
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _getItems(Mage_Core_Service_Args $args)
    {
        $collection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Product_Collection');

        $fieldset = $args->getFieldset();
        $fields = $this->_getFields($fieldset);
        if (!empty($fields)) {
            $collection->addAttributeToSelect($fields);
        }

        $productIds = $args->getProductIds();
        $collection->addIdFilter($productIds);

        $filters = $args->getFilters();
        $collection->addAttributeToFilter($filters);

        // TODO or not TODO
        //$collection->load();

        return $collection;
    }

    protected function _getFields($fieldset)
    {
        $fields = Mage::getConfig()->getNode('global/fieldset/' . $fieldset)->asArray();
        return array_keys($fields);
    }

    /**
     * Extract data from the loaded object with service data added.
     *
     * @param Varien_Object $object
     * @return array
     */
    protected function _getItemData(Varien_Object $item, array $fields = array())
    {
        $data = $item->toArray($fields);

        $formattedData = $this->_formatObjectData($data);

        return $formattedData;
    }
}
