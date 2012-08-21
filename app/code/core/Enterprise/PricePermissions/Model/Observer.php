<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PricePermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Price Permissions Observer
 *
 * @category    Enterprise
 * @package     Enterprise_PricePermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_PricePermissions_Model_Observer
{
    /**
     * Instance of http request
     *
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * Edit Product Price flag
     *
     * @var boolean
     */
    protected $_canEditProductPrice;

    /**
     * Read Product Price flag
     *
     * @var boolean
     */
    protected $_canReadProductPrice;

    /**
     * Edit Product Status flag
     *
     * @var boolean
     */
    protected $_canEditProductStatus;

    /**
     * String representation of the default product price
     *
     * @var string
     */
    protected $_defaultProductPriceString;

    /**
     * Price Permissions Observer class constructor
     *
     * Sets necessary data
     */
    public function __construct(array $data = array())
    {
        $this->_request = (isset($data['request']) && false === $data['request']) ? false : Mage::app()->getRequest();
        if (isset($data['can_edit_product_price']) && false === $data['can_edit_product_price']) {
            $this->_canEditProductPrice = false;
        }
        if (isset($data['can_read_product_price']) && false === $data['can_read_product_price']) {
            $this->_canReadProductPrice = false;
        }
        if (isset($data['can_edit_product_status']) && false === $data['can_edit_product_status']) {
            $this->_canEditProductStatus = false;
        }
        if (isset($data['default_product_price_string'])) {
            $this->_defaultProductPriceString = $data['default_product_price_string'];
        }
    }

    /**
     * Reinit stores only with allowed scopes
     *
     * @param Varien_Event_Observer $observer
     */
    public function adminControllerPredispatch($observer)
    {
        /* @var $session Mage_Backend_Model_Auth_Session */
        $session = Mage::getSingleton('Mage_Backend_Model_Auth_Session');

        // load role with true websites and store groups
        if ($session->isLoggedIn() && $session->getUser()->getRole()) {
            // Set all necessary flags
            $helper = Mage::helper('Enterprise_PricePermissions_Helper_Data');
            $this->_canEditProductPrice = $helper->getCanAdminEditProductPrice();
            $this->_canReadProductPrice = $helper->getCanAdminReadProductPrice();
            $this->_canEditProductStatus = $helper->getCanAdminEditProductStatus();
            // Retrieve value of the default product price
            $this->_defaultProductPriceString = $helper->getDefaultProductPriceString();
        }
    }

    /**
     * Handle core_block_abstract_to_html_before event
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function coreBlockAbstractToHtmlBefore($observer)
    {
         /** @var $block Mage_Core_Block_Abstract */
        $block = $observer->getBlock();
        $blockNameInLayout = $block->getNameInLayout();
        switch ($blockNameInLayout) {
            // Handle product Recurring Profile tab
            case 'adminhtml_recurring_profile_edit_form' :
                if (!Mage::registry('product')->isObjectNew()) {
                    if (!$this->_canReadProductPrice) {
                        $block->setProductEntity(Mage::getModel('Mage_Catalog_Model_Product'));
                    }
                }
                if (!$this->_canEditProductPrice) {
                    $block->setIsReadonly(true);
                }
                break;
            case 'adminhtml_recurring_profile_edit_form_dependence' :
                if (!$this->_canEditProductPrice) {
                    $block->addConfigOptions(array('can_edit_price' => false));
                    if (!$this->_canReadProductPrice) {
                        $dependenceValue = (Mage::registry('product')->getIsRecurring()) ? '0' : '1';
                        // Override previous dependence value
                        $block->addFieldDependence('product[recurring_profile]', 'product[is_recurring]',
                            $dependenceValue);
                    }
                }
                break;
            // Handle MAP functionality for bundle products
            case 'adminhtml.catalog.product.edit.tab.attributes' :
                if (!$this->_canEditProductPrice) {
                    $block->setCanEditPrice(false);
                }
                break;
        }
    }

    /**
     * Handle catalog_product_load_after event
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function catalogProductLoadAfter(Varien_Event_Observer $observer)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getDataObject();

        if (!$this->_canEditProductPrice) {
            // Lock price attributes of product in order not to let administrator to change them
            $product->lockAttribute('price');
            $product->lockAttribute('special_price');
            $product->lockAttribute('tier_price');
            $product->lockAttribute('group_price');
            $product->lockAttribute('special_from_date');
            $product->lockAttribute('special_to_date');
            $product->lockAttribute('is_recurring');
            $product->lockAttribute('cost');
            // For bundle product
            $product->lockAttribute('price_type');
            // Gift Card attributes
            $product->lockAttribute('open_amount_max');
            $product->lockAttribute('open_amount_min');
            $product->lockAttribute('allow_open_amount');
            $product->lockAttribute('giftcard_amounts');
            // For MAP fields
            $product->lockAttribute('msrp_enabled');
            $product->lockAttribute('msrp_display_actual_price_type');
            $product->lockAttribute('msrp');
        }
        if (!$this->_canEditProductStatus) {
            $product->lockAttribute('status');
        }
    }

    /**
     * Handle catalog_product_save_before event
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function catalogProductSaveBefore(Varien_Event_Observer $observer)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getDataObject();
        if ($product->isObjectNew() && !$this->_canEditProductStatus) {
            $product->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
        }
    }

    /**
     * Handle adminhtml_catalog_product_edit_prepare_form event
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function adminhtmlCatalogProductEditPrepareForm(Varien_Event_Observer $observer)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::registry('product');
        if ($product->isObjectNew()) {
            $form = $observer->getEvent()->getForm();
            // Disable Status drop-down if needed
            if (!$this->_canEditProductStatus) {
                $statusElement = $form->getElement('status');
                if (!is_null($statusElement)) {
                    $statusElement->setValue(Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
                    $statusElement->setReadonly(true, true);
                }
            }
        }
    }

    /**
     * Handle catalog_product_before_save event
     *
     * Handle important product data before saving a product
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function catalogProductPrepareSave($observer)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $observer->getEvent()->getProduct();

        if (!$this->_canEditProductPrice) {
            // Handle Custom Options of Product
            $originalOptions = $product->getOptions();
            $options = $product->getData('product_options');
            if (is_array($options)) {

                $originalOptionsAssoc = array();
                if (is_array($originalOptions)) {

                    foreach ($originalOptions as $originalOption) {
                        /** @var $originalOption Mage_Catalog_Model_Product_Option */
                        $originalOptionAssoc = array();
                        $originalOptionAssoc['id'] = $originalOption->getOptionId();
                        $originalOptionAssoc['option_id'] = $originalOption->getOptionId();
                        $originalOptionAssoc['type'] = $originalOption->getType();
                        $originalOptionGroup = $originalOption->getGroupByType();
                        if ($originalOptionGroup != Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                            $originalOptionAssoc['price'] = $originalOption->getPrice();
                            $originalOptionAssoc['price_type'] = $originalOption->getPriceType();
                        } else {
                            $originalOptionAssoc['values'] = array();
                            foreach ($originalOption->getValues() as $value) {
                                /** @var $value Mage_Catalog_Model_Product_Option_Value */
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
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
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

            // Handle seletion data of bundle products
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
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
            if ($product->getTypeId() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {

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
                if (!($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE
                    && $product->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC)
                ) {
                    $product->setPrice((float) $this->_defaultProductPriceString);
                    // Set default amount for Gift Card product
                    if ($product->getTypeId() == Enterprise_GiftCard_Model_Catalog_Product_Type_Giftcard::TYPE_GIFTCARD
                    ) {
                        $storeId = (int) $this->_request->getParam('store', 0);
                        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
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
                    Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Enabled::MSRP_ENABLE_USE_CONFIG);
                $product->setMsrpDisplayActualPriceType(
                    Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Price::TYPE_USE_CONFIG);
            }
        }
    }

    /**
     * Handle adminhtml_catalog_product_form_prepare_excluded_field_list event
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function adminhtmlCatalogProductFormPrepareExcludedFieldList($observer)
    {
        /** @var $block Mage_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute_Tab_Attributes */
        $block = $observer->getEvent()->getObject();
        $excludedFieldList = array();

        if (!$this->_canEditProductPrice) {
            $excludedFieldList = array(
                'price', 'special_price', 'tier_price', 'group_price', 'special_from_date', 'special_to_date',
                'is_recurring', 'cost', 'price_type', 'open_amount_max', 'open_amount_min', 'allow_open_amount',
                'giftcard_amounts', 'msrp_enabled', 'msrp_display_actual_price_type', 'msrp'
            );
        }
        if (!$this->_canEditProductStatus) {
            $excludedFieldList[] = 'status';
        }

        $block->setFormExcludedFieldList(array_merge($block->getFormExcludedFieldList(), $excludedFieldList));
    }

    /**
     * Handle catalog_product_attribute_update_before event
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function catalogProductAttributeUpdateBefore($observer)
    {
        /** @var $block Mage_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute_Tab_Attributes */
        $attributesData = $observer->getEvent()->getAttributesData();
        $excludedAttributes = array();

        if (!$this->_canEditProductPrice) {
            $excludedAttributes = array(
                'price', 'special_price', 'tier_price', 'group_price', 'special_from_date', 'special_to_date',
                'is_recurring', 'cost', 'price_type', 'open_amount_max', 'open_amount_min', 'allow_open_amount',
                'giftcard_amounts', 'msrp_enabled', 'msrp_display_actual_price_type', 'msrp'
            );
        }
        if (!$this->_canEditProductStatus) {
            $excludedAttributes[] = 'status';
        }
        foreach ($excludedAttributes as $excludedAttributeCode) {
            if (isset($attributesData[$excludedAttributeCode])) {
                unset($attributesData[$excludedAttributeCode]);
            }
        }

        $observer->getEvent()->setAttributesData($attributesData);
    }

    /**
     * Hide price elements on Price Tab of Product Edit Page if needed
     *
     * @param Mage_Core_Block_Abstract $block
     * @return void
     */
    protected function _hidePriceElements($block)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::registry('product');
        $form = $block->getForm();
        $group = $block->getGroup();
        $fieldset = null;
        if (!is_null($form) && !is_null($group)) {
            $fieldset = $form->getElement('group_fields' . $group->getId());
        }

        if (!is_null($product) && !is_null($form) && !is_null($group) && !is_null($fieldset)) {
            $priceElementIds = array(
                'special_price',
                'tier_price',
                'group_price',
                'special_from_date',
                'special_to_date',
                'cost',
                // GiftCard attributes
                'open_amount_max',
                'open_amount_min',
                'allow_open_amount',
                'giftcard_amounts',
                // MAP attributes
                'msrp_enabled',
                'msrp_display_actual_price_type',
                'msrp'
            );

            // Leave price element for bundle product active in order to change/view price type when product is created
            if (Mage::registry('product')->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                array_push($priceElementIds, 'price');
            }

            // Remove price elements or disable them if needed
            foreach ($priceElementIds as &$priceId) {
                if (!$this->_canReadProductPrice) {
                    $fieldset->removeField($priceId);
                } elseif (!$this->_canEditProductPrice) {
                    $priceElement = $form->getElement($priceId);
                    if (!is_null($priceElement)) {
                        $priceElement->setReadonly(true, true);
                    }
                }
            }

            if (!$this->_canEditProductPrice) {
                // Handle Recurring Profile tab
                if ($form->getElement('recurring_profile')) {
                    $form->getElement('recurring_profile')->setReadonly(true, true)->getForm()
                        ->setReadonly(true, true);
                }
            }

            if ($product->isObjectNew()) {
                if (!$this->_canEditProductPrice) {
                    // For each type of products accept except Bundle products, set default value for price if allowed
                    $priceElement = $form->getElement('price');
                    if (!is_null($priceElement)
                        && $this->_canReadProductPrice
                        && (Mage::registry('product')->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_BUNDLE)
                    ) {
                        $priceElement->setValue($this->_defaultProductPriceString);
                    }
                    // For giftcard products set default amount
                    $amountsElement = $form->getElement('giftcard_amounts');
                    if (!is_null($amountsElement)) {
                        $storeId = (int) $this->_request->getParam('store', 0);
                        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
                        $amountsElement->setValue(array(
                            array(
                                'website_id'    => $websiteId,
                                'value'         => $this->_defaultProductPriceString,
                                'website_value' => (float) $this->_defaultProductPriceString
                            )
                        ));
                    }
                }
            }
        }
    }
}
