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
use \Magento\Sales\Test\Fixture\PaypalExpressOrder;

/**
 * Fixture with all necessary data for creating a return item on the frontend
 *
 * @package Magento\Rma\Test\Fixture
 */
class ReturnItem extends DataFixture
{
    /**
     * Quantity
     *
     * @var int
     */
    protected $quantity;

    /**
     * Resolution
     *
     * @var string
     */
    protected $resolution;

    /**
     * Condition
     *
     * @var string
     */
    protected $condition;

    /**
     * Reason
     *
     * @var string
     */
    protected $reason;

    /**
     * Product Name
     *
     * @var string
     */
    protected $productName;

    /**
     * Prepare search data for creating RMA
     *
     * @param PaypalExpressOrder $fixture
     */
    public function prepareData($fixture)
    {
        $this->_data['fields']['order_id']['value'] = $fixture->getOrderId();
        $this->_data['fields']['billing_last_name']['value'] = $fixture->getBillingAddress()->getData('fields/lastname/value');
        $this->_data['fields']['email_address']['value'] = $fixture->getCustomer()->getData('fields/login_email/value');
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
        return $this->quantity;
    }

    /**
     * Get resolution of return
     *
     * @return string
     */
    public function getResolution()
    {
        return $this->resolution;
    }

    /**
     * Get condition of return
     *
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Get reason of return
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Get product name of return
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     *
     * Set quantity to return
     *
     * @return int
     */
    public function setQuantity($quantity)
    {
        return $this->quantity = $quantity;
    }

    /**
     * Set resolution of return
     *
     * @return string
     */
    public function setResolution($resolution)
    {
        return $this->resolution = $resolution;
    }

    /**
     * Set condition of return
     *
     * @return string
     */
    public function setCondition($condition)
    {
        return $this->condition = $condition;
    }

    /**
     * Set reason of return
     *
     * @return string
     */
    public function setReason($reason)
    {
        return $this->reason = $reason;
    }

    /**
     * Set product name of return
     *
     * @return string
     */
    public function setProductName($productName)
    {
        return $this->productName = $productName;
    }
}
