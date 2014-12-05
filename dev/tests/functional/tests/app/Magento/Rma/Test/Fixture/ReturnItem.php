<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;
use \Magento\Sales\Test\Fixture\PaypalExpressOrder;

/**
 * Fixture with all necessary data for creating a return item on the frontend
 */
class ReturnItem extends DataFixture
{
    /**
     * Product Names
     *
     * @var array
     */
    protected $productNames = array();

    /**
     * Prepare search data for creating RMA
     *
     * @param PaypalExpressOrder $fixture
     */
    public function prepareData($fixture)
    {
        $this->_data['fields']['order_id']['value'] = $fixture->getOrderId();
        $this->_data['fields']['billing_last_name']['value'] = $fixture->getBillingAddress()->getLastName();
        $this->_data['fields']['email_address']['value'] = $fixture->getCustomer()->getData('fields/login_email/value');
    }

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = Factory::getRepositoryFactory()
                ->getMagentoRmaReturnItem($this->_dataConfig, $this->_data);

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
        return $this->getData('fields/qty_requested');
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

    /**
     * Get product name of return
     *
     * @return array
     */
    public function getProductNames()
    {
        return $this->productNames;
    }

    /**
     * Add product names to return
     *
     * @param string
     */
    public function addProductName($productName)
    {
        array_unshift($this->productNames, $productName);
    }
}
