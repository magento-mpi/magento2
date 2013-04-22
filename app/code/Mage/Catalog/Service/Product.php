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
 * @Version 1.0
 * @Path /products
 */
class Mage_Catalog_Service_Product extends Mage_Core_Service_Type_DefaultEntity
{
    /**#@+
     * Identifier types for _getObject()
     */
    const IDENTIFIER_TYPE_ID = 'id';
    const IDENTIFIER_TYPE_SKU = 'sku';
    /**#@-*/

    /** @var Mage_Core_Helper_Data */
    protected $_coreHelper;
    /** @var Mage_Core_Model_StoreManager */
    protected $_storeManager;
    /** @var Magento_ObjectManager */
    protected $_objectManager;

    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_Core_Helper_Data $coreHelper,
        Mage_Core_Model_StoreManager $storeManager
    ) {
        $this->_objectManager = $objectManager;
        $this->_coreHelper = $coreHelper;
        $this->_storeManager = $storeManager;
    }

    /**
     * Return info about one particular product.
     *
     * @Type call
     * @Method GET
     * @Path /:id
     * @Bindings [REST]
     * @Consumes /resources/product/item/input.xsd
     * @Produces /resources/product/item/output.xsd
     * @param int $id
     * @return array
     */
    public function item($id)
    {
        $data = $this->_getData(array('id' => $id, 'identifierType' => static::IDENTIFIER_TYPE_ID));

        return $data;
    }

    /**
     * Return info about one particular product.
     *
     * @Type call
     * @Method GET
     * @Path /sku/:id
     * @Bindings [REST]
     * @Consumes /resources/product/item_by_sku/input.xsd
     * @Produces /resources/product/item_by_sku/output.xsd
     * @param string $sku
     * @return array
     */
    public function itemBySku($sku)
    {
        $data = $this->_getData(array('id' => $sku, 'identifierType' => static::IDENTIFIER_TYPE_SKU));

        return $data;
    }

    /**
     * Return info about several products.
     *
     * @Type call
     * @Method GET
     * @todo Not sure how to define Path that might be given 1..Inf number of parameters
     * @Bindings [REST]
     * @Consumes input.xsd
     * @Produces output.xsd
     *
     * @param array $ids Product IDs
     * @return array
     */
    public function items(array $ids)
    {
        $data = $this->_getCollectionData($ids);

        return $data;
    }

    /**
     * Return product by ID or SKU.
     *
     * @param int|string $idOrSku        Product ID or SKU (depends on $identifierType)
     * @param string     $identifierType See IDENTIFIER_TYPE_* constants
     * @return Mage_Catalog_Model_Product
     * @throws Mage_Core_Service_Entity_Exception
     */
    public function getProduct($idOrSku, $identifierType = self::IDENTIFIER_TYPE_ID)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->_objectManager->create('Mage_Catalog_Model_Product');
        $product->setStoreId($this->_storeManager->getStore()->getId());

        if (!in_array($identifierType, array(static::IDENTIFIER_TYPE_SKU, static::IDENTIFIER_TYPE_ID))) {
            throw new Mage_Core_Service_Entity_Exception(sprintf('Incorrect identifier type: "%s"', $identifierType));
        }

        if ($identifierType === static::IDENTIFIER_TYPE_SKU) {
            $id = $product->getIdBySku($idOrSku);
        } else {
            $id = $idOrSku;
        }

        if ($id) {
            // $product->setFieldset($this->_getFieldset($fieldsetId));
            $product->load($id);
        }

        if ($product->getId()) {
            $isVisible = $product->isVisibleInCatalog() && $product->isVisibleInSiteVisibility();
            $withinWebsite = in_array($this->_storeManager->getStore()->getWebsiteId(), $product->getWebsiteIds());
        }

        if (empty($isVisible) || empty($withinWebsite)) {
            throw new Mage_Core_Service_Entity_Exception(sprintf(
                'Product with %s "%s" not found',
                $identifierType === static::IDENTIFIER_TYPE_ID ? 'ID' : 'SKU',
                $idOrSku
            ));
        }

        return $product;
    }

    /**
     * Return model which operated by current service.
     *
     * @param array  $params     Parameters of the product in format array('id' => ?, 'identifierType' => ?)
     * @param string $fieldsetId
     * @throws Mage_Core_Service_Entity_Exception
     * @return Mage_Catalog_Model_Product
     */
    protected function _getObject($params, $fieldsetId = '')
    {
        $product = $this->getProduct($params['id'], $params['identifierType']);

        return $product;
    }

    /**
     * Get collection object of the current service
     *
     * @param array  $productIds
     * @param string $fieldsetId
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _getObjectCollection(array $productIds = array(), $fieldsetId = '')
    {
        $collection = $this->_objectManager->create('Mage_Catalog_Model_Resource_Product_Collection');
        // @todo what about setFieldset() for collection?
        // $collection->setFieldset($this->_getFieldset($fieldsetId));
        if (!empty($productIds)) {
            $collection->addIdFilter($productIds);
        }
        $fields = $this->_getFieldset($fieldsetId);

        if (!empty($fields)) {
            $collection->addAttributeToSelect($fields);
        }

        return $collection;
    }

    /**
     * Placeholder method which does mapper's job for now.
     * @todo remove
     *
     * @param array $data
     * @param Varien_Object $object
     * @return array
     */
    protected function _applySchema(array $data, Varien_Object $object)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $object;

        // For now it is done manually, later it is going to be implemented via XSD
        $schema = array(
            'name',
            'sku',
            'description',
            'shortDescription',
            'price',
            'specialPrice',
            'cost',
            'weight',
            'manufacturer',
            'metaTitle',
            'metaKeyword',
            'metaDescription',
            'images',
            'mediaGallery',
            'oldId',
            'groupPrice',
            'tierPrice',
            'color',
            'newsFromDate',
            'newsToDate',
            'gallery',
            'status',
            'urlKey',
            'urlPath',
            'minimalPrice',
            'isRecurring',
            'recurringProfile',
            'visibility',
            'customDesign',
            'customDesignFrom',
            'customDesignTo',
            'customLayoutUpdate',
            'pageLayout',
            'categoryIds',
            'optionsContainer',
            'requiredOptions',
            'hasOptions',
            'createdAt',
            'updatedAt',
            'countryOfManufacture',
            'msrpEnabled',
            'msrpDisplayActualPriceType',
            'msrp',
            'bundle',
            'downloadable',
            'giftMessageAvailable',
            'taxClassId',
            'enableGoogleCheckout',
            'giftcard',
            'giftWrappingAvailable',
            'giftWrappingPrice',
            'isReturnable',
            'targetRules',
            'isInStock',
            'qty',
            'websiteIds',
        );

        $mandatoryFields = array(
            'name',
            'sku',
            'description',
            'shortDescription',
            'price',
            'weight',
            'status',
            'visibility',
            'createdAt',
            'updatedAt',
            'taxClassId',
            'isInStock',
        );

        $priceTypes = array('price', 'cost', 'groupPrice', 'minimalPrice', 'msrp', 'giftWrappingPrice');
        $appBaseCurrencyCode = Mage::app()->getBaseCurrencyCode();
        $currentStoreCurrencyCode = $this->_storeManager->getStore()->getCurrentCurrencyCode();

        /**
         * @todo Fetch information for all websites.
         * By default information for some attributes (i.e. tierPrice, giftcard amounts) fetched for current store only.
         * We need to emulate admin environment to get comprehensive data.
         */

        foreach ($schema as $field) {
            if (in_array($field, $priceTypes)) {
                switch ($field) {
                    case 'price':
                        $price = $product->getPrice();
                        break;
                    case 'cost':
                        $price = $product->getCost();
                        break;
                    case 'groupPrice':
                        $price = $product->getGroupPrice();
                        break;
                    case 'minimalPrice':
                        $price = $product->getMinimalPrice();
                        break;
                    case 'msrp':
                        $price = $product->getMsrp();
                        break;
                    case 'giftWrappingPrice':
                        $price = $product->getGiftWrappingPrice();
                        break;
                }

                if (!is_null($price) || $field === 'price') {
                    $data[$field] = $this->_getPrice($price, $currentStoreCurrencyCode);
                }
            } elseif ($field === 'specialPrice' && $product->getSpecialPrice()) {
                $data[$field] = array(
                    'specialPrice' => $product->getSpecialPrice(),
                    'specialFromDate' => $product->getSpecialFromDate(),
                    'specialToDate' => $product->getSpecialToDate(),
                );
            } elseif ($field === 'tierPrice' && $product->getTierPriceCount()) {
                $tierPrices = array();

                foreach ($product->getTierPrice() as $tierPrice) {
                    $tierPrices[] = array(
                        'tierPrice' => array(
                            'priceId' => $tierPrice['price_id'],
                            'websiteId' => $tierPrice['website_id'],
                            'allGroups' => $tierPrice['all_groups'],
                            'customerGroup' => $tierPrice['cust_group'],
                            'price' => $this->_getPrice($tierPrice['price'], $appBaseCurrencyCode),
                            'priceQty' => $tierPrice['price_qty'],
                            'websitePrice' => $this->_getPrice($tierPrice['website_price'], $currentStoreCurrencyCode),
                        )
                    );
                }

                $data[$field] = $tierPrices;
            } elseif ($field === 'images') {
                $images = array();
                $image = $product->getImage();
                $smallImage = $product->getSmallImage();
                $thumbnail = $product->getThumbnail();

                if ($image) {
                    $images['image'] = $image;
                }

                if ($smallImage) {
                    $images['smallImage'] = $smallImage;
                }

                if ($thumbnail) {
                    $images['thumbnail'] = $thumbnail;
                }

                if (!empty($images)) {
                    $data[$field] = $images;
                }
            } elseif ($field === 'mediaGallery') {
                $images = array();

                foreach ($product->getMediaGalleryImages() as $image) {
                    $images[] = array(
                        'image' => array(
                            'valueId' => (int)$image->getValueId(),
                            'file' => $image->getFile(),
                            'label' => $image->getLabel(),
                            'position' => (int)$image->getPosition(),
                            'isDisabled' => (boolean)$image->getDisabled(),
                            'labelDefault' => $image->getLabelDefault(),
                            'positionDefault' => (int)$image->getPositionDefault(),
                            'isDisabledDefault' => (boolean)$image->getDisabledDefault(),
                            'url' => $image->getUrl(),
                            'id' => $image->getId(),
                            'path' => $image->getPath(),
                        )
                    );
                }

                if (!empty($images)) {
                    $data[$field] = array(
                        'images' => $images
                    );
                }
            } elseif ($field === 'bundle' && $product->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                $data[$field] = array(
                    'priceType' => $product->getPriceType(),
                    'skuType' => $product->getSkuType(),
                    'weightType' => $product->getWeightType(),
                    'priceView' => $product->getPriceView(),
                    'shipmentType' => $product->getShipmentType(),
                );
            } elseif (
                $field === 'downloadable'
                && $product->getTypeId() === Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
            ) {
                $data[$field] = array(
                    'linksPurchasedSeparately' => $product->getLinksPurchasedSeparately(),
                    'samplesTitle' => $product->getSamplesTitle(),
                    'linksTitle' => $product->getLinksTitle(),
                    'shipmentType' => $product->getShipmentType(),
                );
            } elseif (
                $field === 'giftcard'
                && $product->getTypeId() === Enterprise_GiftCard_Model_Catalog_Product_Type_Giftcard::TYPE_GIFTCARD
            ) {
                $amounts = array();

                foreach ($product->getGiftcardAmounts() as $amount) {
                    $amounts[] = array(
                        'amount' => $this->_getPrice($amount['website_value'], $currentStoreCurrencyCode)
                    );
                }

                $data[$field] = array(
                    'giftcardAmounts' => $amounts,
                    'allowOpenAmount' => $product->getAllowOpenAmount(),
                    'openAmountMin' => $this->_getPrice($product->getOpenAmountMin(), $currentStoreCurrencyCode),
                    'openAmountMax' => $this->_getPrice($product->getOpenAmountMax(), $currentStoreCurrencyCode),
                    'giftcardType' => $product->getGiftCardType(),
                    'isRedeemable' => $product->getIsRedeemable(),
                    'useConfigIsRedeemable' => $product->getUseConfigIsRedeemable(),
                    'lifetime' => $product->getLifetime(),
                    'useConfigLifetime' => $product->getUseConfigLifetime(),
                    'emailTemplate' => $product->getEmailTemplate(),
                    'useConfigEmailTemplate' => $product->getUseConfigEmailTemplate(),
                    'allowMessage' => $product->getAllowMessage(),
                    'useConfigAllowMessage' => $product->getUseConfigAllowMessage(),
                );
            } elseif ($field === 'websiteIds') {
                $websiteIds = array();

                foreach ($product->getWebsiteIds() as $websiteId) {
                    $websiteIds[] = array('id' => $websiteId);
                }

                $data[$field] = $websiteIds;
            } elseif ($field === 'qty') {
                $quantityAndStockStatus = $product->getQuantityAndStockStatus();
                $data[$field] = $quantityAndStockStatus['qty'];
            } elseif (empty($data[$field])) {
                $method = 'get' . ucwords($field);
                $value = $product->$method();

                if (!is_null($value) || in_array($field, $mandatoryFields)) {
                    $data[$field] = $value;
                }
            }
        }

        return $data;
    }

    /**
     * Return array which represents XSD "price" complex type.
     *
     * @param $amount
     * @param $currencyCode
     * @return array
     */
    private function _getPrice($amount, $currencyCode)
    {
        $price = array(
            'amount' => $amount,
            'currencyCode' => $currencyCode,
            'formattedPrice' => $this->_coreHelper->currency($amount, true, false),
        );

        return $price;
    }
}
