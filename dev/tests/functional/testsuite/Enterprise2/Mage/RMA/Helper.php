<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_RMA
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_RMA_Helper extends Mage_Selenium_TestCase
{
    /**
     * Fill form in Return page
     *
     * @param array $rmaData
     *
     */
    public function frontFillReturnForm(array $rmaData)
    {
        $param = 0;
        if (!empty($rmaData)) {
            foreach ($rmaData as $key => $rmaRequest) {
                $this->addParameter('param', $param++);
                $this->fillFieldset($rmaRequest, 'create_rma');
                unset($rmaData[$key]);
                if (!empty($rmaData)) {
                    $this->clickControl('link', 'add_item_to_return', false);
                    $this->waitForAjax();
                }
            }
        }
    }

    /**
     * Create RMA on frontend
     *
     * @param string $orderId
     * @param array $rmaData
     *
     */
    public function frontCreateRMA($orderId, array $rmaData)
    {
        if (empty($orderId)) {
            $this->fail("Order Id parameter is missing");
        }
        $this->addParameter('orderId', $orderId);
        $this->frontend('my_orders_history');
        $this->clickControl('link', 'view_order');
        $this->clickControl('link', 'return');
        $this->frontFillReturnForm($rmaData);
        $this->saveForm('submit');
    }

    /**
     * Create RMA on frontend as guest
     *
     * @param array $orderInfo
     * @param array $rmaData
     *
     */
    public function frontGuestCreateRMA(array $orderInfo, array $rmaData)
    {
        if (!isset($orderInfo['order_id'])) {
            $this->fail("Parameter order_id is not set");
        }
        $this->addParameter('orderId', $orderInfo['order_id']);
        $this->clickControl('link', 'orders_and_returns');
        $this->fillFieldset($orderInfo, 'orders_and_returns_form');
        $this->clickButton('continue');
        $this->clickControl('link', 'return');
        $this->frontFillReturnForm($rmaData);
        $this->saveForm('submit');
    }
}
