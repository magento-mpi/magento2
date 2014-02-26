<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftMessage\Block\Message\Multishipping\Plugin;

use Magento\Code\Plugin\InvocationChain;

/**
 * Multishipping items box plugin
 */
class ItemsBox
{
    /**
     * Gift message helper
     *
     * @var \Magento\GiftMessage\Helper\Message
     */
    protected $helper;

    /**
     * Construct
     *
     * @param \Magento\GiftMessage\Helper\Message $helper
     */
    public function __construct(\Magento\GiftMessage\Helper\Message $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Get items box message text for multishipping
     *
     * @param array $arguments
     * @param InvocationChain $invocationChain
     * @return string
     */
    public function aroundGetItemsBoxTextAfter(array $arguments, InvocationChain $invocationChain)
    {
        $itemsBoxText = $invocationChain->proceed($arguments);
        return $itemsBoxText . $this->helper->getInline('multishipping_address', $arguments[0]);
    }
}
