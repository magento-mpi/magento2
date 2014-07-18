<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Controller\Express;

class Cancel extends \Magento\Paypal\Controller\Express\AbstractExpress\Cancel
{
    /**
     * Config mode type
     *
     * @var string
     */
    protected $_configType = 'Magento\Paypal\Model\Config';

    /**
     * Config method type
     *
     * @var string
     */
    protected $_configMethod = \Magento\Paypal\Model\Config::METHOD_WPP_EXPRESS;

    /**
     * Checkout mode type
     *
     * @var string
     */
    protected $_checkoutType = 'Magento\Paypal\Model\Express\Checkout';
}
