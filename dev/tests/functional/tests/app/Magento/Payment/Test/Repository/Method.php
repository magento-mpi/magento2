<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Method Repository
 * Shipping methods
 *
 * @package Magento\Payment\Test\Repository
 */
class Method extends AbstractRepository
{
    /**
     * {inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['authorizenet'] = $this->_getAuthorizeNet();
        $this->_data['paypal_express'] = $this->_getPayPalExpress();
        $this->_data['paypal_direct'] = $this->_getPayPalDirect();
        $this->_data['paypal_payflow_pro'] = $this->_getPayPalPayflowPro();
        $this->_data['paypal_payflow_link_express'] = $this->_getPayPalPayflowLinkExpress();
        $this->_data['paypal_advanced'] = $this->_getPayPalAdvanced();
        $this->_data['check_money_order'] = $this->_getCheckMoneyOrder();
    }

    protected function _getAuthorizeNet()
    {
        return array(
            'config' => array(
                'payment_form_class' => '\\Magento\\Paygate\\Test\\Block\\Authorizenet\\Form\\Cc',
            ),
            'data' => array(
                'fields' => array(
                    'payment_code' => 'authorizenet'
                ),
            )
        );
    }

    protected function _getPayPalExpress()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'payment_code' => 'paypal_express'
                ),
            )
        );
    }

    protected function _getPayPalDirect()
    {
        return array(
            'config' => array(
                'payment_form_class' => '\\Magento\\Payment\\Test\\Block\\Form\\Cc',
            ),
            'data' => array(
                'fields' => array(
                    'payment_code' => 'paypal_direct'
                ),
            )
        );
    }

    /**
     * Provides Credit Card Data for PayPal Payflow Pro Method
     *
     * @return array
     */
    protected function _getPayPalPayflowPro()
    {
        return array(
            'config' => array(
                'payment_form_class' => '\\Magento\\Payment\\Test\\Block\\Form\\Cc',
            ),
            'data' => array(
                'fields' => array(
                    'payment_code' => 'verisign'
                ),
            )
        );
    }

    /**
     * Provides Credit Card Data for PayPal Payflow Link Express Method
     *
     * @return array
     */
    protected function _getPayPalPayflowLinkExpress()
    {
        return array(
            'config' => array(
                'payment_form_class' => '\\Magento\\Payment\\Test\\Block\\Form\\Cc',
            ),
            'data' => array(
                'fields' => array(
                    'payment_code' => 'paypaluk_express'
                ),
            )
        );
    }

    /**
     * Provides Credit Card Data for PayPal Payflow Pro Method
     *
     * @return array
     */
    protected function _getPayPalAdvanced()
    {
        return array(
            'config' => array(),
            'data' => array(
                'fields' => array(
                    'payment_code' => 'payflow_advanced'
                ),
            )
        );
    }

    /**
     * Provides Check money order data for the according payment method
     *
     * @return array
     */
    protected function _getCheckMoneyOrder()
    {
        return array(
            'data' => array(
                'fields' => array(
                    'payment_code' => 'checkmo'
                ),
            )
        );
    }
}
