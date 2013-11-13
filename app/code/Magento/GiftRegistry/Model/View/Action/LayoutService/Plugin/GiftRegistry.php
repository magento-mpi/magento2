<?php
/**
 * Plugin for layout service that assigns a flag to HTML head block signaling whether GiftRegistry is enabled or not
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model\View\Action\LayoutService\Plugin;

use Magento\View\LayoutInterface,
    Magento\App\ActionFlag;

class GiftRegistry
{
    /**
     * @var \Magento\GiftRegistry\Helper\Data
     */
    protected $_helper;

    /**
     * @var LayoutInterface
     */
    protected $_layout;

    /**
     * @var ActionFlag
     */
    protected $_flag;

    /**
     * @param \Magento\GiftRegistry\Helper\Data $helper
     * @param LayoutInterface $layout
     * @param ActionFlag $flag
     */
    public function __construct(
        \Magento\GiftRegistry\Helper\Data $helper,
        LayoutInterface $layout,
        ActionFlag $flag
    ) {
        $this->_helper = $helper;
        $this->_layout = $layout;
        $this->_flag = $flag;
    }

    /**
     * Assign a flag to HTML head block signaling whether GiftRegistry is enabled or not
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\View\Action\LayoutServiceInterface
     */
    public function aroundGenerateLayoutBlocks(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $layoutService = $invocationChain->proceed($arguments);

        if (!$this->_flag->get('', \Magento\App\Action\Action::FLAG_NO_DISPATCH_BLOCK_EVENT)) {
            $blockHead = $this->_layout->getBlock('head');
            if ($blockHead && $this->_helper->isEnabled()) {
                $blockHead->setData('giftregistry_enabled', true);
            }
        }

        return $layoutService;
    }
}
