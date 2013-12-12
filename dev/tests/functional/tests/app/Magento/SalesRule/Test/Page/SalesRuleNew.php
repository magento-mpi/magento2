<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Test\Page;

use Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Form;
use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Core\Test\Block\Messages;
use Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Tab\Actions;
use Magento\Backend\Test\Block\Widget\FormTabs;

class SalesRuleNew extends Page
{
    /**
     * URL for customer login
     */
    const MCA = 'sales_rule/promo_quote/new';

    /**
     * Id of Conditions Tab
     */
    const CONDITIONS_TAB_SELECTOR = 'promo_catalog_edit_tabs_conditions_section';

    /**
     * Constant of ACTIONS Tab Id
     */
    const ACTIONS_TAB_SELECTOR = 'promo_catalog_edit_tabs_actions_section';

    const CONDITIONS_CHILD_SELECTOR = 'conditions__1__children';

    const MESSAGES_BLOCK_SELECTOR = '#messages .messages';

    const PROMO_QUOTE_FORM_SELECTOR = 'page:main-container';

    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * @return Form
     */
    public function getPromoQuoteForm()
    {
        return Factory::getBlockFactory()->getMagentoSalesRuleAdminhtmlPromoQuoteEditForm(
            $this->_browser->find(self::PROMO_QUOTE_FORM_SELECTOR, Locator::SELECTOR_ID)
        );
    }

    /**
     * @return Messages
     */
    public function getMessageBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find(self::MESSAGES_BLOCK_SELECTOR)
        );
    }

    /**
     * @return FormTabs
     */
    public function getConditionsFormTab()
    {
        return Factory::getBlockFactory()->getMagentoBackendWidgetFormTabs(
            $this->_browser->find(self::CONDITIONS_TAB_SELECTOR, Locator::SELECTOR_ID)
        );
    }

    /**
     * @return FormTabs
     */
    public function getActionsFormTab()
    {
        return Factory::getBlockFactory()->getMagentoBackendWidgetFormTabs(
            $this->_browser->find(self::ACTIONS_TAB_SELECTOR, Locator::SELECTOR_ID)
        );
    }

    /**
     * @return Actions
     */
    public function getConditionsActions()
    {
        return Factory::getBlockFactory()->getMagentoSalesRuleAdminhtmlPromoQuoteEditTabActions(
            $this->_browser->find(self::CONDITIONS_CHILD_SELECTOR, Locator::SELECTOR_ID)
        );
    }
}
