<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Express Checkout Controller
 */
namespace Magento\Paypal\Controller;

class Express extends \Magento\Paypal\Controller\Express\AbstractExpress
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

    /**
     * Redirect to login page
     *
     */
    public function redirectLogin()
    {
        $this->setFlag('', 'no-dispatch', true);
        \Mage::getSingleton('Magento\Customer\Model\Session')->setBeforeAuthUrl($this->_getRefererUrl());
        $this->getResponse()->setRedirect(
            $this->_objectManager->get('Magento\Core\Helper\Url')->addRequestParam(
                $this->_objectManager->get('Magento\Customer\Helper\Data')->getLoginUrl(),
                array('context' => 'checkout')
            )
        );
    }
}
