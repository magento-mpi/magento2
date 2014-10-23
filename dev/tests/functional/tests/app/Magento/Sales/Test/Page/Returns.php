<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class Returns
 * View returns page
 *
 */
class Returns extends Page
{
    /**
     * URL for returns page
     */
    const MCA = 'sales/guest/returns';

    /**
     * Form wrapper selector
     *
     * @var string
     */
    protected $blockSelector = 'div.order-items.returns';

    /**
     * Message selector
     *
     * @var string
     */
    protected $messageSelector = '.messages .messages';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get returns block
     *
     * @return \Magento\Rma\Test\Block\Returns\Returns
     */
    public function getReturnsReturnsBlock()
    {
        return Factory::getBlockFactory()->getMagentoRmaReturnsReturns(
            $this->_browser->find($this->blockSelector)
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
            $this->_browser->find($this->messageSelector, Locator::SELECTOR_CSS)
        );
    }
}
