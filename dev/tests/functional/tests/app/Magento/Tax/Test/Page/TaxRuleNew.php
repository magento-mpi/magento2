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
use Magento\Core\Test\Block\Messages;
use Magento\Tax\Test\Block\Adminhtml\Rule\Edit\Form;

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
     * @var Form
     */
    private $editBlock;

    /**
     * Global messages block
     *
     * @var Messages
     */
    private $messagesBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;

        $this->editBlock = Factory::getBlockFactory()->getMagentoTaxAdminhtmlRuleEditForm(
            $this->_browser->find('[id="page:main-container"]', Locator::SELECTOR_CSS)
        );
        $this->messagesBlock = Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find('#messages .messages')
        );
    }

    /**
     * Get form for tax rule creation
     *
     * @return \Magento\Tax\Test\Block\Adminhtml\Rule\Edit\Form
     */
    public function getEditBlock()
    {
        return $this->editBlock;
    }

    /**
     * Get global messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->messagesBlock;
    }
}
