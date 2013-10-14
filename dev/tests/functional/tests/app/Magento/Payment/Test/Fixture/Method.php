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

namespace Magento\Payment\Test\Fixture;

use Mtf\Fixture\DataFixture;

/**
 * Class Method
 * Shipping methods
 *
 * @package Magento\Payment\Test\Fixture
 */
class Method extends DataFixture
{
    /**
     * Get payment code
     *
     * @return string
     */
    public function getPaymentCode()
    {
        return $this->getData('fields/payment_code');
    }

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = array(
            'authorizenet' => array(
                'config' => array(
                    'payment_form_class' => '\\Magento\\Paygate\\Test\\Block\\Authorizenet\\Form\\Cc',
                ),
                'data' => array(
                    'fields' => array(
                        'payment_code' => 'authorizenet'
                    ),
                )
            ),
            'paypal_express' => array(
                'data' => array(
                    'fields' => array(
                        'payment_code' => 'paypal_express'
                    ),
                )
            ),
            'paypal_direct' => array(
                'config' => array(
                    'payment_form_class' => '\\Magento\\Payment\\Test\\Block\\Form\\Cc',
                ),
                'data' => array(
                    'fields' => array(
                        'payment_code' => 'paypal_direct'
                    ),
                )
            )
        );

        //Default data set
        $this->switchData('authorizenet');
    }
}
