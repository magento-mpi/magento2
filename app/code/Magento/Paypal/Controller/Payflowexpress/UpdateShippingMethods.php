<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Paypal\Controller\Payflowexpress;

class UpdateShippingMethods extends \Magento\Paypal\Controller\Express\AbstractExpress\UpdateShippingMethods
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
    protected $_configMethod = \Magento\Paypal\Model\Config::METHOD_WPP_PE_EXPRESS;

    /**
     * Checkout mode type
     *
     * @var string
     */
    protected $_checkoutType = 'Magento\Paypal\Model\PayflowExpress\Checkout';
}
