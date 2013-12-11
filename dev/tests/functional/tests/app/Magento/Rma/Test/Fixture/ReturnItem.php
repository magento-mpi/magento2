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

namespace Magento\Rma\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

/**
 * Fixture with all necessary data for creating a return item on the frontend
 *
 * @package Magento\Rma\Test\Fixture
 */
class ReturnItem extends DataFixture
{
    /**
     * Prepare search data for creating RMA
     *
     * @param \Magento\Sales\Test\Fixture\PaypalExpressOrder $fixture
     */
    public function prepareData(\Magento\Sales\Test\Fixture\PaypalExpressOrder $fixture)
    {
        $this->_data['fields']['order_id']['value'] = $fixture->getOrderId();
        //$this->_data['fields']['billing_last_name']['value'] = $fixture->getBillingAddress()->getLastName()['value'];
        $this->_data['fields']['billing_last_name']['value'] = $fixture->getBillingAddress()->getData('fields/lastname/value');
        //$this->_data['fields']['email_address']['value'] = $fixture->getCustomer()->getEmail()['value'];
        $this->_data['fields']['email_address']['value'] = $fixture->getCustomer()->getData('fields/login_email/value');

        // TODO:  Add products.
        //$this->_data['fields']['product_name_1']['value'] = $fixture->getProduct(0)->getProductName();
        //$this->_data['fields']['product_name_2']['value'] = $fixture->getProduct(1)->getProductName();
    }

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_data = array(
            'fields' => array(
                'order_id' => array(
                    'value' => ''
                ),
                'billing_last_name' => array(
                    'value' => ''
                ),
                'find_order_by' => array(
                    'value' => 'Email Address'
                ),
                'email_address' => array(
                    'value' => ''
                )
                /*'quantity' => array(
                    'value' => '1'
                ),
                'resolution' => array(
                    'value' => 'Refund'
                ),
                'condition' => array(
                    'value' => 'Opened'
                ),
                'reason' => array(
                    'value' => 'Wrong Size'
                )*/
            )
        );
    }

    /**
     * Get quantity to return
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->getData('fields/quantity');
    }

    /**
     * Get resolution of return
     *
     * @return string
     */
    public function getResolution()
    {
        return $this->getData('fields/resolution');
    }

    /**
     * Get condition of return
     *
     * @return string
     */
    public function getCondition()
    {
        return $this->getData('fields/condition');
    }

    /**
     * Get reason of return
     *
     * @return string
     */
    public function getReason()
    {
        return $this->getData('fields/reason');
    }
}
