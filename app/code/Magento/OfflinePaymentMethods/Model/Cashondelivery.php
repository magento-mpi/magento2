<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cash on delivery payment method model
 */
namespace Magento\OfflinePaymentMethods\Model;

class Cashondelivery extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code  = 'cashondelivery';

    /**
     * Cash On Delivery payment block paths
     *
     * @var string
     */
    protected $_formBlockType = 'Magento\OfflinePaymentMethods\Block\Form\Cashondelivery';
    protected $_infoBlockType = 'Magento\OfflinePaymentMethods\Block\Info\Instructions';

    /**
     * Get instructions text from config
     *
     * @return string
     */
    public function getInstructions()
    {
        return trim($this->getConfigData('instructions'));
    }

}
