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

namespace Magento\Rma\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class Returns
 * View returns page
 *
 * @package Magento\Rma\Test\Page
 */
class RmaEdit extends Page
{
    /**
     * URL for RMA Edit page
     */
    const MCA = 'admin/rma/edit';

    /**
     * Rma edit tabs block
     *
     * @var string
     */
    protected $formTabsBlock = '#rma_info_tabs';

    /**
     * Rma actions block
     *
     * @var string
     */
    protected $rmaActionsBlock = '.page-actions';

    /**
     * Rma edit form block
     *
     * @var string
     */
    protected $rmaEditBlock = 'edit_form';

    /**
     * Messages Block
     *
     * @var string
     */
    protected $messagesBlock = 'messages';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get Rma info tabs block
     *
     * @return \Magento\Rma\Test\Block\Adminhtml\Rma\Edit\Tabs
     */
    public function getFormTabsBlock()
    {
        return Factory::getBlockFactory()->getMagentoRmaAdminhtmlRmaEditTabs(
            $this->_browser->find($this->formTabsBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get Rma actions block
     *
     * @return \Magento\Rma\Test\Block\Adminhtml\Rma\Actions
     */
    public function getRmaActionsBlock()
    {
        return Factory::getBlockFactory()->getMagentoRmaAdminhtmlRmaActions(
            $this->_browser->find($this->rmaActionsBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get Rma Edit Form block
     *
     * @return \Magento\Rma\Test\Block\Adminhtml\Rma\Edit\Tab\Items
     */
    public function getRmaEditFormBlock()
    {
        return Factory::getBlockFactory()->getMagentoRmaAdminhtmlRmaEditTabItems(
            $this->_browser->find($this->rmaEditBlock, Locator::SELECTOR_ID)
        );
    }

    /**
     * Get global messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessageBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find($this->messagesBlock, Locator::SELECTOR_ID)
        );
    }
}
