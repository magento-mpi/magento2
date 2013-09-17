<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PromotionPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Promotion Permissions Observer
 *
 * @category    Magento
 * @package     Magento_PromotionPermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_PromotionPermissions_Model_Observer
{
    /**
     * Instance of http request
     *
     * @var Magento_Core_Controller_Request_Http
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
     * Magento_Banner flag
     *
     * @var boolean
     */
    protected $_isEnterpriseBannerEnabled;

    /**
     * Magento_Reminder flag
     *
     * @var boolean
     */
    protected $_isEnterpriseReminderEnabled;

    /**
     * Promotion Permissions Observer class constructor
     *
     * Sets necessary data
     *
     * @param Magento_PromotionPermissions_Helper_Data $promoPermData
     */
    public function __construct(
        Magento_PromotionPermissions_Helper_Data $promoPermData
    ) {
        $this->_request = Mage::app()->getRequest();
        $this->_canEditCatalogRules = $promoPermData->getCanAdminEditCatalogRules();
        $this->_canEditSalesRules = $promoPermData->getCanAdminEditSalesRules();
        $this->_canEditReminderRules = $promoPermData->getCanAdminEditReminderRules();

        $this->_isEnterpriseBannerEnabled = $promoPermData->isModuleEnabled('Magento_Banner');
        $this->_isEnterpriseReminderEnabled = $promoPermData->isModuleEnabled('Magento_Reminder');
    }

    /**
     * Handle core_block_abstract_to_html_before event
     *
     * @param Magento_Event_Observer $observer
     * @return void
     */
    public function coreBlockAbstractToHtmlBefore($observer)
    {
         /** @var $block Magento_Core_Block_Abstract */
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
        /** @var $block Magento_Adminhtml_Block_Template */
        $block = $observer->getBlock();
        $blockNameInLayout = $block->getNameInLayout();
        switch ($blockNameInLayout) {
            // Handle blocks related to Magento_CatalogRule module
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
            // Handle blocks related to Magento_SalesRule module
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
            // Handle blocks related to Magento_Reminder module
            case 'magento_reminder' :
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
            // Handle blocks related to Magento_Banner module
            case 'related_catalogrule_banners_grid' :
                if ($this->_isEnterpriseBannerEnabled && !$this->_canEditCatalogRules) {
                    $block->getColumn('in_banners')
                        ->setDisabledValues(Mage::getModel('Magento_Banner_Model_Banner')->getCollection()->getAllIds());
                    $block->getColumn('in_banners')->setDisabled(true);
                }
                break;
            case 'related_salesrule_banners_grid' :
                if ($this->_isEnterpriseBannerEnabled && !$this->_canEditSalesRules) {
                    $block->getColumn('in_banners')
                        ->setDisabledValues(Mage::getModel('Magento_Banner_Model_Banner')->getCollection()->getAllIds());
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
            && $controllerAction instanceof Magento_Adminhtml_Controller_Promo_Quote)
            || (!$this->_canEditCatalogRules
            && $controllerAction instanceof Magento_Adminhtml_Controller_Promo_Catalog)
            || ($this->_isEnterpriseReminderEnabled && !$this->_canEditReminderRules
            && $controllerAction instanceof Magento_Reminder_Controller_Adminhtml_Reminder))
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
