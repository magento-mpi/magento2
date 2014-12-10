<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CurrencySymbol\Test\Block\Adminhtml\System\Currency;

use Magento\Backend\Test\Block\PageActions;
use Magento\Core\Test\Block\Messages;

/**
 * Grid page actions on the SystemCurrencyIndex page.
 */
class GridPageActions extends PageActions
{
    /**
     * Import button locator.
     *
     * @var string
     */
    protected $importButton = '[data-ui-id$="import-button"]';

    /**
     * Message block css selector.
     *
     * @var string
     */
    protected $message = '#messages';

    /**
     * Click Import button.
     *
     * @throws \Exception
     * @return void
     */
    public function clickImportButton()
    {
        $this->_rootElement->find($this->importButton)->click();

        //Wait message
        $browser = $this->browser;
        $selector = $this->message;
        $browser->waitUntil(
            function () use ($browser, $selector) {
                $message = $browser->find($selector);
                return $message->isVisible() ? true : null;
            }
        );
        if ($this->getMessageBlock()->isVisibleMessage('warning')) {
            throw new \Exception($this->getMessageBlock()->getWarningMessages());
        }
    }

    /**
     * Get message block.
     *
     * @return Messages
     */
    protected function getMessageBlock()
    {
        return $this->blockFactory->create(
            'Magento\Core\Test\Block\Messages',
            ['element' => $this->_rootElement->find($this->message)]
        );
    }
}
