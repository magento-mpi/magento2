<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PromotionPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Promotion Permissions Observer
 *
 * @category    Enterprise
 * @package     Enterprise_PromotionPermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_PromotionPermissions_Model_Observer
{
    /**
     * Instance of http request
     *
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * Edit Catalog Rules flag
     *
     * @var boolean
     */
    protected $_canEditCatalogRules;

    /**
     * Edit Sales Rules flag
     *
     * @var boolean
     */
    protected $_canEditSalesRules;

    /**
     * Edit Reminder Rules flag
     *
     * @var boolean
     */
    protected $_canEditReminderRules;

    /**
     * Enterprise_Banner flag
     *
     * @var boolean
     */
    protected $_isEnterpriseBannerEnabled;

    /**
     * Enterprise_Reminder flag
     *
     * @var boolean
     */
    protected $_isEnterpriseReminderEnabled;

    /**
     * Promotion Permissions Observer class constructor
     *
     * Sets necessary data
     */
    public function __construct()
    {
        $this->_request = Mage::app()->getRequest();
        // Set necessary flags
        $helper = Mage::helper('Enterprise_PromotionPermissions_Helper_Data');
        $this->_canEditCatalogRules = $helper->getCanAdminEditCatalogRules();
        $this->_canEditSalesRules = $helper->getCanAdminEditSalesRules();
        $this->_canEditReminderRules = $helper->getCanAdminEditReminderRules();

        $this->_isEnterpriseBannerEnabled = $helper->isModuleEnabled('Enterprise_Banner');
        $this->_isEnterpriseReminderEnabled = $helper->isModuleEnabled('Enterprise_Reminder');
    }

    /**
     * Handle core_block_abstract_to_html_before event
     *
     * @param Magento_Event_Observer $observer
     * @return void
     */
    public function coreBlockAbstractToHtmlBefore($observer)
    {
         /** @var $block Mage_Core_Block_Abstract */
        $block = $observer->getBlock();
        $blockNameInLayout = $block->getNameInLayout();
        switch ($blockNameInLayout) {
            // Handle General Tab on Edit Reminder Rule page
            case 'adminhtml_reminder_edit_tab_general' :
                if (!$this->_canEditReminderRules) {
                    $block->setCanEditReminderRule(false);
                }
                break;
        }
    }

    /**
     * Handle adminhtml_block_html_before event
     *
     * @param Magento_Event_Observer $observer
     * @return void
     */
    public function adminhtmlBlockHtmlBefore($observer)
    {
        /** @var $block Mage_Adminhtml_Block_Template */
        $block = $observer->getBlock();
        $blockNameInLayout = $block->getNameInLayout();
        switch ($blockNameInLayout) {
            // Handle blocks related to Mage_CatalogRule module
            case 'promo_catalog' :
                if (!$this->_canEditCatalogRules) {
                    $block->removeButton('add');
                    $block->removeButton('apply_rules');
                }
                break;
            case 'promo_catalog_edit' :
                if (!$this->_canEditCatalogRules) {
                    $block->removeButton('delete');
                    $block->removeButton('save');
                    $block->removeButton('save_and_continue_edit');
                    $block->removeButton('save_apply');
                    $block->removeButton('reset');
                }
                break;
            case 'promo_catalog_edit_tab_main' :
            case 'promo_catalog_edit_tab_actions' :
            case 'promo_catalog_edit_tab_conditions' :
                if (!$this->_canEditCatalogRules) {
                    $block->getForm()->setReadonly(true, true);
                }
                break;
            // Handle blocks related to Mage_SalesRule module
            case 'promo_quote' :
                if (!$this->_canEditSalesRules) {
                    $block->removeButton('add');
                }
                break;
            case 'promo_quote_edit' :
                if (!$this->_canEditSalesRules) {
                    $block->removeButton('delete');
                    $block->removeButton('save');
                    $block->removeButton('save_and_continue_edit');
                    $block->removeButton('reset');
                }
                break;
            case 'promo_quote_edit_tab_main':
                if (!$this->_canEditSalesRules) {
                    $block->unsetChild('form_after');
                }
            // no break needed
            case 'promo_quote_edit_tab_actions' :
            case 'promo_quote_edit_tab_conditions' :
            case 'promo_quote_edit_tab_labels' :
                if (!$this->_canEditSalesRules) {
                    $block->getForm()->setReadonly(true, true);
                }
                break;
            // Handle blocks related to Enterprise_Reminder module
            case 'enterprise_reminder' :
                if (!$this->_canEditReminderRules) {
                    $block->removeButton('add');
                }
                break;
            case 'adminhtml_reminder_edit' :
                if (!$this->_canEditReminderRules) {
                    $block->removeButton('save');
                    $block->removeButton('delete');
                    $block->removeButton('reset');
                    $block->removeButton('save_and_continue_edit');
                    $block->removeButton('run_now');
                }
                break;
            case 'adminhtml_reminder_edit_tab_conditions' :
            case 'adminhtml_reminder_edit_tab_templates' :
                if (!$this->_canEditReminderRules) {
                    $block->getForm()->setReadonly(true, true);
                }
                break;
            // Handle blocks related to Enterprise_Banner module
            case 'related_catalogrule_banners_grid' :
                if ($this->_isEnterpriseBannerEnabled && !$this->_canEditCatalogRules) {
                    $block->getColumn('in_banners')
                        ->setDisabledValues(Mage::getModel('Enterprise_Banner_Model_Banner')->getCollection()->getAllIds());
                    $block->getColumn('in_banners')->setDisabled(true);
                }
                break;
            case 'related_salesrule_banners_grid' :
                if ($this->_isEnterpriseBannerEnabled && !$this->_canEditSalesRules) {
                    $block->getColumn('in_banners')
                        ->setDisabledValues(Mage::getModel('Enterprise_Banner_Model_Banner')->getCollection()->getAllIds());
                    $block->getColumn('in_banners')->setDisabled(true);
                }
                break;
            case 'promo_quote_edit_tabs' :
                if ($this->_isEnterpriseBannerEnabled && !$this->_canEditSalesRules) {
                    $relatedBannersBlock = $block->getChildBlock('salesrule.related.banners');
                    if (!is_null($relatedBannersBlock)) {
                        $relatedBannersBlock->unsetChild('banners_grid_serializer');
                    }
                }
                break;
            case 'promo_catalog_edit_tabs' :
                if ($this->_isEnterpriseBannerEnabled && !$this->_canEditCatalogRules) {
                    $relatedBannersBlock = $block->getChildBlock('catalogrule.related.banners');
                    if ($relatedBannersBlock) {
                        $relatedBannersBlock->unsetChild('banners_grid_serializer');
                    }
                }
                break;
        }
    }

    /**
     * Handle controller_action_predispatch event
     *
     * @param Magento_Event_Observer $observer
     * @return void
     */
    public function controllerActionPredispatch($observer)
    {
        $controllerAction = $observer->getControllerAction();
        $controllerActionName = $this->_request->getActionName();
        $forbiddenActionNames = array('new', 'applyRules', 'save', 'delete', 'run');

        if (in_array($controllerActionName, $forbiddenActionNames)
            && ((!$this->_canEditSalesRules
            && $controllerAction instanceof Mage_Adminhtml_Promo_QuoteController)
            || (!$this->_canEditCatalogRules
            && $controllerAction instanceof Mage_Adminhtml_Promo_CatalogController)
            || ($this->_isEnterpriseReminderEnabled && !$this->_canEditReminderRules
            && $controllerAction instanceof Enterprise_Reminder_Adminhtml_ReminderController))
        ) {
            $this->_forward();
        }
    }

    /**
     * Forward current request
     *
     * @param string $action
     * @param string $module
     * @param string $controller
     * @return void
     */
    protected function _forward($action = 'denied', $module = null, $controller = null)
    {
        if ($this->_request->getActionName() === $action
            && (null === $module || $this->_request->getModuleName() === $module)
            && (null === $controller || $this->_request->getControllerName() === $controller)
        ) {
            return;
        }

        $this->_request->initForward();

        if ($module) {
            $this->_request->setModuleName($module);
        }
        if ($controller) {
            $this->_request->setControllerName($controller);
        }
        $this->_request->setActionName($action)->setDispatched(false);
    }
}
