<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Test\Page;

use Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Form;
use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Core\Test\Block\Messages;
use Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Tab\Conditions;
use Magento\Backend\Test\Block\Widget\FormTabs;
use Magento\Backend\Test\Block\FormPageActions;

/**
 * Class SalesRuleNew
 *
 */
class SalesRuleNew extends Page
{
    /**
     * URL for customer login
     */
    const MCA = 'sales_rule/promo_quote/new';

    /**
     * Id of Conditions Tab
     *
     * @var string
     */
    protected $conditionsTabId = 'conditions';

    /**
     * ACTIONS Tab Id
     *
     * @var string
     */
    protected $actionTabId = 'promo_catalog_edit_tabs_actions_section';

    /**
     * Condition Child Selector
     *
     * @var string
     */
    protected $conditionsChildSelector = 'conditions__1__children';

    /**
     * Message Block Selector
     *
     * @var string
     */
    protected $messageBlockSelector = '#messages .messages';

    /**
     * Promo Quote Form Selector
     *
     * @var string
     */
    protected $promoQuoteFormSelector = 'page:main-container';

    /**
     * Form page actions block
     *
     * @var string
     */
    protected $pageActionsBlock = '.page-main-actions';

    /**
     * {@inheritDoc}
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get the Promo Quote Form
     *
     * @return Form
     */
    public function getPromoQuoteForm()
    {
        return Factory::getBlockFactory()->getMagentoSalesRuleAdminhtmlPromoQuoteEditForm(
            $this->_browser->find($this->promoQuoteFormSelector, Locator::SELECTOR_ID)
        );
    }

    /**
     * Get the Messages Block
     *
     * @return Messages
     */
    public function getMessageBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages($this->_browser->find($this->messageBlockSelector));
    }

    /**
     * Get the Actions Tab
     *
     * @return FormTabs
     */
    public function getActionsFormTab()
    {
        return Factory::getBlockFactory()->getMagentoBackendWidgetFormTabs(
            $this->_browser->find($this->actionTabId, Locator::SELECTOR_ID)
        );
    }

    /**
     * Get the Conditions Form Tab
     *
     * @return FormTabs
     */
    public function getConditionsFormTab()
    {
        return Factory::getBlockFactory()->getMagentoBackendWidgetFormTabs(
            $this->_browser->find($this->conditionsTabId, Locator::SELECTOR_ID)
        );
    }

    /**
     * Get the Conditions Tab
     *
     * @return Conditions
     */
    public function getConditionsTab()
    {
        return Factory::getBlockFactory()->getMagentoSalesRuleAdminhtmlPromoQuoteEditTabConditions(
            $this->_browser->find($this->conditionsChildSelector, Locator::SELECTOR_ID)
        );
    }

    /**
     * Get Form page actions block
     *
     * @return FormPageActions
     */
    public function getPageActionsBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendFormPageActions(
            $this->_browser->find($this->pageActionsBlock)
        );
    }
}
