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
 * @Service product
 * @Version 1.0
 * @Path /products
 */
class Mage_Catalog_Service_Product extends Mage_Core_Service_Entity_Abstract
{
    /** @var Mage_Catalog_Helper_Product */
    protected $_productHelper;
    /** @var Mage_Core_Helper_Data */
    protected $_coreHelper;
    /** @var Mage_Core_Model_StoreManager */
    protected $_storeManager;
    /** @var Magento_ObjectManager */
    protected $_objectManager;

    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_Catalog_Helper_Product $productHelper,
        Mage_Core_Helper_Data $coreHelper,
        Mage_Core_Model_StoreManager $storeManager
    ) {
        $this->_objectManager = $objectManager;
        $this->_productHelper = $productHelper;
        $this->_coreHelper = $coreHelper;
        $this->_storeManager = $storeManager;
    }

    /**
     * Returns info about one particular product.
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
        $data = $this->_getData($id);

        return $data;
    }

    /**
     * Returns info about several products.
     *
     * @Type call
     * @Method GET
     * @todo Not sure how to define Path that might be given 1..Inf number of parameters
     * @Bindings [REST]
     * @Consumes input.xsd
     * @Produces output.xsd
     *
     * @return array
     */
    public function items()
    {
        // @todo
        return array();
        $data = $this->_getCollectionData(array());

        return $data;
    }

    /**
     * Returns model which operated by current service.
     *
     * @param mixed  $productIdOrSku         Product ID or SKU
     * @param string $fieldsetId
     * @throws Mage_Core_Service_Entity_Exception
     * @return Mage_Catalog_Model_Product
     */
    protected function _getObject($productIdOrSku, $fieldsetId = '')
    {
        $product = $this->_productHelper->getProduct($productIdOrSku, null);

        if (!$product->getId()) {
            throw new Mage_Core_Service_Entity_Exception;
        }

        // $product->setFieldset($this->_getFieldset($fieldsetId));
        $product->load($product->getId());

        if ($product->getId()) {
            $isVisible = $product->isVisibleInCatalog() && $product->isVisibleInSiteVisibility();
            $withinWebsite = in_array($this->_storeManager->getStore()->getWebsiteId(), $product->getWebsiteIds());

            if (!$isVisible || !$withinWebsite) {
                throw new Mage_Core_Service_Entity_Exception;
            }
        } else {
            throw new Mage_Core_Service_Entity_Exception;
        }

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
            'quantityAndStockStatus',
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
        );

        $priceTypes = array('price', 'cost', 'groupPrice', 'tierPrice', 'minimalPrice', 'msrp', 'giftWrappingPrice');

        $currCode = $this->_storeManager->getStore()->getCurrentCurrencyCode();

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
                    case 'tierPrice':
                        $price = $product->getTierPrice();
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
                    $data[$field] = array(
                        'amount' => $price,
                        'currencyCode' => $currCode,
                        'formattedPrice' => $this->_coreHelper->currency($price, true, false),
                    );
                }
            } else if ($field === 'specialPrice' && $product->getSpecialPrice()) {
                $data[$field] = array(
                    'specialPrice' => $product->getSpecialPrice(),
                    'specialFromDate' => $product->getSpecialFromDate(),
                    'specialToDate' => $product->getSpecialToDate(),
                );
            } else if ($field === 'images') {
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
            } else if ($field === 'mediaGallery') {
                $images = array();

                foreach ($product->getMediaGalleryImages() as $image) {
                    $images[] = array(
                        'image' => array(
                            'valueId' => (int)$image->getValueId(),
                            'file' => $image->getFile(),
                            'label' => $image->getLabel(),
                            'position' => (int)$image->getPosition(),
                            'isDisabled' => (boolean)$image->getDisabled(),
                            'labelDefault' => $images->getLabelDefault(),
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
            } else if ($field === 'quantityAndStockStatus') {
                $quantityAndStockStatus = $product->getQuantityAndStockStatus();
                if (!is_null($quantityAndStockStatus['is_in_stock']) || !is_null($quantityAndStockStatus['qty'])) {
                    $data[$field] = array(
                        'isInStock' => $quantityAndStockStatus['is_in_stock'],
                        'qty' => $quantityAndStockStatus['qty']
                    );
                }
            } else if ($field === 'bundle' && $product->getTypeId() === Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                $data[$field] = array(
                    'priceType' => $product->getPriceType(),
                    'skuType' => $product->getSkuType(),
                    'weightType' => $product->getWeightType(),
                    'priceView' => $product->getPriceView(),
                    'shipmentType' => $product->getShipmentType(),
                );
            } else if (
                $field === 'downloadable'
                && $product->getTypeId() === Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE
            ) {
                $data[$field] = array(
                    'linksPurchasedSeparately' => $product->getLinksPurchasedSeparately(),
                    'samplesTitle' => $product->getSamplesTitle(),
                    'linksTitle' => $product->getLinksTitle(),
                    'shipmentType' => $product->getShipmentType(),
                );
            } else if ($field === 'giftcard') {
                $data[$field] = array(
                    'giftcardAmounts' => array(
                        array(
                            'amount' => array(
                                'amount' => '',
                                'currencyCode' => '',
                                'formattedPrice' => '',
                            )
                        )
                    ),
                    'allowOpenAmount' => $product->getAllowOpenAmount(),
                    'openAmountMin' => array(
                        'amount' => $product->getOpenAmountMin(),
                        'currencyCode' => $currCode,
                        'formattedPrice' => $this->_coreHelper->currency($product->getOpenAmountMin(), true, false),
                    ),
                    'openAmountMax' => array(
                        'amount' => $product->getOpenAmountMax(),
                        'currencyCode' => $currCode,
                        'formattedPrice' => $this->_coreHelper->currency($product->getOpenAmountMax(), true, false),
                    ),
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
            } else if ($field === 'targetRules') {
                $data[$field] = array(
                    'related' => array(
                        'positionLimit' => '',
                        'positionBehavior' => '',
                    ),
                    'upsell' => array(
                        'positionLimit' => '',
                        'positionBehavior' => '',
                    )
                );
            } else if (empty($data[$field])) {
                $method = 'get' . ucwords($field);
                $value = $product->$method();

                if (!is_null($value) || in_array($field, $mandatoryFields)) {
                    $data[$field] = $value;
                }
            }
        }

        return $data;
    }
}
