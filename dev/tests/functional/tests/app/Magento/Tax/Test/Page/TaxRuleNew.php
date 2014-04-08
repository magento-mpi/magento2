<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\FormPageActions;

/**
 * Class TaxRuleNew
 * Class for new tax rule page
 *
 * @package Magento\Tax\Test\Page
 */
class TaxRuleNew extends Page
{
    /**
     * URL for new tax rule
     */
    const MCA = 'tax/rule/new/';

    /**
     * Form for tax rule creation
     *
     * @var string
     */
    protected $editBlock = '[id="page:main-container"]';

    /**
     * Global messages block
     *
     * @var string
     */
    protected $messagesBlock = '#messages .messages';

    /**
     * Form page actions block
     *
     * @var string
     */
    protected $pageActionsBlock = '.page-main-actions';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get form for tax rule creation
     *
     * @return \Magento\Tax\Test\Block\Adminhtml\Rule\Edit\Form
     */
    public function getEditBlock()
    {
        return Factory::getBlockFactory()->getMagentoTaxAdminhtmlRuleEditForm(
            $this->_browser->find($this->editBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get global messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find($this->messagesBlock, Locator::SELECTOR_CSS)
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
