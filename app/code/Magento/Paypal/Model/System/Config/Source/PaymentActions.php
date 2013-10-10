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
 * Source model for available payment actions
 */
namespace Magento\Paypal\Model\System\Config\Source;

class PaymentActions implements \Magento\Core\Model\Option\ArrayInterface
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
        return $this->_configFactory->create()->getPaymentActions();
    }
}
