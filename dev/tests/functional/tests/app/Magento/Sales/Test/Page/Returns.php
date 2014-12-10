<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Sales\Test\Page;

use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

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
