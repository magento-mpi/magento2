<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for available paypal express payment actions
 */
namespace Magento\Paypal\Model\System\Config\Source\PaymentActions;

class Express
    implements \Magento\Option\ArrayInterface
{
    /**
     * @var \Magento\Paypal\Model\ConfigFactory
     */
    protected $_configFactory;

    /**
     * @param \Magento\Paypal\Model\ConfigFactory $configFactory
     */
    public function __construct(\Magento\Paypal\Model\ConfigFactory $configFactory)
    {
        $this->_configFactory = $configFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var \Magento\Paypal\Model\Config $configModel */
        $configModel = $this->_configFactory->create();
        $configModel->setMethod(\Magento\Paypal\Model\Config::METHOD_WPP_EXPRESS);
        return $configModel->getPaymentActions();
    }
}
