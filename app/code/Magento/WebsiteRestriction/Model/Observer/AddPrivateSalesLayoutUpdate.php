<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\WebsiteRestriction\Model\Observer;

class AddPrivateSalesLayoutUpdate
{
    /**
     * @var \Magento\WebsiteRestriction\Model\ConfigInterface
     */
    protected $_config;

    /**
     * List of allowed mode
     *
     * @var int[]
     */
    protected $allowedTypes = [
        \Magento\WebsiteRestriction\Model\Mode::ALLOW_REGISTER,
        \Magento\WebsiteRestriction\Model\Mode::ALLOW_LOGIN
    ];

    /**
     * @param \Magento\WebsiteRestriction\Model\ConfigInterface $config
     */
    public function __construct(\Magento\WebsiteRestriction\Model\ConfigInterface $config)
    {
        $this->_config = $config;
    }

    /**
     * Make layout load additional handler when in private sales mode
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute($observer)
    {
        if (in_array($this->_config->getMode(), $this->allowedTypes, true)) {
            /** @var \Magento\Framework\View\LayoutInterface $layout */
            $layout = $observer->getEvent()->getLayout();
            $layout->getUpdate()->addHandle('restriction_privatesales_mode');
        }
    }
}
