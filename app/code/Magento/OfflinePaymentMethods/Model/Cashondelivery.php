<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\OfflinePaymentMethods\Model;

/**
 * Cash on delivery payment method model
 */
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

    /**
     * Info instructions block path
     *
     * @var string
     */
    protected $_infoBlockType = 'Magento\Payment\Block\Info\Instructions';

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
