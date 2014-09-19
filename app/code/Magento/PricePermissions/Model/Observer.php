<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PricePermissions\Model;

use Magento\Backend\Block\Template;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Backend\Block\Widget\Grid;

/**
 * Price Permissions Observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Observer
{
    /**
     * Instance of http request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * Edit Product Price flag
     *
     * @var bool
     */
    protected $_canEditProductPrice;

    /**
     * Read Product Price flag
     *
     * @var bool
     */
    protected $_canReadProductPrice;

    /**
     * Edit Product Status flag
     *
     * @var bool
     */
    protected $_canEditProductStatus;

    /**
     * String representation of the default product price
     *
     * @var string
     */
    protected $_defaultProductPriceString;

    /**
     * Function name and corresponding block names
     *
     * @var array
     */
    protected $_filterRules = array(
        '_removeStatusMassaction' => array('product.grid', 'admin.product.grid'),
        '_removeColumnPrice' => array(
            'catalog.product.edit.tab.related',
            'catalog.product.edit.tab.upsell',
            'catalog.product.edit.tab.crosssell',
            'category.product.grid',
            'products',
            'wishlist',
            'compared',
            'rcompared',
            'rviewed',
            'ordered',
            'checkout.accordion.products',
            'checkout.accordion.wishlist',
            'checkout.accordion.compared',
            'checkout.accordion.rcompared',
            'checkout.accordion.rviewed',
            'checkout.accordion.ordered',
            'adminhtml.catalog.product.edit.tab.bundle.option.search.grid',
            'admin.product.edit.tab.super.config.grid',
            'catalog.product.edit.tab.super.group',
            'product.grid',
            'admin.product.grid'
        ),
        '_removeColumnsPriceTotal' => array('admin.customer.view.cart'),
        '_setCanReadPriceFalse' => array('checkout.items', 'items'),
        '_setCanEditReadPriceFalse' => array(
            'catalog.product.edit.tab.downloadable.links',
            'adminhtml.catalog.product.bundle.edit.tab.attributes.price'
        ),
        '_setOptionsEditReadFalse' => array('admin.product.options'),
        '_setCanEditReadDefaultPrice' => array('adminhtml.catalog.product.bundle.edit.tab.attributes.price'),
        '_setCanEditReadChildBlock' => array('adminhtml.catalog.product.edit.tab.bundle.option'),
        '_hidePriceElements' => array('adminhtml.catalog.product.edit.tab.attributes')
    );

    /**
     * Price permissions data
     *
     * @var \Magento\PricePermissions\Helper\Data
     */
    protected $_pricePermData = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Backend authorization session
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * Catalog product model
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * Store list manager
     *
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\PricePermissions\Helper\Data $pricePermData
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        \Magento\PricePermissions\Helper\Data $pricePermData,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_pricePermData = $pricePermData;
        $this->_request = $request;
        $this->_authSession = $authSession;
        $this->_storeManager = $storeManager;
        $this->_productFactory = $productFactory;
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
     * @param EventObserver $observer
     * @return void
     */
    public function adminControllerPredispatch($observer)
    {
        // load role with true websites and store groups
        if ($this->_authSession->isLoggedIn() && $this->_authSession->getUser()->getRole()) {
            // Set all necessary flags
            /** @var $helper \Magento\PricePermissions\Helper\Data */
            $helper = $this->_pricePermData;
            $this->_canEditProductPrice = $helper->getCanAdminEditProductPrice();
            $this->_canReadProductPrice = $helper->getCanAdminReadProductPrice();
            $this->_canEditProductStatus = $helper->getCanAdminEditProductStatus();
            // Retrieve value of the default product price
            $this->_defaultProductPriceString = $helper->getDefaultProductPriceString();
        }
    }

    /**
     * Call needed function depending on block name
     *
     * @param Template $block
     * @return void
     */
    protected function _filterByBlockName($block)
    {
        $blockName = $block->getNameInLayout();
        foreach ($this->_filterRules as $function => $list) {
            if (in_array($blockName, $list)) {
                call_user_func(array($this, $function), $block);
            }
        }
    }

    /**
     * Remove status option in massaction
     *
     * @param Template $block
     * @return void
     */
    protected function _removeStatusMassaction($block)
    {
        if (!$this->_canEditProductStatus) {
            $block->getMassactionBlock()->removeItem('status');
        }
    }

    /**
     * Remove price column from grid
     *
     * @param Template $block
     * @return void
     */
    protected function _removeColumnPrice($block)
    {
        $this->_removeColumnsFromGrid($block, array('price'));
    }

    /**
     * Remove price and total columns from grid
     *
     * @param Template $block
     * @return void
     */
    protected function _removeColumnsPriceTotal($block)
    {
        $this->_removeColumnsFromGrid($block, array('price', 'total'));
    }

    /**
     * Set read price to false
     *
     * @param Template $block
     * @return void
     */
    protected function _setCanReadPriceFalse($block)
    {
        if (!$this->_canReadProductPrice) {
            $block->setCanReadPrice(false);
        }
    }

    /**
     * Set read and edit price to false
     *
     * @param Template $block
     * @return void
     */
    protected function _setCanEditReadPriceFalse($block)
    {
        $this->_setCanReadPriceFalse($block);
        if (!$this->_canEditProductPrice) {
            $block->setCanEditPrice(false);
        }
    }

    /**
     * Set edit and read price in child block to false
     *
     * @param Template $block
     * @return void
     */
    protected function _setOptionsEditReadFalse($block)
    {
        if (!$this->_canEditProductPrice) {
            $optionsBoxBlock = $block->getChildBlock('options_box');
            if (!is_null($optionsBoxBlock)) {
                $optionsBoxBlock->setCanEditPrice(false);
                if (!$this->_canReadProductPrice) {
                    $optionsBoxBlock->setCanReadPrice(false);
                }
            }
        }
    }

    /**
     * Set default product price
     *
     * @param Template $block
     * @return void
     */
    protected function _setCanEditReadDefaultPrice($block)
    {
        // Handle Price tab of bundle product
        if (!$this->_canEditProductPrice) {
            $block->setDefaultProductPrice($this->_defaultProductPriceString);
        }
    }

    /**
     * Set edit and read price to child block
     *
     * @param Template $block
     * @return void
     */
    protected function _setCanEditReadChildBlock($block)
    {
        // Handle selection prices of bundle product with fixed price
        $selectionTemplateBlock = $block->getChildBlock('selection_template');
        if (!$this->_canReadProductPrice) {
            $block->setCanReadPrice(false);
            if (!is_null($selectionTemplateBlock)) {
                $selectionTemplateBlock->setCanReadPrice(false);
            }
        }
        if (!$this->_canEditProductPrice) {
            $block->setCanEditPrice(false);
            if (!is_null($selectionTemplateBlock)) {
                $selectionTemplateBlock->setCanEditPrice(false);
            }
        }
    }

    /**
     * Handle adminhtml_block_html_before event
     *
     * @param EventObserver $observer
     * @return void
     */
    public function adminhtmlBlockHtmlBefore($observer)
    {
        /** @var $block Template */
        $block = $observer->getBlock();

        $this->_filterByBlockName($block);

        // Handle prices that are shown when admin reviews customers shopping cart
        if (stripos($block->getNameInLayout(), 'customer_cart_') === 0) {
            if (!$this->_canReadProductPrice) {
                if ($block->getParentBlock()->getNameInLayout() == 'admin.customer.carts') {
                    $this->_removeColumnFromGrid($block, 'price');
                    $this->_removeColumnFromGrid($block, 'total');
                }
            }
        }
    }

    /**
     * Remove columns from grid
     *
     * @param Grid $block
     * @param array $columns
     * @return void
     */
    protected function _removeColumnsFromGrid($block, array $columns)
    {
        if (!$this->_canReadProductPrice) {
            foreach ($columns as $column) {
                $this->_removeColumnFromGrid($block, $column);
            }
        }
    }

    /**
     * Remove column from grid
     *
     * @param Grid $block
     * @param string $column
     * @return Grid|bool
     */
    protected function _removeColumnFromGrid($block, $column)
    {
        if (!$block instanceof \Magento\Backend\Block\Widget\Grid\Extended) {
            return false;
        }
        return $block->removeColumn($column);
    }

    /**
     * Handle view_block_abstract_to_html_before event
     *
     * @param EventObserver $observer
     * @return void
     */
    public function viewBlockAbstractToHtmlBefore($observer)
    {
        /** @var $block \Magento\Framework\View\Element\AbstractBlock */
        $block = $observer->getBlock();
        $blockNameInLayout = $block->getNameInLayout();
        switch ($blockNameInLayout) {
            // Handle product Recurring Payment tab
            case 'adminhtml_recurring_payment_edit_form':
                if (!$this->_coreRegistry->registry('product')->isObjectNew()) {
                    if (!$this->_canReadProductPrice) {
                        $block->setProductEntity($this->_productFactory->create());
                    }
                }
                if (!$this->_canEditProductPrice) {
                    $block->setIsReadonly(true);
                }
                break;
            case 'adminhtml_recurring_payment_edit_form_dependence':
                if (!$this->_canEditProductPrice) {
                    $block->addConfigOptions(array('can_edit_price' => false));
                    if (!$this->_canReadProductPrice) {
                        $dependenceValue = $this->_coreRegistry->registry('product')->getIsRecurring() ? '0' : '1';
                        // Override previous dependence value
                        $block->addFieldDependence(
                            'product[recurring_payment]',
                            'product[is_recurring]',
                            $dependenceValue
                        );
                    }
                }
                break;
                // Handle MAP functionality for bundle products
            case 'adminhtml.catalog.product.edit.tab.attributes':
                if (!$this->_canEditProductPrice) {
                    $block->setCanEditPrice(false);
                }
                break;
        }
    }

    /**
     * Handle catalog_product_load_after event
     *
     * @param EventObserver $observer
     * @return void
     */
    public function catalogProductLoadAfter(EventObserver $observer)
    {
        /** @var $product \Magento\Catalog\Model\Product */
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
     * @param EventObserver $observer
     * @return void
     */
    public function catalogProductSaveBefore(EventObserver $observer)
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $observer->getEvent()->getDataObject();
        if ($product->isObjectNew() && !$this->_canEditProductStatus) {
            $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
        }
    }

    /**
     * Handle adminhtml_catalog_product_edit_prepare_form event
     *
     * @param EventObserver $observer
     * @return void
     */
    public function adminhtmlCatalogProductEditPrepareForm(EventObserver $observer)
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->_coreRegistry->registry('product');
        if ($product->isObjectNew()) {
            $form = $observer->getEvent()->getForm();
            // Disable Status drop-down if needed
            if (!$this->_canEditProductStatus) {
                $statusElement = $form->getElement('status');
                if (!is_null($statusElement)) {
                    $statusElement->setValue(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                    $statusElement->setReadonly(true, true);
                }
            }
        }
    }

    /**
     * Handle adminhtml_catalog_product_form_prepare_excluded_field_list event
     *
     * @param EventObserver $observer
     * @return void
     */
    public function adminhtmlCatalogProductFormPrepareExcludedFieldList($observer)
    {
        /** @var $block \Magento\Catalog\Block\Adminhtml\Product\Edit\Action\Attribute\Tab_Attributes */
        $block = $observer->getEvent()->getObject();
        $excludedFieldList = array();

        if (!$this->_canEditProductPrice) {
            $excludedFieldList = array(
                'price',
                'special_price',
                'tier_price',
                'group_price',
                'special_from_date',
                'special_to_date',
                'is_recurring',
                'cost',
                'price_type',
                'open_amount_max',
                'open_amount_min',
                'allow_open_amount',
                'giftcard_amounts',
                'msrp_enabled',
                'msrp_display_actual_price_type',
                'msrp'
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
     * @param EventObserver $observer
     * @return void
     */
    public function catalogProductAttributeUpdateBefore($observer)
    {
        /** @var $block \Magento\Catalog\Block\Adminhtml\Product\Edit\Action\Attribute\Tab_Attributes */
        $attributesData = $observer->getEvent()->getAttributesData();
        $excludedAttributes = array();

        if (!$this->_canEditProductPrice) {
            $excludedAttributes = array(
                'price',
                'special_price',
                'tier_price',
                'group_price',
                'special_from_date',
                'special_to_date',
                'is_recurring',
                'cost',
                'price_type',
                'open_amount_max',
                'open_amount_min',
                'allow_open_amount',
                'giftcard_amounts',
                'msrp_enabled',
                'msrp_display_actual_price_type',
                'msrp'
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
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     * @return void
     */
    protected function _hidePriceElements($block)
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->_coreRegistry->registry('product');
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
                'open_amount_max',
                'open_amount_min',
                'allow_open_amount',
                'giftcard_amounts',
                'msrp_enabled',
                'msrp_display_actual_price_type',
                'msrp'
            );

            // Leave price element for bundle product active in order to change/view price type when product is created
            $typeId = $this->_coreRegistry->registry('product')->getTypeId();
            if ($typeId != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                $priceElementIds[] = 'price';
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
                // Handle Recurring Payment tab
                if ($form->getElement('recurring_payment')) {
                    $form->getElement(
                        'recurring_payment'
                    )->setReadonly(
                        true,
                        true
                    )->getForm()->setReadonly(
                        true,
                        true
                    );
                }
            }

            if ($product->isObjectNew()) {
                if (!$this->_canEditProductPrice) {
                    // For each type of products accept except Bundle products, set default value for price if allowed
                    $priceElement = $form->getElement('price');
                    if (!is_null(
                        $priceElement
                    ) && $this->_canReadProductPrice && $typeId != \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE
                    ) {
                        $priceElement->setValue($this->_defaultProductPriceString);
                    }
                    // For giftcard products set default amount
                    $amountsElement = $form->getElement('giftcard_amounts');
                    if (!is_null($amountsElement)) {
                        $storeId = (int)$this->_request->getParam('store', 0);
                        $websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId();
                        $amountsElement->setValue(
                            array(
                                array(
                                    'website_id' => $websiteId,
                                    'value' => $this->_defaultProductPriceString,
                                    'website_value' => (double)$this->_defaultProductPriceString
                                )
                            )
                        );
                    }
                }
            }
        }
    }
}
