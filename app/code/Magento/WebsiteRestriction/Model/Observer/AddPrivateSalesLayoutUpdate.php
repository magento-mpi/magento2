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
        if (in_array(
            $this->_config->getMode(),
            array(
                \Magento\WebsiteRestriction\Model\Mode::ALLOW_REGISTER,
                \Magento\WebsiteRestriction\Model\Mode::ALLOW_LOGIN
            ),
            true
        )
        ) {
            $observer->getEvent()->getLayout()->getUpdate()->addHandle('restriction_privatesales_mode');
        }
    }
} 
