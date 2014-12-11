<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Reward\Test\Block;

use Mtf\Block\Block;

/**
 * Class Tooltip
 * Tooltip block to get different messages about reward points
 */
class Tooltip extends Block
{
    /**
     * Message CSS selector on page
     *
     * @var string
     */
    protected $messageSelector = '.reward-register > :first-child';

    /**
     * Message addition of reward points at registration
     *
     * @return string
     */
    public function getRewardMessages()
    {
        $message = '';
        $element = $this->_rootElement->find($this->messageSelector);
        if ($element->isVisible()) {
            $message = $element->getText();
        }
        return $message;
    }
}
