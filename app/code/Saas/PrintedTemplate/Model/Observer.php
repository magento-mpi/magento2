<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Observer model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Saas_PrintedTemplate_Model_Observer
{
    /**
     * Array of widget types that should be visible only on printed template edit page
     *
     * @var array
     */
    protected $_widgetsToSkip = array(
        'Saas_PrintedTemplate_Block_Widget_ItemsGrid',
        'Saas_PrintedTemplate_Block_Widget_TaxGrid'
    );

    /**
     * Array of widget types that shouldn't be visible on printed template edit page
     *
     * @var array
     */
    protected $_widgetsToRemove = array(
        'Magento_Cms_Block_Widget_Page_Link',
        'Magento_Cms_Block_Widget_Block',
        'Magento_Catalog_Block_Product_Widget_New',
        'Magento_Catalog_Block_Product_Widget_Link',
        'Magento_Catalog_Block_Category_Widget_Link',
        'Magento_Reports_Block_Product_Widget_Viewed',
        'Magento_Reports_Block_Product_Widget_Compared',
        'Magento_Sales_Block_Widget_Guest_Form',
        'Enterprise_Cms_Block_Widget_Node',
        'Enterprise_Banner_Block_Widget_Banner',
    );

    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param Magento_AuthorizationInterface $authorization
     */
    public function __construct(Magento_AuthorizationInterface $authorization)
    {
        $this->_authorization = $authorization;
    }

    /**
     * Save order detailed tax information on event sales_order_save_after
     *
     * @param Magento_Event_Observer $observer
     * @return Saas_PrintedTemplate_Model_Observer
     */
    public function saveOrderTaxDetails(Magento_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if (!$order->getConvertingFromQuote() || $order->getAppliedTaxDetailsIsSaved()) {
            return;
        }

        $calculation = Mage::getModel('Saas_PrintedTemplate_Model_Tax_Details');

        // Save order items tax details information
        $itemsTaxDetails = $calculation->calculateItemsTaxInfo($order->getQuote());
        foreach ($order->getAllItems() as $item) {
            if (isset($itemsTaxDetails[$item->getQuoteItemId()])) {
                $rates = $itemsTaxDetails[$item->getQuoteItemId()];
                foreach ($rates as $rate) {
                    Mage::getModel('Saas_PrintedTemplate_Model_Tax_Order_Item')
                        ->setData($rate)
                        ->setItemId($item->getId())
                        ->save();
                }
            }
        }

        // Save order shipping tax details information
        $shippingTaxRates = $calculation->calculateShippingTaxInfo($order->getQuote());
        foreach ($shippingTaxRates as $rate) {
            Mage::getModel('Saas_PrintedTemplate_Model_Tax_Order_Shipping')
                ->setData($rate)
                ->setOrderId($order->getId())
                ->save();
        }

        $order->setAppliedTaxDetailsIsSaved(true);

        return $this;
    }

    /**
     * Remove printed template widgets from wysiwyg editors
     * observes cms_wysiwyg_config_prepare event
     *
     * @param Magento_Event_Observer $observer
     * @return Saas_PrintedTemplate_Model_Observer
     */
    public function removeWidgetsFromWysiwyg(Magento_Event_Observer $observer)
    {
        $config = $observer->getEvent()->getConfig();
        $skipTemplate = true;
        if ($config->hasSkipPrintedTemplateWidgets()) {
            $skipTemplate = $config->getSkipPrintedTemplateWidgets();
        }
        if ($skipTemplate) {
            $this->_addWidgetsToSkip($this->_widgetsToSkip, $config);
        } else {
            $this->_addWidgetsToSkip($this->_widgetsToRemove, $config);
        }

        return $this;
    }

    /**
     * Add observer to specific event
     *
     * @param string $area
     * @param string $eventName
     * @param string $observerName
     * @param string $observerClass
     * @param string $observerMethod
     * @return Saas_PrintedTemplate_Model_Observer
     */
    protected function _addObserver($area, $eventName, $observerName, $observerClass, $observerMethod)
    {
        /** @var $eventManager Magento_Core_Model_Event_Manager */
        $eventManager = Mage::getSingleton('Magento_Core_Model_Event_Manager');
        $eventManager->addObservers($area, $eventName, array(
            $observerName => array(
                'type' => 'model',
                'model' => $observerClass,
                'method' => $observerMethod
            )
        ));
        return $this;
    }

    /**
     * Dynamically add adminhtml_block_html_before event observer for adminhtml_widget_instance_edit action
     * observes controller_action_predispatch_adminhtml_widget_instance_edit event
     *
     * @return Saas_PrintedTemplate_Model_Observer
     */
    public function addWidgetInstanceFormBlockRenderingObserver()
    {
        $this->_addObserver('adminhtml',
            'adminhtml_block_html_before',
            'saas_printedtemplate_remove_widgets_from_widget_instance_form',
            'Saas_PrintedTemplate_Model_Observer',
            'removeWidgetsFromWidgetInstanceForm'
        );

        return $this;
    }

    /**
     * Remove printed template widgets from widget instance edit form
     *
     * @param Magento_Event_Observer $observer
     * @return Saas_PrintedTemplate_Model_Observer
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function removeWidgetsFromWidgetInstanceForm(Magento_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (!($block instanceof Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Settings
            || $block instanceof Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main)) {
            return $this;
        }

        $form = $block->getForm();
        if (!$form) {
            return $this;
        }

        $fieldset = $form->getElement('base_fieldset');
        if (!$fieldset) {
            return $this;
        }

        foreach ($fieldset->getElements() as $element) {
            if ($element->getId() == 'type') {
                $values = $element->getValues();
                if (is_array($values)) {
                    foreach ($values as $key => $value) {
                        if (in_array($value['value'], $this->_widgetsToSkip)) {
                            unset($values[$key]);
                        }
                    }
                }
                $element->setValues($values);
                break;
            }
        }

        return $this;
    }

    /**
     * Dynamically add adminhtml_block_html_before event observer for adminhtml_widget_instance_index action
     * observes controller_action_predispatch_adminhtml_widget_instance_index event
     *
     * @return Saas_PrintedTemplate_Model_Observer
     */
    public function addWidgetInstanceGridBlockRenderingObserver()
    {
        $this->_addObserver('adminhtml',
            'adminhtml_block_html_before',
            'saas_printedtemplate_remove_widgets_from_widget_instance_grid_filter',
            'Saas_PrintedTemplate_Model_Observer',
            'removeWidgetsFromWidgetInstanceGridFilter'
        );

        return $this;
    }

    /**
     * Remove printed template widgets from widget instances grid filter
     *
     * @param Magento_Event_Observer $observer
     * @return Saas_PrintedTemplate_Model_Observer
     */
    public function removeWidgetsFromWidgetInstanceGridFilter(Magento_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (!($block->getNameInLayout() == 'adminhtml.widget.instance.grid.container' &&
            $block instanceof Magento_Adminhtml_Block_Widget_Grid)) {
            return $this;
        }

        $column = $block->getColumn('type');
        if (!$column) {
            return $this;
        }

        $options = $column->getOptions();
        if (is_array($options)) {
            foreach ($this->_widgetsToSkip as $widgetType) {
                if (isset($options[$widgetType])) {
                    unset($options[$widgetType]);
                }
            }
            $column->setOptions($options);
        }

        return $this;
    }

    /**
     * Replace URL for print invoices massaction
     * Remove controls if user is not allowed to print documents
     *
     * Observe adminhtml_block_html_before event
     *
     * @param Magento_Event_Observer $observer
     * @return Saas_PrintedTemplate_Model_Observer
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function updatePrintTemplateAction(Magento_Event_Observer $observer)
    {
        if (!Mage::getStoreConfig('sales_pdf/general/enable_printed_templates')) {
            return $this;
        }

        $block = $observer->getEvent()->getBlock();
        if (!$this->_authorization->isAllowed('Saas_PrintedTemplate::print')) {
            if ($block instanceof Mage_Backend_Block_Widget_Grid
                && $block->getMassactionBlock() instanceof Mage_Backend_Block_Widget) {
                $gridBlocks = array('pdfdocs_order','pdfshipments_order','pdfcreditmemos_order','pdfinvoices_order');
                foreach ($gridBlocks as $_item) {
                    $item = $block->getMassactionBlock()->getItem($_item);
                    if ($item) {
                        $block->getMassactionBlock()->removeItem($item->getId());
                    }
                }
            }

            if ($block instanceof Mage_Backend_Block_Widget_Form_Container) {
                $blocks = array('sales_creditmemo_view', 'sales_invoice_view', 'sales_shipment_view');
                if (in_array($block->getNameInLayout(), $blocks)) {
                    $block->removeButton('print');
                }
            }

            return $this;
        }

        if ($block instanceof Magento_Adminhtml_Block_Sales_Order_Grid) {
            $this->_setMassactionPrintEntitiesUrl($block, 'pdfinvoices_order', 'invoice')
                ->_setMassactionPrintEntitiesUrl($block, 'pdfcreditmemos_order', 'creditmemo')
                ->_setMassactionPrintEntitiesUrl($block, 'pdfshipments_order', 'shipment')
                ->_setMassactionPrintEntitiesUrl($block, 'pdfdocs_order', 'all');
        } else if ($block instanceof Magento_Adminhtml_Block_Sales_Invoice_Grid) {
            $this->_setMassactionPrintEntitiesUrl($block, 'pdfinvoices_order', 'invoice');
        } else if ($block instanceof Magento_Adminhtml_Block_Sales_Creditmemo_Grid) {
            $this->_setMassactionPrintEntitiesUrl($block, 'pdfcreditmemos_order', 'creditmemo');
        } else if ($block instanceof Magento_Adminhtml_Block_Sales_Shipment_Grid) {
            $this->_setMassactionPrintEntitiesUrl($block, 'pdfshipments_order', 'shipment');
        }

        return $this;
    }

    /**
     * Replace massaction item URL in the grid block
     *
     * @param Magento_Adminhtml_Block_Widget_Grid|Mage_Backend_Block_Widget_Grid $block grid block
     * @param string $itemName the name of mass action item
     * @param string $type entity type
     * @return Saas_PrintedTemplate_Model_Observer
     */
    protected function _setMassactionPrintEntitiesUrl(Mage_Backend_Block_Widget_Grid $block, $itemName, $type)
    {
        $item = $block->getMassactionBlock()->getItem($itemName);
        if ($item) {
            if ($type == 'all') {
                $item->setUrl(Mage::helper('Mage_Backend_Helper_Data')->getUrl('adminhtml/print/allEntities'));
            } else {
                $item->setUrl(Mage::helper('Mage_Backend_Helper_Data')
                    ->getUrl('adminhtml/print/entities', array('type' => $type)));
            }
        }

        return $this;
    }

    /**
     * Adds to config array of widgets to skip
     *
     * @param array $widgetsToSkip
     * @param Magento_Object $config
     */
    protected function _addWidgetsToSkip(array $widgetsToSkip, $config)
    {
        $currentWidgetsToSkip = array();
        if ($config->hasSkipWidgets()) {
            $currentWidgetsToSkip = $config->getSkipWidgets();
        }
        $config->setSkipWidgets(array_merge($currentWidgetsToSkip, $widgetsToSkip));
        if ($config->hasWidgetWindowUrl()) {
            $config->setWidgetWindowUrl(Mage::getModel('Mage_Widget_Model_Widget_Config')->getWidgetWindowUrl($config));
        }
    }
}
