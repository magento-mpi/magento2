<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PricePermissions\Controller\Adminhtml\Product\Initialization\Helper\Plugin;

class PricePermissions 
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @var \Magento\PricePermissions\Helper\Data
     */
    protected $pricePermData;

    /**
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\PricePermissions\Helper\Data $pricePermData
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\PricePermissions\Helper\Data $pricePermData
    ) {
        $this->pricePermData = $pricePermData;
        $this->authSession = $authSession;
    }

    /**
     * Handle important product data before saving a product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product
     */
    public function afterInitialize(\Magento\Catalog\Model\Product $product)
    {
        $canEditProductPrice = false;
        if ($this->authSession->isLoggedIn() && $this->authSession->getUser()->getRole()) {
            $canEditProductPrice = $this->pricePermData->getCanAdminEditProductPrice();
        }

        if (false == $canEditProductPrice) {
            // Handle Custom Options of Product
            $originalOptions = $product->getOptions();
            $options = $product->getData('product_options');
            if (is_array($options)) {

                $originalOptionsAssoc = array();
                if (is_array($originalOptions)) {

                    foreach ($originalOptions as $originalOption) {
                        /** @var $originalOption \Magento\Catalog\Model\Product\Option */
                        $originalOptionAssoc = array();
                        $originalOptionAssoc['id'] = $originalOption->getOptionId();
                        $originalOptionAssoc['option_id'] = $originalOption->getOptionId();
                        $originalOptionAssoc['type'] = $originalOption->getType();
                        $originalOptionGroup = $originalOption->getGroupByType();
                        if ($originalOptionGroup != \Magento\Catalog\Model\Product\Option::OPTION_GROUP_SELECT) {
                            $originalOptionAssoc['price'] = $originalOption->getPrice();
                            $originalOptionAssoc['price_type'] = $originalOption->getPriceType();
                        } else {
                            $originalOptionAssoc['values'] = array();
                            foreach ($originalOption->getValues() as $value) {
                                /** @var $value \Magento\Catalog\Model\Product\Option\Value */
                                $originalOptionAssoc['values'][$value->getOptionTypeId()] = array(
                                    'price' => $value->getPrice(),
                                    'price_type' => $value->getPriceType()
                                );
                            }
                        }
                        $originalOptionsAssoc[$originalOption->getOptionId()] = $originalOptionAssoc;
                    }
                }

                foreach ($options as $optionId => &$option) {
                    // For old options
                    if (isset($originalOptionsAssoc[$optionId])
                        && $originalOptionsAssoc[$optionId]['type'] == $option['type']
                    ) {
                        if (!isset($option['values'])) {
                            $option['price'] = $originalOptionsAssoc[$optionId]['price'];
                            $option['price_type'] = $originalOptionsAssoc[$optionId]['price_type'];
                        } elseif (is_array($option['values'])) {
                            foreach ($option['values'] as &$value) {
                                if (isset($originalOptionsAssoc[$optionId]['values'][$value['option_type_id']])) {
                                    $originalValue =
                                        $originalOptionsAssoc[$optionId]['values'][$value['option_type_id']];
                                    $value['price'] = $originalValue['price'];
                                    $value['price_type'] = $originalValue['price_type'];
                                } else {
                                    // Set zero price for new selections of old custom option
                                    $value['price'] = '0';
                                    $value['price_type'] = 0;
                                }
                            }
                        }
                        // Set price to zero and price type to fixed for new options
                    } else {
                        if (!isset($option['values'])) {
                            $option['price'] = '0';
                            $option['price_type'] = 0;
                        } elseif (is_array($option['values'])) {
                            foreach ($option['values'] as &$value) {
                                $value['price'] = '0';
                                $value['price_type'] = 0;
                            }
                        }
                    }
                }
                $product->setData('product_options', $options);
            }

            // Handle recurring profile data (replace it with original)
            $originalRecurringProfile = $product->getOrigData('recurring_profile');
            $product->setRecurringProfile($originalRecurringProfile);

            // Handle data received from Associated Products tab of configurable product
            if ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE) {
                $originalAttributes = $product->getTypeInstance()
                    ->getConfigurableAttributesAsArray($product);
                // Organize main information about original product attributes in assoc array form
                $originalAttributesMainInfo = array();
                if (is_array($originalAttributes)) {
                    foreach ($originalAttributes as $originalAttribute) {
                        $originalAttributesMainInfo[$originalAttribute['id']] = array();
                        foreach ($originalAttribute['values'] as $value) {
                            $originalAttributesMainInfo[$originalAttribute['id']][$value['value_index']] = array(
                                'is_percent'    => $value['is_percent'],
                                'pricing_value' => $value['pricing_value']
                            );
                        }
                    }
                }
                $attributeData = $product->getConfigurableAttributesData();
                if (is_array($attributeData)) {
                    foreach ($attributeData as &$data) {
                        $id = $data['id'];
                        foreach ($data['values'] as &$value) {
                            $valueIndex = $value['value_index'];
                            if (isset($originalAttributesMainInfo[$id][$valueIndex])) {
                                $value['pricing_value'] =
                                    $originalAttributesMainInfo[$id][$valueIndex]['pricing_value'];
                                $value['is_percent'] = $originalAttributesMainInfo[$id][$valueIndex]['is_percent'];
                            } else {
                                $value['pricing_value'] = 0;
                                $value['is_percent'] = 0;
                            }
                        }
                    }
                    $product->setConfigurableAttributesData($attributeData);
                }
            }

            // Handle selection data of bundle products
            if ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                $bundleSelectionsData = $product->getBundleSelectionsData();
                if (is_array($bundleSelectionsData)) {
                    // Retrieve original selections data
                    $product->getTypeInstance()->setStoreFilter($product->getStoreId(), $product);

                    $optionCollection = $product->getTypeInstance()->getOptionsCollection($product);
                    $selectionCollection = $product->getTypeInstance()->getSelectionsCollection(
                        $product->getTypeInstance()->getOptionsIds($product),
                        $product
                    );

                    $origBundleOptions = $optionCollection->appendSelections($selectionCollection);
                    $origBundleOptionsAssoc = array();
                    foreach ($origBundleOptions as $origBundleOption) {
                        $optionId = $origBundleOption->getOptionId();
                        $origBundleOptionsAssoc[$optionId] = array();
                        if ($origBundleOption->getSelections()) {
                            foreach ($origBundleOption->getSelections() as $selection) {
                                $selectionProductId = $selection->getProductId();
                                $origBundleOptionsAssoc[$optionId][$selectionProductId] = array(
                                    'selection_price_type' => $selection->getSelectionPriceType(),
                                    'selection_price_value' => $selection->getSelectionPriceValue()
                                );
                            }
                        }
                    }
                    // Keep previous price and price type for selections
                    foreach ($bundleSelectionsData as &$bundleOptionSelections) {
                        foreach ($bundleOptionSelections as &$bundleOptionSelection) {
                            if (!isset($bundleOptionSelection['option_id'])
                                || !isset($bundleOptionSelection['product_id'])
                            ) {
                                continue;
                            }
                            $optionId = $bundleOptionSelection['option_id'];
                            $selectionProductId = $bundleOptionSelection['product_id'];
                            $isDeleted = $bundleOptionSelection['delete'];
                            if (isset($origBundleOptionsAssoc[$optionId][$selectionProductId]) && !$isDeleted) {
                                $bundleOptionSelection['selection_price_type'] =
                                    $origBundleOptionsAssoc[$optionId][$selectionProductId]['selection_price_type'];
                                $bundleOptionSelection['selection_price_value'] =
                                    $origBundleOptionsAssoc[$optionId][$selectionProductId]['selection_price_value'];
                            } else {
                                // Set zero price for new bundle selections and options
                                $bundleOptionSelection['selection_price_type'] = 0;
                                $bundleOptionSelection['selection_price_value'] = 0;
                            }
                        }
                    }
                    $product->setData('bundle_selections_data', $bundleSelectionsData);
                }
            }

            // Handle data received from Downloadable Links tab of downloadable products
            if ($product->getTypeId() == \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE) {

                $downloadableData = $product->getDownloadableData();
                if (is_array($downloadableData) && isset($downloadableData['link'])) {
                    $originalLinks = $product->getTypeInstance()->getLinks($product);
                    foreach ($downloadableData['link'] as $id => &$downloadableDataItem) {
                        $linkId = $downloadableDataItem['link_id'];
                        if (isset($originalLinks[$linkId]) && !$downloadableDataItem['is_delete']) {
                            $originalLink = $originalLinks[$linkId];
                            $downloadableDataItem['price'] = $originalLink->getPrice();
                        } else {
                            // Set zero price for new links
                            $downloadableDataItem['price'] = 0;
                        }
                    }
                    $product->setDownloadableData($downloadableData);
                }
            }

            if ($product->isObjectNew()) {
                // For new products set default price
                if (!($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE
                    && $product->getPriceType() == \Magento\Bundle\Model\Product\Price::PRICE_TYPE_DYNAMIC)
                ) {
                    $product->setPrice((float) $this->_defaultProductPriceString);
                    // Set default amount for Gift Card product
                    if ($product->getTypeId() == \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD
                    ) {
                        $storeId = (int) $this->_request->getParam('store', 0);
                        $websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId();
                        $product->setGiftcardAmounts(array(
                            array(
                                'website_id' => $websiteId,
                                'price'      => $this->_defaultProductPriceString,
                                'delete'     => ''
                            )
                        ));
                    }
                }
                // New products are created without recurring profiles
                $product->setIsRecurring(false);
                $product->unsRecurringProfile();
                // Add MAP default values
                $product->setMsrpEnabled(
                    \Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type\Enabled::MSRP_ENABLE_USE_CONFIG);
                $product->setMsrpDisplayActualPriceType(
                    \Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type\Price::TYPE_USE_CONFIG);
            }
        }
        return $product;
    }
} 
