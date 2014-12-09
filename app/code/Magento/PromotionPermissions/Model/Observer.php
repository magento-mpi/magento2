<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Promotion Permissions Observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\PromotionPermissions\Model;

class Observer
{
    /**
     * Instance of http request
     *
     * @var \Magento\Framework\App\RequestInterface
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
     * \Magento\Banner flag
     *
     * @var boolean
     */
    protected $_isEnterpriseBannerEnabled;

    /**
     * \Magento\Reminder flag
     *
     * @var boolean
     */
    protected $_isEnterpriseReminderEnabled;

    /**
     * @var \Magento\Banner\Model\Resource\Banner\Collection
     */
    protected $_bannerCollection;

    /**
     * @param \Magento\PromotionPermissions\Helper\Data $promoPermData
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Banner\Model\Resource\Banner\Collection $bannerCollection
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Magento\PromotionPermissions\Helper\Data $promoPermData,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Banner\Model\Resource\Banner\Collection $bannerCollection,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->_request = $request;
        $this->_bannerCollection = $bannerCollection;
        $this->_canEditCatalogRules = $promoPermData->getCanAdminEditCatalogRules();
        $this->_canEditSalesRules = $promoPermData->getCanAdminEditSalesRules();
        $this->_canEditReminderRules = $promoPermData->getCanAdminEditReminderRules();

        $this->_isEnterpriseBannerEnabled = $moduleManager->isEnabled('Magento_Banner');
        $this->_isEnterpriseReminderEnabled = $moduleManager->isEnabled('Magento_Reminder');
    }

    /**
     * Handle view_block_abstract_to_html_before event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function viewBlockAbstractToHtmlBefore($observer)
    {
        /** @var $block \Magento\Framework\View\Element\AbstractBlock */
        $block = $observer->getBlock();
        $blockNameInLayout = $block->getNameInLayout();
        switch ($blockNameInLayout) {
            // Handle General Tab on Edit Reminder Rule page
            case 'adminhtml_reminder_edit_tab_general':
                if (!$this->_canEditReminderRules) {
                    $block->setCanEditReminderRule(false);
                }
                break;
        }
    }

    /**
     * Handle adminhtml_block_html_before event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function adminhtmlBlockHtmlBefore($observer)
    {
        /** @var $block \Magento\Backend\Block\Template */
        $block = $observer->getBlock();
        $blockNameInLayout = $block->getNameInLayout();
        switch ($blockNameInLayout) {
            // Handle blocks related to \Magento\CatalogRule module
            case 'promo_catalog_edit_tab_main':
            case 'promo_catalog_edit_tab_actions':
            case 'promo_catalog_edit_tab_conditions':
                if (!$this->_canEditCatalogRules) {
                    $block->getForm()->setReadonly(true, true);
                }
                break;
            // Handle blocks related to \Magento\SalesRule module
            case 'promo_quote_edit_tab_main':
                if (!$this->_canEditSalesRules) {
                    $block->unsetChild('form_after');
                }
                // no break needed
            case 'promo_quote_edit_tab_actions':
            case 'promo_quote_edit_tab_conditions':
            case 'promo_quote_edit_tab_labels':
                if (!$this->_canEditSalesRules) {
                    $block->getForm()->setReadonly(true, true);
                }
                break;
            // Handle blocks related to \Magento\Reminder module
            case 'adminhtml_reminder_edit_tab_conditions':
            case 'adminhtml_reminder_edit_tab_templates':
                if (!$this->_canEditReminderRules) {
                    $block->getForm()->setReadonly(true, true);
                }
                break;
            // Handle blocks related to \Magento\Banner module
            case 'related_catalogrule_banners_grid':
                if ($this->_isEnterpriseBannerEnabled && !$this->_canEditCatalogRules) {
                    $block->getColumn('in_banners')->setDisabledValues($this->_bannerCollection->getAllIds());
                    $block->getColumn('in_banners')->setDisabled(true);
                }
                break;
            case 'related_salesrule_banners_grid':
                if ($this->_isEnterpriseBannerEnabled && !$this->_canEditSalesRules) {
                    $block->getColumn('in_banners')->setDisabledValues($this->_bannerCollection->getAllIds());
                    $block->getColumn('in_banners')->setDisabled(true);
                }
                break;
            case 'promo_quote_edit_tabs':
                if ($this->_isEnterpriseBannerEnabled && !$this->_canEditSalesRules) {
                    $relatedBannersBlock = $block->getChildBlock('salesrule.related.banners');
                    if ($relatedBannersBlock instanceof \Magento\Framework\View\Element\AbstractBlock) {
                        $relatedBannersBlock->unsetChild('banners_grid_serializer');
                    }
                }
                break;
            case 'promo_catalog_edit_tabs':
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function controllerActionPredispatch($observer)
    {
        $controllerAction = $observer->getControllerAction();
        $controllerActionName = $this->_request->getActionName();
        $forbiddenActionNames = array('new', 'applyRules', 'save', 'delete', 'run');

        if (in_array(
            $controllerActionName,
            $forbiddenActionNames
        ) &&
            (!$this->_canEditSalesRules &&
            $controllerAction instanceof \Magento\SalesRule\Controller\Adminhtml\Promo\Quote ||
            !$this->_canEditCatalogRules &&
            $controllerAction instanceof \Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog ||
            $this->_isEnterpriseReminderEnabled &&
            !$this->_canEditReminderRules &&
            $controllerAction instanceof \Magento\Reminder\Controller\Adminhtml\Reminder)
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
        if ($this->_request->getActionName() === $action && (null === $module ||
            $this->_request->getModuleName() === $module) && (null === $controller ||
            $this->_request->getControllerName() === $controller)
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
