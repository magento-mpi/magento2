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

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Core\Test\Block\Messages;
use Magento\SalesRule\Test\Block\PromoQuoteForm;
use Magento\SalesRule\Test\Block\PromoQuoteForm\Actions;
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
    const CONDITIONS_TAB_ID = 'promo_catalog_edit_tabs_conditions_section';

    /**
     * Constant of ACTIONS Tab Id
     */
    const ACTIONS_TAB_ID = 'promo_catalog_edit_tabs_actions_section';

    /**
     * @var  Messages
     */
    private $messageBlock;

    /**
     * @var FormTabs
     */
    private $conditionsFormTab;
    /**
     * @var FormTabs
     */
    private $actionsFormTab;

    /**
     * @var Actions
     */
    private $actions;

    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->messageBlock = Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find('#messages .messages')
        );
        $this->conditionsFormTab = Factory::getBlockFactory()->getMagentoBackendWidgetFormTabs(
            $this->_browser->find('#'.self::CONDITIONS_TAB_ID)
        );
        $this->actionsFormTab = Factory::getBlockFactory()->getMagentoBackendWidgetFormTabs(
            $this->_browser->find('#'.self::ACTIONS_TAB_ID)
        );
        $this->actions = Factory::getBlockFactory()->getMagentoSalesRulePromoQuoteFormActions(
            $this->_browser->find('#conditions__1__children')
        );
    }

    /**
     * @return PromoQuoteForm
     */
    public function getPromoQuoteForm()
    {
        return Factory::getBlockFactory()->getMagentoSalesRulePromoQuoteForm(
            $this->_browser->find('[id="page:main-container"]')
        );
    }

    /**
     * @return Messages
     */
    public function getMessageBlock()
    {
        return $this->messageBlock;
    }

    /**
     * @return FormTabs
     */
    public function getConditionsFormTab()
    {
        return $this->conditionsFormTab;
    }

    /**
     * @return FormTabs
     */
    public function getActionsFormTab()
    {
        return $this->actionsFormTab;
    }

    /**
     * @return Actions
     */
    public function getConditionsActions()
    {
        return $this->actions;
    }
}
