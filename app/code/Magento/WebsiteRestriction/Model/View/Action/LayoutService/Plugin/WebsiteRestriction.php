<?php
/**
 * Plugin for layout service that modifies layout for private sales mode.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\WebsiteRestriction\Model\View\Action\LayoutService\Plugin;

use Magento\WebsiteRestriction\Model\ConfigInterface,
    Magento\View\LayoutInterface;

class WebsiteRestriction
{
    /**
     * @var ConfigInterface
     */
    protected $_config;

    /**
     * @var LayoutInterface
     */
    protected $_layout;

    /**
     * @param ConfigInterface $config
     * @param LayoutInterface $layout
     */
    public function __construct(
        ConfigInterface $config,
        LayoutInterface $layout
    ) {
        $this->_config = $config;
        $this->_layout = $layout;
    }

    /**
     * Add private sales layout update handle if needed
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\View\Action\LayoutServiceInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundLoadLayoutUpdates(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        if (in_array(
            $this->_config->getMode(),
            array(
                \Magento\WebsiteRestriction\Model\Mode::ALLOW_REGISTER,
                \Magento\WebsiteRestriction\Model\Mode::ALLOW_LOGIN
            ),
            true
        )) {
            $this->_layout->getUpdate()->addHandle('restriction_privatesales_mode');
        }

        return $invocationChain->proceed($arguments);
    }
}
