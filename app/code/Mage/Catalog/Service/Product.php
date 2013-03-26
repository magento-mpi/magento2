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
     * @return Mage_Catalog_Service_Parameter_ProductData
     */
    public function item($id)
    {
        $data = $this->_getData($id, $this->getMethodId('item'));

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
     * @return Mage_Catalog_Service_Parameter_ProductData[]
     */
    public function items()
    {
        $data = $this->_getCollectionData(array(), $this->getMethodId('items'));

        return $data;
    }

    /**
     * Returns model which operated by current service.
     *
     * @param mixed  $id         Product ID or SKU
     * @param string $fieldsetId
     * @throws Mage_Core_Service_Entity_Exception
     * @return Mage_Catalog_Model_Product
     */
    protected function _getObject($id, $fieldsetId = '')
    {
        /** @var $productHelper Mage_Catalog_Helper_Product */
        /** @var $product Mage_Catalog_Model_Product */
        $productHelper = Mage::helper('Mage_Catalog_Helper_Product');
        return $productHelper->getProduct($id, Mage::app()->getCurrentStore());

        // TODO: try as `sku` first (can be voided if we won't be supporting numeric SKUs)
        $productId = $product->getIdBySku($id);

        if (false === $productId) {
            if (is_numeric($id)) {
                $productId = $id;
            }
        }

        if (empty($productId)) {
            throw new Mage_Core_Service_Entity_Exception;
        }

        // $product->setFieldset($this->_getFieldset($fieldsetId));
        $product->load($productId);

        if ($product->getId()) {
            $isVisible = $product->isVisibleInCatalog() && $product->isVisibleInSiteVisibility();
            /** @var $storeManager Mage_Core_Model_StoreManager */
            $storeManager = $this->_objectManager->create('Mage_Core_Model_StoreManager');
            $withinWebsite = in_array($storeManager->getStore()->getWebsiteId(), $product->getWebsiteIds());

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
        $collection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Product_Collection');
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
     * {@inheritdoc}
     *
     * @param array         $data
     * @param Varien_Object $object
     * @param string        $methodId
     * @return array
     */
    protected function _applySchema(array $data, Varien_Object $object, $methodId)
    {
        /**
         * This is a placeholder method until we develop a tool that do this automatically.
         */

        if (!$methodId === $this->getMethodId('item')) {
            return array();
        }

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

        $labelTypes = array(
            'status',
            'visibility',
            'msrpEnabled',
            'msrpDisplayActualPriceType',
            'taxClassId',
            'isReturnable'
        );

        $priceTypes = array('price', 'cost', 'groupPrice', 'tierPrice', 'minimalPrice', 'msrp', 'giftWrappingPrice');

        foreach ($schema as $field) {
            if (in_array($field, $priceTypes)) {
                $data[$field] = array(
                    'amount' => '',
                    'currencyCode' => '',
                    'formattedPrice' => '',
                );
            } else if ($field === 'specialPrice') {
                $data[$field] = array(
                    'specialPrice' => '',
                    'specialFromDate' => '',
                    'specialToDate' => '',
                );
            } else if ($field === 'images') {
                $data[$field] = array(
                    'image' => array(
                        'url' => '',
                        'label' => '',
                    ),
                    'smallImage' => array(
                        'url' => '',
                        'label' => '',
                    ),
                    'thumbnail' => array(
                        'url' => '',
                        'label' => '',
                    ),
                );
            } else if ($field === 'mediaGallery') {
                $data[$field] = array(
                    'images' => array(
                        array(
                            'image' => array(
                                'valueId' => '',
                                'file' => '',
                                'label' => '',
                                'position' => '',
                                'disabled' => '',
                                'positionDefault' => '',
                                'disabledDefault' => '',
                            )
                        )
                    )
                );
            } else if (in_array($field, $labelTypes)) {
                $data[$field] = array(
                    'value' => '',
                    'label' => '',
                );
            } else if (in_array($field, array('pageLayout', 'categoryIds'))) {
                $data[$field] = array(
                    array(
                        'valueLabel' => array(
                            'value' => '',
                            'label' => '',
                        )
                    )
                );
            } else if ($field === 'quantityAndStockStatus') {
                $data[$field] = array(
                    'isInStock' => '',
                    'qty' => '',
                );
            } else if ($field === 'bundle') {
                $data[$field] = array(
                    'priceType' => '',
                    'skuType' => '',
                    'weightType' => '',
                    'priceView' => array(
                        'value' => '',
                        'label' => '',
                    ),
                    'shipmentType' => '',
                );
            } else if ($field === 'downloadable') {
                $data[$field] = array(
                    'linksPurchasedSeparately' => '',
                    'samplesTitle' => '',
                    'linksTitle' => '',
                    'shipmentType' => '',
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
                    'allowOpenAmount' => '',
                    'openAmountMin' => array(
                        'amount' => '',
                        'currencyCode' => '',
                        'formattedPrice' => '',
                    ),
                    'openAmountMax' => array(
                        'amount' => '',
                        'currencyCode' => '',
                        'formattedPrice' => '',
                    ),
                    'giftcardType' => array(
                        'value' => '',
                        'label' => '',
                    ),
                    'isRedeemable' => '',
                    'useConfigIsRedeemable' => '',
                    'lifetime' => '',
                    'useConfigLifetime' => '',
                    'emailTemplate' => '',
                    'useConfigEmailTemplate' => '',
                    'allowMessage' => '',
                    'useConfigAllowMessage' => '',
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
            } else {
                $method = 'get' . ucwords($field);
                $data[$field] = $product->$method();
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    protected function _getServiceId()
    {
        return 'product';
    }
}
